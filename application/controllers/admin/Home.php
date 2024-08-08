<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->data['message'] = $this->session->flashdata('message');
	}

	public function index() {
		$this->template('home', $this->data);
	}

	public function email_config() {
		$this->TemplateModel->verify_access('email', 'view_data');
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
		$this->template('config/manage', $this->data);
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
		$this->template('templates/add_template', $this->data);
	}

	public function payment_config() {
		$this->TemplateModel->verify_access('payment_config', 'view_data');
		$this->form_validation->set_rules('razorpay_key_state', 'Payment Keys', 'required');
		$post_data = $this->input->post();
		if ($post_data) {
			if ($post_data['razorpay_key_state'] == 'live') {
				$this->form_validation->set_rules('razp_live_key_id', 'Razorpay Live Key ID', 'required');
				$this->form_validation->set_rules('razp_live_key_secret', 'Razorpay Live Key Secret', 'required');
				// $this->form_validation->set_rules('razp_live_wh_secret', 'Razorpay Live Webhook Secret', 'required');
			} else if ($post_data['razorpay_key_state'] == 'test') {
				$this->form_validation->set_rules('razp_test_key_id', 'Razorpay Test Key ID', 'required');
				$this->form_validation->set_rules('razp_test_key_secret', 'Razorpay Test Key Secret', 'required');
				// $this->form_validation->set_rules('razp_test_wh_secret', 'Razorpay Test Webhook Secret', 'required');
			}
		}
		$config_items = [
			'razorpay_key_state',
			'razp_live_key_id',
			'razp_live_key_secret',
			// 'razp_live_wh_secret',
			'razp_test_key_id',
			'razp_test_key_secret',
			// 'razp_test_wh_secret',
		];
		if ($this->form_validation->run()) {
			$status = $this->TemplateModel->set_config($config_items, $post_data);
			$alert = $status ? $this->TemplateModel->show_alert('suc', 'Successfully updated') : $this->TemplateModel->show_alert('err', 'Failed to update');
			$this->session->set_flashdata('message', $alert);
			redirect(base_url(uri_string()));
		}
		$this->data['config'] = $this->TemplateModel->get_config($config_items);
		$this->template('config/payment', $this->data);
	}

	public function get_create_table($config_name) {
		$this->TemplateModel->verify_admin();
		// header('Content-Type: application/json');
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
		$interface_props = [];
		if ($config->id == 'id') {
			$create_table_fields[] = "`{$config->id}` INT(11) NOT NULL AUTO_INCREMENT";
			$interface_props[] = "public int \${$config->id};";
		} else {
			$create_table_fields[] = "`{$config->id}` VARCHAR(50) NOT NULL";
			$interface_props[] = "public varchar \${$config->id};";
		}
		$unique_keys = [];
		$unique_key_fields = [];
		$mapping_tables = [];
		$interface_mapping_tables = [];
		foreach ($form_template as $key => $template) {
			if ($key == $config->id) continue;
			if ($template['type'] == "form-separator") continue;
			if ($template['type'] == "group-head") continue;
			if ($template['type'] == "custom") continue;
			$null = "NOT NULL";
			$maxlength = 250;
			$type = "VARCHAR($maxlength)";
			$interface_prop_type = "varchar";
			$interface_prop_nullable = "";
			$interface_prop_comment = "";
			$comment = "";
			if (($template['allow_null'] ?? false) == true) {
				$null = "NULL DEFAULT NULL";
				$interface_prop_nullable = "?";
			}
			if (array_key_exists('unique', $template)) {
				$unique_keys[] = $key;
			}
			if ($template['type'] == 'select-widget') {
				if ((($template['multiple']) ?? false) == false) {
					$type = 'INT(11)';
					$interface_prop_type = "int";
					if (($template['fixed_options'] ?? false) == true) {
						$options = array_column($template['options'], 'option_name', 'option_value');
						$options = array_filter($options, function ($val, $key) {
							return $key;
						}, ARRAY_FILTER_USE_BOTH);
						$type = "ENUM('" . implode("', '", array_keys($options)) . "')";
						$interface_prop_type = "enum";
						$comment = " COMMENT '";
						foreach ($options as $option_key => $option) {
							$comment .= $option_key . " - " . $option . ", ";
						}
						$comment = substr($comment, 0, -2) . "'";
						$interface_prop_comment = "/** @var enum `" . implode("`|`", array_keys($options)) . "` */ ";
					} else {
						$option_value = ($template['options'][1]['option_value'] ?? null);
						if (is_numeric($option_value)) {
							$type = 'INT(11)';
							$interface_prop_type = "int";
						} else {
							$type_length = ceil((strlen($option_value) + 5) / 10) * 10;
							$type = "VARCHAR($type_length)";
							$interface_prop_type = "varchar";
						}
					}
				} else {
					$map_key_name = $template['key'] ?? (singular($table_name) . '_id');
					$map_join_name = singular($template['name']);
					$map_table_name = $template['table'] ?? ($map_join_name . '_map');
					$field_type = 'INT(11)';
					$interface_prop_type = "int";
					if (($template['fixed_options'] ?? false) == true) {
						$field_type = 'VARCHAR(50)';
						$interface_prop_type = "varchar";
					}
					$mapping_tables[] = [
						'table' => $map_table_name,
						'fields' => [
							"`{$map_key_name}` INT(11) NOT NULL",
							"`{$map_join_name}_id` {$field_type} NOT NULL",
							"UNIQUE `{$map_key_name}_{$map_join_name}`(\n\t\t`{$map_key_name}`, \n\t\t`{$map_join_name}_id`\n\t)",
						],
					];
					$interface_mapping_tables[] = [
						'table' => $map_table_name,
						'fields' => [
							"public int \${$map_key_name};",
							"public {$interface_prop_type} \${$map_join_name}_id;"
						],
					];
					continue;
				}
			}
			// if (($template['validation'] ?? true) == false) continue;
			if ($template['type'] == 'radio') {
				$options = $template['options'];
				$type = "ENUM('" . implode("', '", array_keys($options)) . "')";
				$comment = " COMMENT '";
				foreach ($options as $option_key => $option) {
					$comment .= $option_key . " - " . $option . ", ";
				}
				$comment = substr($comment, 0, -2) . "'";
			}
			if ($template['type'] == 'textarea') {
				$type = 'VARCHAR(1000)';
				$interface_prop_type = "varchar";
			}
			if ($template['type'] == 'wysiwyg') {
				$type = 'TEXT';
				$interface_prop_type = "text";
			}
			if ($template['type'] == 'time') {
				$type = 'TIME';
				$interface_prop_type = "time";
			}
			if ((($template['class_list'] ?? null) == 'numeric') || ($template['type'] == 'number')) {
				$type = 'INT(11)';
				$interface_prop_type = "int";
				if (($template['attributes']['data-currency'] ?? false)) {
					$type = 'DECIMAL(10, 2)';
					$interface_prop_type = "float";
				}
				if (($template['attributes']['data-precision'] ?? false)) {
					$type = "DECIMAL(10, {$template['attributes']['data-precision']})";
					$interface_prop_type = "float";
				}
			}
			if ($template['type'] == 'date-widget') {
				$type = 'DATE';
				$interface_prop_type = "date";
			}
			if ($template['type'] == 'datetime-widget') {
				$type = 'DATETIME';
				$interface_prop_type = "datetime";
			}
			if ($template['type'] == 'input-table') {
				$map_key_name = $template['key'] ?? (singular($table_name) . "_id");
				$input_table_fields = $this->TemplateModel->{$template['fields']}();
				$mapping_interface_fields = [];
				$mapping_table_unique_key_fields = [];
				$mapping_table_fields = array_map(function ($field) use (&$mapping_interface_fields, &$mapping_table_unique_key_fields) {
					if ($field['ignore_field'] ?? false) return null;
					$field_type = "VARCHAR(250)";
					$field_type_null = "NOT NULL";
					$interface_prop_type = "varchar";
					$interface_prop_comment = "";
					if (strpos(($field['class_list'] ?? ""), "time-widget") !== false) {
						$field_type = "TIME";
						$interface_prop_type = "time";
					}
					if ($field['type'] == 'select-widget') {
						$field_type = 'INT(11)';
						$interface_prop_type = "int";
						if (($field['fixed_options'] ?? false) == true) {
							$options = $field['options'];
							$options = array_filter($options, function ($val, $key) {
								return $key;
							}, ARRAY_FILTER_USE_BOTH);
							$field_type = "ENUM('" . implode("', '", array_keys($options)) . "')";
							$interface_prop_type = "enum";
							$interface_prop_comment = "/** @var enum `" . implode("`|`", array_keys($options)) . "` */ ";
						}
					}
					if ((($field['class_list'] ?? null) == 'numeric') || ($field['type'] == 'number')) {
						$field_type = 'INT(11)';
						$interface_prop_type = "int";
						if (($field['attributes']['data-currency'] ?? false)) {
							$field_type = 'DECIMAL(10, 2)';
							$interface_prop_type = "float";
						}
						if (($field['attributes']['data-precision'] ?? false)) {
							$field_type = "DECIMAL(10, {$field['attributes']['data-precision']})";
							$interface_prop_type = "float";
						}
					}
					$mapping_interface_fields[] = "{$interface_prop_comment}public {$interface_prop_type} \${$field['name']};";
					if (($field['unique_key_field'] ?? false)) {
						$mapping_table_unique_key_fields[] = $field['name'];
					}
					return "`{$field['name']}` {$field_type} {$field_type_null}";
				}, $input_table_fields);
				$mapping_table_fields[] = "`{$map_key_name}` INT(11) NOT NULL";
				if (count($mapping_table_unique_key_fields) > 0) {
					$mapping_table_fields[] = "UNIQUE `{$template['name']}`(\n\t\t`{$map_key_name}`, \n\t\t`" . join("`, \n\t\t`", $mapping_table_unique_key_fields) . "`\n\t)";
				}
				$mapping_table_name = $template['table'] ?? ($template['name'] . '_map');
				$mapping_interface_fields[] = "public int \${$map_key_name};";
				$mapping_tables[] = [
					'table' => $mapping_table_name,
					'fields' => $mapping_table_fields,
				];
				$interface_mapping_tables[] = [
					'table' => $mapping_table_name,
					'fields' => $mapping_interface_fields,
				];
				continue;
			}
			if ($template['type'] == 'list') {
				$map_key_name = $template['key'] ?? (singular($table_name) . '_id');
				$map_join_name = singular($template['name']);
				$map_table_name = $template['table'] ?? ($map_join_name . '_map');
				$mapping_tables[] = [
					'table' => $map_table_name,
					'fields' => [
						"`{$map_key_name}` INT(11) NOT NULL",
						"`{$map_join_name}` VARCHAR(50) NOT NULL",
						"UNIQUE `{$map_key_name}_{$map_join_name}`(\n\t\t`{$map_key_name}`, \n\t\t`{$map_join_name}`\n\t)",
					],
				];
				$interface_mapping_tables[] = [
					'table' => $map_table_name,
					'fields' => [
						"public int \${$map_key_name};",
						"public varchar \${$template['name']};"
					],
				];
				continue;
			}
			if (($template['class_list'] ?? "") == "numeric") {
				$type = 'INT(11)';
				$interface_prop_type = "int";
				if ((($template['attributes']['data-currency']) ?? false) != false) {
					$type = 'DECIMAL(10, 2)';
					$interface_prop_type = "float";
				}
			}
			if (($template['attributes']['maxlength']) ?? false) {
				$type = "VARCHAR({$template['attributes']['maxlength']})";
				$interface_prop_type = "varchar";
			}
			$create_table_fields[] = "`{$key}` $type $null$comment";
			$interface_props[] = "{$interface_prop_comment}public {$interface_prop_nullable}{$interface_prop_type} \${$key};";
		}

		foreach ($unique_keys as $unique_key) {
			if ($unique_key == $config->id) continue;
			$unique_key_fields[] = "UNIQUE `{$unique_key}`(`{$unique_key}`)";
		}

		$view_template = $this->TemplateModel->{$config->view_template}();
		if (array_key_exists('sort', $view_template['links'])) {
			$create_table_fields[] = "`sort_order` INT(11) NULL DEFAULT NULL";
			$interface_props[] = "public int \$sort_order;";
		}

		$status_field = $config->status_field;
		if ($status_field) {
			$create_table_fields[] = "`{$status_field}` ENUM('1', '0') NOT NULL DEFAULT '1'";
			$interface_props[] = "/** @var enum `1`|`0` */ public enum \${$status_field};";
		}

		$create_table_fields[] = "`created_date` DATETIME NULL DEFAULT NULL";
		$interface_props[] = "public ?datetime \$created_date;";
		$create_table_fields[] = "`updated_date` DATETIME NULL DEFAULT NULL";
		$interface_props[] = "public ?datetime \$updated_date;";

		if ($config->id == 'id') {
			$create_table_fields[] = "PRIMARY KEY(`{$key}`)";
		}
		if (count($unique_key_fields) > 0) {
			$create_table_fields[] = implode(",\n\t", $unique_key_fields);
		}
		$create_query = "CREATE TABLE `{$table_name}`(\n\t";
		$create_query .= implode(",\n\t", $create_table_fields);
		$create_query .= "\n) ENGINE = INNODB;";
		$drop_query = "DROP TABLE `{$table_name}`;";
		// echo json_encode($create_table_fields);
		$mapping_table_create_query = "";
		$interface_body = "interface {$table_name} extends table {\n\t";
		$interface_body .= implode("\n\t", $interface_props);
		$interface_body .= "\n}";
		$mapping_interface = "";
		$mapping_table_drop_query = "";
		foreach ($mapping_tables as $m => $map) {
			$mapping_table_create_query .= "\n\nCREATE TABLE `{$map['table']}`(\n\t";
			$mapping_table_create_query .= implode(",\n\t", array_filter($map['fields']));
			$mapping_table_create_query .= "\n) ENGINE = INNODB;";
			$mapping_interface .= "\n\ninterface {$interface_mapping_tables[$m]['table']} extends table {\n\t";
			$mapping_interface .= implode("\n\t", $interface_mapping_tables[$m]['fields']);
			$mapping_interface .= "\n}";
			$mapping_table_drop_query .= "DROP TABLE `{$map['table']}`;";
		}
		$data['form_template'] = $form_template;
		$data['create_query'] = $create_query;
		$data['mapping_table_create_query'] = $mapping_table_create_query;
		$data['interface_body'] = $interface_body;
		$data['mapping_interface'] = $mapping_interface;
		$data['migration_up'] = array_filter([$create_query, trim($mapping_table_create_query)], 'strlen');
		$data['migration_down'] = array_filter([$mapping_table_drop_query, $drop_query], 'strlen');
		$data['mapping_interface'] = ($mapping_interface);
		$this->load->view('admin/setup/create', $data);
	}

	public function run_query() {
		$this->TemplateModel->verify_admin();
		$sql = $this->input->post('sql') ?? false;
		$referrer = $_SERVER['HTTP_REFERER'] ?? false;
		if (!$referrer || !$sql) {
			redirect_base(ADMIN_LOGIN_REDIRECT);
		}
		$this->db = @DB('migrator');
		$queries = explode("\r\n\r\n", $sql);
		foreach ($queries as $query) {
			$query = trim($query);
			if (empty($query)) continue;
			$this->db->query($query);
		}
		$this->db->close();
		redirect($referrer);
	}
}
