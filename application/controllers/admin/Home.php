<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->data['message'] = $this->session->flashdata('message');
	}

	public function index() {
		$this->load->template('home', $this->data);
	}

	public function email_config() {
		$this->TemplateModel->verify_access('email_config', 'view_data');
		$this->form_validation->set_rules('sendmail_mode', 'Sendmail Mode', 'required');
		$config_items = [
			'sendmail_mode',
			'alert_from_email_id',
			'alert_from_name',
			'alert_to_email_id',
			// 'alert_to_email_name',
			'sendinblue_api_key',
		];
		if ($this->form_validation->run()) {
			$post_data = $this->input->post();
			$status = $this->TemplateModel->set_config($config_items, $post_data);
			$alert = $status ? $this->TemplateModel->show_alert('suc', 'Successfully updated') : $this->TemplateModel->show_alert('err', 'Failed to update');
			$this->session->set_flashdata('message', $alert);
			redirect(base_url(uri_string()));
		}
		$this->data['config'] = $this->TemplateModel->get_config($config_items);
		$this->load->template('config/manage', $this->data);
	}

	public function mail_config_view() {
		$this->load->library('alerts');
		echo '<pre>';
		$mail_config = $this->alerts->mail_config();
		print_r($mail_config);
	}

	public function getMailAccount() {
		$this->load->library('Alerts');
		$data = $this->alerts->getAccount();
		echo '<pre>';
		print_r($data);
	}

	public function change_password() {
		$this->data['form_template'] = $this->TemplateModel->change_pw_form();
		$this->data['view_template'] = $this->TemplateModel->change_pw_view();
		$this->TemplateModel->set_validation($this->data['form_template']);
		if ($this->form_validation->run()) {
			$post_data = $this->input->post();
		}
		$this->load->template('templates/add_template', $this->data);
	}

	public function payment_config() {
		$this->TemplateModel->verify_access('payment_config', 'view_data');
		$this->form_validation->set_rules('payment_gateway', 'Payment Gateway', 'required');
		$this->form_validation->set_rules('payment_key_state', 'Active Payment Keys', 'required');
		$post_data = $this->input->post();
		if ($post_data) {
			if ($post_data['payment_gateway'] == 'razorpay') {
				if ($post_data['payment_key_state'] == 'live') {
					$this->form_validation->set_rules('razp_live_key_id', 'Razorpay Live Key ID', 'required');
					$this->form_validation->set_rules('razp_live_key_secret', 'Razorpay Live Key Secret', 'required');
					// $this->form_validation->set_rules('razp_live_wh_secret', 'Razorpay Live Webhook Secret', 'required');
				} else if ($post_data['payment_key_state'] == 'test') {
					$this->form_validation->set_rules('razp_test_key_id', 'Razorpay Test Key ID', 'required');
					$this->form_validation->set_rules('razp_test_key_secret', 'Razorpay Test Key Secret', 'required');
					// $this->form_validation->set_rules('razp_test_wh_secret', 'Razorpay Test Webhook Secret', 'required');
				}
			} else if ($post_data['payment_gateway'] == 'cashfree') {
				if ($post_data['payment_key_state'] == 'live') {
					$this->form_validation->set_rules('cf_live_key_id', 'Cashfree Live App ID', 'required');
					$this->form_validation->set_rules('cf_live_key_secret', 'Cashfree Live Key Secret', 'required');
				} else if ($post_data['payment_key_state'] == 'test') {
					$this->form_validation->set_rules('cf_test_key_id', 'Cashfree Test App ID', 'required');
					$this->form_validation->set_rules('cf_test_key_secret', 'Cashfree Test Key Secret', 'required');
				}
			}
		}
		$config_items = [
			'payment_gateway',
			'payment_key_state',
			'razp_live_key_id',
			'razp_live_key_secret',
			'razp_live_wh_secret',
			'razp_test_key_id',
			'razp_test_key_secret',
			'razp_test_wh_secret',
			'cf_live_key_id',
			'cf_live_key_secret',
			'cf_test_key_id',
			'cf_test_key_secret',
		];
		if ($this->form_validation->run()) {
			foreach ($config_items as $config) {
				$set_config = [
					'config_key' => $config,
					'config_value' => $post_data[$config],
					'config_date' => date(date_time_format),
					'config_user' => $this->session->userdata('user')['id'],
				];
				$this->db->replace('admin_config', $set_config);
			}
			redirect(base_url(uri_string()));
		}
		$this->data['config'] = $this->TemplateModel->get_config($config_items);
		$this->load->template('config/payment', $this->data);
	}
}
