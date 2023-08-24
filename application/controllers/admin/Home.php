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

	public function profile() {
		$this->data['form_template'] = $this->TemplateModel->profile_form();
		$this->data['view_template'] = $this->TemplateModel->profile_view();
		$this->TemplateModel->verify_access('profile', 'view_data');
		$this->TemplateModel->set_validation($this->data['form_template']);
		$config_items = [
			'profile_business_name',
			'profile_address',
			'profile_city',
			'profile_phone',
			'profile_email_id',
			'profile_website_url',
			PROFILE_LOGO_FIELD,
			'profile_gst',
		];
		if ($this->form_validation->run()) {
			$post_data = $this->input->post();
			$old_application_file = $post_data[PROFILE_LOGO_FIELD . '_edit'];
			$post_data[PROFILE_LOGO_FIELD] = $old_application_file;
			$application_file = $this->TemplateModel->save_image(PROFILE_LOGO_FIELD, PROFILE_LOGO_UPLOAD_PATH, null, null, $old_application_file);
			if ($application_file) $post_data[PROFILE_LOGO_FIELD] = $application_file;
			$status = $this->TemplateModel->set_config($config_items, $post_data);
			$alert = $status ? $this->TemplateModel->show_alert('suc', 'Successfully updated') : $this->TemplateModel->show_alert('err', 'Failed to update');
			$this->session->set_flashdata('message', $alert);
			redirect(base_url(uri_string()));
		}
		$this->data['edit'] = $this->TemplateModel->get_config($config_items);
		$this->load->template('templates/add_template', $this->data);
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
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['form_template'] = $this->TemplateModel->change_pw_form();
		$this->data['view_template'] = $this->TemplateModel->change_pw_view();
		$this->TemplateModel->set_validation($this->data['form_template']);
		$post_data = $this->input->post();
		if ($this->form_validation->run()) {
			$this->load->library('encription_utility');
			$login_id = $this->session->userdata('user')['id'];
			$enc_old_password = $this->encription_utility->getSaltPassword($post_data['old_password']);
			$user = $this->db->get_where('users', ['id' => $login_id, 'login_password' => $enc_old_password, 'user_status' => '1'], 1)->row_array();
			$old_password_valid = $user ? true : false;
			if (!$old_password_valid) {
				$this->session->set_flashdata('message', $this->TemplateModel->show_alert('err', 'Invalid Old Password'));
			} else {
				$login_password = $this->encription_utility->getSaltPassword($post_data['new_password']);
				$status = $this->TemplateModel->change_password($login_id, $login_password);
				$alert = $status ? $this->TemplateModel->show_alert('suc', 'Successfully updated') : $this->TemplateModel->show_alert('err', 'Failed to update');
				$this->session->set_flashdata('message', $alert);
			}
			redirect(ad_base_url('home/change_password'));
		}
		$this->data['edit'] = $post_data;
		$this->data['edit']['username'] = $this->session->userdata('user')['user_mobile'];
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

	public function get_create_table($config_name) {
		header('Content-Type: application/json');
		if (!property_exists($this->TemplateModel, "{$config_name}_config")) {
			echo 'No config';
			return;
		}
		/** @var TemplateConfig */
		$config = $this->TemplateModel->{"{$config_name}_config"};
		if (!method_exists($this->TemplateModel, $config->form_template)) {
			echo 'No form template';
			return;
		}
		$form_template = $this->TemplateModel->{$config->form_template}();
		$form_template = array_column($form_template, null, 'name');
		$table_name = $config->table;
		$create_table_fields = [];
		if ($config->id == 'id') {
			$create_table_fields[] = "`{$config->id}` INT(11) NOT NULL AUTO_INCREMENT";
		} else {
			$create_table_fields[] = "`{$config->id}` VARCHAR(50) NOT NULL";
		}
		$unique_keys = [];
		$unique_key_fields = [];
		$mapping_tables = [];
		foreach ($form_template as $key => $template) {
			if ($key == $config->id) continue;
			if ($template['type'] == "form-separator") continue;
			if ($template['type'] == "group-head") continue;
			if ($template['type'] == "custom") continue;
			$null = "NOT NULL";
			$maxlength = 250;
			$type = "VARCHAR($maxlength)";
			if (($template['allow_null'] ?? false) == true) {
				$null = "NULL DEFAULT NULL";
			}
			if (array_key_exists('unique', $template)) {
				$unique_keys[] = $key;
			}
			if ($template['type'] == 'select-widget') {
				if ((($template['multiple']) ?? false) == false) {
					$type = 'INT(11)';
					$option_value = $template['options'][1]['option_value'];
					if (is_numeric($option_value)) {
						$type = 'INT(11)';
					} else {
						$type_length = ceil((strlen($option_value) + 5) / 10) * 10;
						$type = "VARCHAR($type_length)";
					}
				} else {
					$map_key_name = singular($table_name);
					$mapping_tables[] = [
						'table' => $template['name'],
						'fields' => [
							"`$map_key_name` INT(11) NOT NULL",
							"`{$template['name']}` INT(11) NOT NULL",
							"UNIQUE `{$template['name']}`(\n\t\t`{$map_key_name}`, \n\t\t`{$template['name']}`\n\t)",
						],
					];
					continue;
				}
			}
			// if (($template['validation'] ?? true) == false) continue;
			if ($template['type'] == 'textarea') {
				$type = 'VARCHAR(1000)';
			}
			if ($template['type'] == 'time') {
				$type = 'TIME';
			}
			if ($template['type'] == 'date-widget') {
				$type = 'DATE';
			}
			if ($template['type'] == 'datetime-widget') {
				$type = 'DATETIME';
			}
			if ($template['type'] == 'input-table') {
				$map_key_name = singular($table_name);
				$input_table_fields = $this->TemplateModel->{$template['fields']}();
				$mapping_table_fields = array_map(function ($field) {
					$field_type = "VARCHAR(250)";
					$field_type_null = "NOT NULL";
					if (strpos(($field['class_list'] ?? ""), "time-widget") !== false) {
						$field_type = "TIME";
					}
					return "`{$field['name']}` {$field_type} {$field_type_null}";
				}, $input_table_fields);
				$mapping_table_fields[] = "`{$map_key_name}` INT(11) NOT NULL";
				$mapping_table_fields[] = "UNIQUE `{$template['name']}`(\n\t\t`{$map_key_name}`, \n\t\t`" . join("`, \n\t\t`", array_column($input_table_fields, 'name')) . "`\n\t)";
				$mapping_tables[] = [
					'table' => $template['name'],
					'fields' => $mapping_table_fields,
				];
				continue;
			}
			if (($template['class_list'] ?? "") == "numeric") {
				$type = 'INT(11)';
				if ((($template['attributes']['data-currency']) ?? false) != false) {
					$type = 'DECIMAL(10, 2)';
				}
			}
			if (($template['attributes']['maxlength']) ?? false) {
				$type = "VARCHAR({$template['attributes']['maxlength']})";
			}
			$create_table_fields[] = "`{$key}` $type $null";
		}

		foreach ($unique_keys as $unique_key) {
			if ($unique_key == $config->id) continue;
			$unique_key_fields[] = "UNIQUE `{$unique_key}`(`{$unique_key}`)";
		}

		$view_template = $this->TemplateModel->{$config->view_template}();
		if (array_key_exists('sort', $view_template['links'])) {
			$create_table_fields[] = "`sort_order` INT(11) NULL DEFAULT NULL";
		}

		$status_field = $config->status_field;
		if ($status_field) {
			$create_table_fields[] = "`{$status_field}` ENUM('1', '0') NOT NULL DEFAULT '1'";
		}

		$create_table_fields[] = "`created_date` DATETIME NULL DEFAULT NULL";
		$create_table_fields[] = "`updated_date` DATETIME NULL DEFAULT NULL";

		if ($config->id == 'id') {
			$create_table_fields[] = "PRIMARY KEY(`{$key}`)";
		}
		if (count($unique_key_fields) > 0) {
			$create_table_fields[] = implode(",\n\t", $unique_key_fields);
		}
		$create_query = "CREATE TABLE `{$table_name}`(\n\t";
		$create_query .= implode(",\n\t", $create_table_fields);
		$create_query .= "\n) ENGINE = INNODB;";
		// echo json_encode($create_table_fields);
		$mapping_table_create_query = "";
		foreach ($mapping_tables as $map) {
			$mapping_table_create_query .= "\nCREATE TABLE `{$map['table']}_map`(\n\t";
			$mapping_table_create_query .= implode(",\n\t", $map['fields']);
			$mapping_table_create_query .= "\n) ENGINE = INNODB;";
		}
		echo $create_query;
		echo $mapping_table_create_query;
	}
}
