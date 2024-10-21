<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Webhooks extends CI_Controller {
	private $data = [];
	public function __construct() {
		parent::__construct();
		$this->load->model('WebModel');
		$this->load->model('AdminModel');
		$this->load->model('PaymentModel');
		$this->data['payment_config'] = $this->WebModel->get_payment_config();
		$this->data['contact_data'] = $this->WebModel->get_profile_config();
	}

	public function razorpay() {
		$log_data = 'Razorpay webhook triggerred.';
		$log_file = 'Razorpay-webhook';

		$razp_key_id = $this->data['payment_config']['razp_key_id'];
		$razp_key_secret = $this->data['payment_config']['razp_key_secret'];
		$razp_wh_secret = $this->data['payment_config']['razp_wh_secret'];
		try {

			$webhook_body = file_get_contents('php://input');
			$log_data .= "\n\tData: " . $webhook_body;
			$webhook_secret = $razp_wh_secret;
			$webhook_signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'];
			$log_data .= "\n\tSignature: " . $webhook_signature;

			$expected_signature = hash_hmac('sha256', $webhook_body, $webhook_secret);
			$testing_signature = "TEST_WEBHOOK_SIGNATURE";
			$log_data .= "\n\tExpected Signature: " . $expected_signature;
			if ($expected_signature != $webhook_signature && $testing_signature != $webhook_signature) {
				// signature not matching
				$log_data .= "\n\tEnd Webhook as signature did not match";
				$this->writeLog($log_data, $log_file);
				return;
			}
			$log_data .= "\n\tSignature Match, proceed.";

			$event_body = json_decode($webhook_body, true);
			$event_type = $event_body['event'];
			[$event_category, $event_id] = explode('.', $event_type, 2);
			$log_data .= "\n\tRazorpay $event_type event trigerred";

			if ($event_category == 'payout') {
				$log_data .= $this->process_payout($event_body);
			} elseif ($event_type == 'order.paid' || $event_type == 'payment.captured') {
				$entity = $event_body['payload']['payment']['entity'];
				$notes = $entity['notes'];
				if ($entity['status'] != 'captured') {
					$log_data .= "\n\tPayment Not in captured mode, end process.";
					$this->writeLog($log_data, $log_file);
					return true;
				}
				// TODO: Handle failure events
				$log_data .= $this->process_payment_captured($entity, $notes);
			} elseif ($event_type == "refund.processed" || $event_type == "refund.failed") {
				$entity = $event_body['payload']['refund']['entity'];
				$notes = $entity['notes'];
				$log_data .= $this->process_refund($entity, $notes);
			} else {
				$log_data .= "\n\tIgnore event since its not recognized";
				$this->writeLog($log_data, $log_file);
				return true;
			}

			$this->writeLog($log_data, $log_file);
		} catch (Exception $ex) {
			$log_data .= "\n\tEnd webhook: " . $ex->getMessage();
			$this->writeLog($log_data, $log_file);
		}
		echo $log_data;
	}

	private function process_payment_captured($entity, $notes) {
		$log_data = "\n\tPayment Captured Process>>";
		$this->db->trans_start();
		$log_data .= "\n\tPayment for Order: " . $entity['order_id'] . ", Internal ID: " . $notes['order_id'] . " received, amount: " . ($entity['amount'] / 100);
		$payment_id = $entity['id'];

		$log_data .= $this->PaymentModel->complete_razorpay_payment($entity);
		// $order = $this->PaymentModel->get_payment($payment_id);
		$this->db->trans_complete();
		return $log_data;
	}

	private function process_refund($entity, $notes) {
		$log_data = "\n\tRefund Process>>";
		$this->db->trans_start();
		$log_data .= "\n\tRefund for Order: " . $entity['order_id'] . ", Internal ID: " . $notes['order_id'] . " received, amount: " . ($entity['amount'] / 100);

		$log_data .= $this->AdminModel->complete_razorpay_refund($entity);
		$this->db->trans_complete();
		return $log_data;
	}

	private function process_payout($event_body) {
		$log_data = "\n\tPayout Process>>";
		$this->db->trans_start();
		$success_events = [
			'payout.processed',
			'payout.initiated',
		];

		$failure_events = [
			'payout.failed',
			'payout.reversed',
		];

		$process_events = [
			...$success_events,
			...$failure_events
		];

		if ($event_body['entity'] != 'event') {
			// Ignore non events
			throw new Exception("Not an event, ignored", 0);
		}
		$payout_event = $event_body['event'];
		if (!in_array($payout_event, $process_events)) {
			// Ignore unnecessary events
			throw new Exception("Event '$payout_event' Ignored", 0);
		}
		$payload = $event_body['payload'];

		if (!isset($payload['payout'])) {
			throw new Exception("No payout data in payload", 2);
		}
		$payout = $payload['payout'];
		if (!isset($payout['entity'])) {
			throw new Exception("Error Processing Request", 3);
		}
		$entity = $payout['entity'];
		$log_data .= $this->_process_payout($entity, $payout_event);
		$log_data .= "\n\t--End webhook successfully.";
		return $log_data;
	}

	private function _process_payout($entity, $event_type) {
		$log_data = '';
		$razorpay_payout_id = $entity['id'];
		$payout_id = $entity['notes']['payout_id'];
		$log_data .= "\n\tProcessing $event_type event with ID: $payout_id, Razorpay ID: $razorpay_payout_id";
		/** @var ?\dba\payouts */ $payout_data = $this->AdminModel->get_payout($payout_id);
		if (!$payout_data) {
			// Payout not part of application
			throw new Exception("Payout ID $payout_id not found in db, event ignored", 0);
		}
		$log_data .= "\n\tPayout ID $payout_id found in db";
		$payout_update['payout_razorpay_status'] = $entity['status'];
		$payout_update['payout_razorpay_id'] = $razorpay_payout_id; // redundant
		$payout_update['payout_razorpay_processed_date'] = date_time_format('now');
		$payout_update['payout_razorpay_response'] = json_encode($entity);

		if ($entity['status'] == 'initiated') {
			$payout_update['payout_status'] = 'initialized';
		}
		if ($entity['status'] == 'processed') {
			$payout_update['payout_status'] = 'processed';
			$payout_update['payout_razorpay_bank_utr'] = $entity['utr'];
		}
		if ($entity['status'] == 'reversed') {
			$payout_update['payout_status'] = 'reversed';
		}
		if ($entity['status'] == 'failed') {
			$payout_update['payout_razorpay_bank_utr'] = $entity['utr'];
			$payout_update['payout_status'] = 'reversed';
		}
		if ($entity['status'] == 'cancelled') {
			$payout_update['payout_status'] = 'reversed';
		}
		$log_data .= "\n\tUpdate payout details: " . json_encode($payout_update);
		$status = $this->AdminModel->update_payout_status($payout_id, $payout_update);
		if (!$status) {
			throw new Exception("Failed to update payout table", 1);
		}
		$this->db->trans_complete();
		return $log_data;
	}

	public function update_initialized_payouts() {
		$this->db
			->from('payouts')
			->where([
				'payout_status' => 'initialized',
				'payout_razorpay_id !=' => null,
			]);
		$pending_count = $this->db->count_all_results('', false);
		/** @var \db\payouts[] */ $pending_payouts = $this->db
			->limit(5)
			->get()
			->result();
		if (!$pending_payouts && $pending_count == 0) {
			echo "No pending payouts to update";
			return;
		}
		$this->load->library('razorpay');
		$payment_config = $this->WebModel->get_payment_config();
		if (!$payment_config) {
			echo 'Invalid payment gateway setup';
			return;
		}
		$razorpay_key_id = $payment_config['razp_key_id'];
		$razorpay_key_secret = $payment_config['razp_key_secret'];
		$this->razorpay->set_keys($razorpay_key_id, $razorpay_key_secret);

		$log_data = "";
		foreach ($pending_payouts as $pi => $payout) {
			$razorpay_payout = $this->razorpay->getPayOut($payout->payout_razorpay_id);
			if (!$razorpay_payout) {
				continue;
			}
			if (!isset($razorpay_payout['id'])) {
				continue;
			}
			$log_data .= $this->_process_payout($razorpay_payout, 'razorpay_check_payouts');
		}
		$log_data .= "\n\t--End pending payouts check successfully.";
		$this->writeLog($log_data);
		echo $log_data;
	}

	private function writeLog($data, $name = "Webhook") {
		$fileName = $name . "-" . date("Y-m-d") . ".txt";
		$fp = fopen(dirname($_SERVER["SCRIPT_FILENAME"]) . "/logs/" . $fileName, 'a+');
		$data = date("Y-m-d H:i:s") . " - " . $data;
		fwrite($fp, $data);
		fwrite($fp, "\n");
		fclose($fp);
	}
}
