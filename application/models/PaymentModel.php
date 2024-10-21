<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PaymentModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	public function initiate_order($event_id, $booking, $summary) {
		$this->db->trans_start();
		$create_order = $this->create_order($event_id, $booking, $summary);

		try {
			$razorpay_order = $this->initiate_order_razorpay($create_order);
			$this->db->trans_complete();
			$update = $razorpay_order['status'];
			$error = $razorpay_order['error'];
			$data = $razorpay_order['data'];
			return ['status' => $update, 'error' => $error, 'data' => $data];
		} catch (Exception $ex) {
			$this->db->trans_rollback();
			return ['status' => false, 'error' => $ex->getMessage()];
		}
	}

	/**
	 * @param \dba\bookings $create_order
	 * @return array
	 */
	public function initiate_order_razorpay($create_order) {
		$customer = $create_order->customer;
		$payment_config = $this->WebModel->get_payment_config();
		$profile_config = $this->WebModel->get_profile_config();
		if (!$payment_config) {
			// return (['status' => false, 'error' => 'Invalid payment gateway setup.']);
			throw new Exception('Invalid payment gateway setup.');
		}
		$razorpay_key_id = $payment_config['razp_key_id'];
		$razorpay_key_secret = $payment_config['razp_key_secret'];
		// $razorpay_wh_secret = $payment_config['razp_wh_secret'];
		$this->load->library('razorpay');
		$this->razorpay->set_keys($razorpay_key_id, $razorpay_key_secret);
		$order_notes = [
			// Max 15 fields
			'order_id' => $create_order->ticket_id,
			'order_amount' => $create_order->order_total,
			'customer_id' => $customer->customer_id,
			'customer_name' => $customer->customer_name,
			// Custom fields
		];
		$receipt = $order_notes['order_id'] . '-' . date('yzhis');
		$order_data = [
			'amount' => ($order_notes['order_amount']) * 100,
			'order_id' => $order_notes['order_id'],
			'notes' => $order_notes,
			'receipt' => $receipt,
		];
		// die(json_encode($order_data));
		$razorpay_order = $this->razorpay->create_order($order_data);
		if ($razorpay_order['status'] != 'created') {
			// return (['status' => false, 'error' => 'Failed to create razorpay order']);
			throw new Exception('Failed to create razorpay order', 1);
		}
		$order_update = [
			'pay_start_datetime' => date_time_format('now'),
			'pay_order_id' => $razorpay_order['id'],
		];
		$update = $this->db->update('', $order_update, ['' => $create_order->], 1);
		if (!$update) {
			throw new Exception('Failed to save razorpay order', 1);
		}

		$data['razorpay_order_id'] = $razorpay_order['id'];
		$data['key_id'] = $this->razorpay->get_keyid();
		$data['price'] = $order_data['amount'];
		$data['order_id'] = $order_data['order_id'];
		$data['sub_name'] = $profile_config['company_name'];
		$data['store_name'] = $profile_config['company_name'];
		$data['logo'] = base_url(PROFILE_LOGO_UPLOAD_PATH . $profile_config[PROFILE_LOGO_FIELD]);
		$data['url'] = base_url('razorpay_status');
		$data['mobile_number'] = $customer->customer_phone;
		$data['name'] = $customer->customer_name;
		$data['color'] = "#ff5866";

		return ['status' => true, 'error' => '', 'data' => $data];
	}

	/**
	 * @param int $customer_id
	 * @param array $order_data
	 *
	 * @return \dba\bookings
	 * @throws Exception
	 */
	public function create_order($event_id, $booking, $summary) {
		$log_data = 'Initiate Create Order.';
		$log_file = 'orders';

		try {
			$this->db->trans_start();
			/** @var ?\db\customers */ $customer = $this->WebModel->get_customer($booking['customer']);
			if (!$customer) {
				$log_data .= 'Invalid customer.';
				throw new Exception($log_data, 1);
				return false;
			}
			$ = $this->WebModel->();
			$new_order= [
				'customer_id' => $customer->customer_id,
				'' => $,
				'order_date' => date_time_format('now'),
				'order_status' => 'pending',
			];

			$save = $this->db->insert('', $new_order);
			if (!$save) {
				$this->db->trans_rollback();
				$db_error = $this->db->error();
				$log_data .= "\n\tOrder not created. Error: " . json_encode($db_error);
				throw new Exception($log_data, 1);
				return false;
			}
			$= $this->db->insert_id();
			/** @var ?\dba\ */ $save_= $this->WebModel->get_($);

			if (!$save_) {
				$db_error = $this->db->error();
				$log_data .= "\n\tOrder not created. Error: " . json_encode($db_error);
				throw new Exception($log_data, 1);
				return false;
			}

			$log_file .= "/{$}";

			$this->db->trans_complete();
			if ($this->db->trans_status() === false) {
				$db_error = $this->db->error();
				$log_data .= "\n\tErrors: " . json_encode($db_error);
				throw new Exception($log_data, 1);
				return false;
			}
			$log_data .= "\n\tComplete Order Creation. Order ID: " . $new_order[''];
			$log_data .= "\n\tOrder Data: " . json_encode($new_order, JSON_PRETTY_PRINT);
			$this->writeLog($log_data, $log_file);
			return $save_booking;
		} catch (Exception $e) {
			$log_data .= "\n\tEnd Order Creation. Error: " . $e->getMessage();
			$this->writeLog($log_data, $log_file);
			return false;
		}
	}

	/**
	 * @param string $payment_id
	 * @return ?\db\bookings
	 */
	public function get_payment($payment_id) {
		return $this->db
			->where(['pay_order_id' => $payment_id])
			->or_where(['pay_id' => $payment_id])
			->get("", 1)
			->row();
	}

	public function complete_razorpay_payment($event_body) {
		$entity = $event_body;
		if ($event_body['entity'] == "event") {
			$entity = $event_body['payload']['payment']['entity'];
		}
		$notes = $entity['notes'];
		$payment_id = $entity['id'];
		$post = [
			'pay_id' => $payment_id,
			'pay_response' => json_encode($event_body),
			'pay_status' => $entity['status'],
		];
		return $this->complete_payment($post, $notes);
	}

	public function complete_payment($post, $notes) {
		$log_data = "\n\tPayment Data Received : " . json_encode($post);
		$order_table = "";

		$this->db->where('', $notes['order_id']);
		$update_order = $this->db->update($order_table, $post);
		if (!$update_order) {
			$error = $this->db->error();
			throw new Exception("Order ID : " . $notes['order_id'] . " not updated. Error : " . $error['message'] . "", 1);
		}

		$update['order_status'] = 'complete';
		$update['pay_complete_date'] = date_time_format('now');

		$pending_order_where = [
			'ticket_id' => $notes['order_id'],
			// 'payment_status' => 'pending'
		];
		$this->db->trans_start();
		/** @var \db\ */ $pending_order = $this->db->get_where($order_table, $pending_order_where)->row();
		if (!$pending_order) {
			throw new Exception("Order ID : " . $notes['order_id'] . " not found", 1);
		}
		if ($pending_order->order_status == 'pending') {
			$this->db->update($order_table, $update, $pending_order_where);
			$log_data .= "\n\tOrder No completed: {$notes['order_id']}";

			// Email Order Confirmation
			$mail_sent = $this->WebModel->confirm_order_mail($notes['order_id']);
			if (!$mail_sent) {
				throw new Exception("Order ID : " . $notes['order_id'] . " mail not sent", 1);
			}
		} else {
			throw new Exception("Order ID : " . $notes['order_id'] . " already confirmed", 1);
		}
		$this->db->trans_complete();

		return $log_data;
	}

	private function writeLog($data, $name) {
		$fileName = $name . "-" . date("Y-m-d") . ".txt";
		$log_file_path = dirname($_SERVER["SCRIPT_FILENAME"]) . "/logs/" . $fileName;
		$log_dir = dirname($log_file_path);
		if (!is_dir($log_dir)) {
			mkdir($log_dir, 0777, true);
		}
		$fp = fopen($log_file_path, 'a+');
		$data = date("Y-m-d H:i:s") . " - " . $data;
		fwrite($fp, $data);
		fwrite($fp, "\n");
		fclose($fp);
	}
}
