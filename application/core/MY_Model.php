<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model {
	public $db_setup = true;
	public $template_errors = [];
	public function __construct() {
		parent::__construct();
		$this->form_validation->set_error_delimiters('<label class="error">', '</label>');
	}

	public function verify_admin() {
		$login = !$this->db_setup;
		if (null != ($this->session->userdata('user'))) {
			$user_data = $this->session->userdata('user');
			if (!$this->db_setup) {
				$user_data = [
					'user_type' => 'admin',
					'display_name' => 'Admin',
					'id' => '0',
				];
			}
			$user_type = $user_data['user_type'];
			$access = [
				'user_name' => $user_data['display_name'],
				'user_id' => $user_data['id'],
				'user_type' => $user_data['user_type'],
			];
			if ($user_type == 'admin') {
				$navs = $this->TemplateModel->get_user_access_navs();
				foreach ($navs as $pages) {
					foreach ($pages as $page) {
						/** @var \TemplateConfig */ $config = $page['config'];
						$access['page_access'][$config->access] = [
							'page' => $config->access,
							'view_data' => '1',
							'add_data' => '1',
							'edit_data' => '1',
							'block_data' => '1',
							'delete_data' => '1',
						];
					}
				}
			} elseif ($user_type == "type") {
				$access['page_access'] = $this->get_type_access();
				$access['type_id'] = $user_data['user_id'];
			} else {
				$access['page_access'] = array_column($this->get_user_access_map($user_data), null, 'page');
			}
			$login = true;
		}
		if (!$this->db_setup) {
			$user_data = [
				'user_type' => 'admin',
				'display_name' => 'Admin',
				'id' => '0',
			];
			$access = [
				'user_name' => $user_data['display_name'],
				'user_id' => $user_data['id'],
				'user_type' => $user_data['user_type'],
			];
			$navs = $this->TemplateModel->get_user_access_navs();
			foreach ($navs as $pages) {
				foreach ($pages as $page) {
					$access['page_access'][$page['config']->access] = [
						'page' => $page['config']->access,
						'view_data' => '1',
						'add_data' => '1',
						'edit_data' => '1',
						'block_data' => '1',
						'delete_data' => '1',
					];
				}
			}
		}
		if (!$login) {
			redirect_base(ADMIN_LOGIN_PATH);
		}
		return $access;
	}

	public function verify_access($access_page, $access_type, $redirect = true) {
		if (!$this->db_setup) {
			return true;
		}
		$access_verified = false;
		$user = $this->session->userdata('user');
		if ($user) {
			$username = $user['user_mobile'];
			$login_id = $user['id'];
			$login_type = $user['user_type'];
		}
		if ($user && $login_type == 'admin') {
			$access_verified = $this->get_user_access()[$access_page][$access_type] ?? '0';
		}
		if ($user && $login_type == 'type') {
			$access_verified = $this->get_type_access()[$access_page][$access_type] ?? '0';
		}
		if ($user && $login_type == 'user') {
			$access_data = $this->db->get_where('user_access_map', ['user' => $login_id, 'page' => $access_page], 1)->row_array();
			$access_verified = $access_data[$access_type] ?? '0' == '1';
		}
		if ($access_verified) {
			return true;
		}
		if (!$redirect) {
			return false;
		}
		$this->session->set_flashdata('message', $this->show_alert('err', 'You do not have access to the page/action'));
		$redirect_url = $_SERVER['HTTP_REFERER'] ?? base_url(ADMIN_PATH);
		$current_url = current_url();
		if ($redirect_url == $current_url) {
			$redirect_url = base_url(ADMIN_PATH);
		}
		redirect($redirect_url);
	}

	public function get_user_access_map($edit_data) {
		if (!$edit_data) return [];
		if (!isset($edit_data['id'])) return [];

		$user_id = $edit_data['id'];
		$page_access = $this->db->get_where('user_access_map', ['user' => $user_id])->result_array();
		return $page_access;
	}

	public function get_user_access() {
		$type_navs = $this->TemplateModel->get_user_access_navs();
		return $this->get_navs_access($type_navs);
	}

	public function get_type_access() {
		$type_navs = $this->TemplateModel->get_type_access_navs();
		return $this->get_navs_access($type_navs);
	}

	public function get_navs_access($type_navs) {
		$access = [];
		foreach ($type_navs as $pages) {
			foreach ($pages as $page) {
				$options = $page['options'];
				$page_access = [];
				if (in_array('v', $options)) {
					$page_access['view_data'] = '1';
				}
				if (in_array('a', $options)) {
					$page_access['add_data'] = '1';
				}
				if (in_array('e', $options)) {
					$page_access['edit_data'] = '1';
				}
				if (in_array('b', $options)) {
					$page_access['block_data'] = '1';
				}
				if (in_array('d', $options)) {
					$page_access['delete_data'] = '1';
				}
				$access[$page['config']->access] = $page_access;
			}
		}
		return $access;
	}

	public function change_pw_form() {
		return [
			['type' => 'hidden', 'label' => '', 'name' => 'username'],
			['type' => 'password', 'label' => 'Old Password', 'name' => 'old_password', 'required' => true, 'attributes' => ['autocomplete' => 'off']],
			['type' => 'password', 'label' => 'New Password', 'name' => 'new_password', 'required' => true, 'attributes' => ['autocomplete' => 'new-password']],
			['type' => 'password', 'label' => 'Confirm Password', 'name' => 'confirm_password', 'required' => true, 'attributes' => ['autocomplete' => 'confirm-password'], 'rules' => ['matches[new_password]']],
		];
	}

	public function change_pw_view() {
		return [
			'head' => 'Change Password',
			'links' => [],
		];
	}

	public function change_password($login_id, $new_password) {
		return $this->db->update('users', ['login_password' => $new_password], ['id' => $login_id]);
	}

	public function save_user_account($user_data) {
		$user = $this->db->get_where('users', ['user_id' => $user_data['user_id'], 'user_type' => $user_data['user_type']])->row_array();
		$save_user = false;
		if ($user) {
			$user_data['updated_date'] = date(date_time_format);
			$save_user = $this->db->update('users', $user_data, ['id' => $user['id']]);
		} else {
			$this->load->library('encription_utility');
			$pw = $this->encription_utility->getSaltPassword('password');
			$user_data['login_password'] = $pw;
			$user_data['created_date'] = date(date_time_format);
			$save_user = $this->db->insert('users', $user_data);
		}
		return $save_user;
	}

	// Example
	private function example_view() {
		return [
			'head' => 'Page Header',
			'links' => [
				'add' => 'website/add_master',
				'view' => 'website/view_master',
			],
			'form_action' => ad_base_url('website/submit_master'),
			'form_ajax' => true,
		];
	}

	private function example_table() {
		return [
			'heads' => ['Sl. no', 'Name', 'Image', 'Action'],
			'src' => 'ajax',
			'data' => 'ajaxtables/example',
		];
	}

	private function example_add() {
		$common_properties = [
			['type', 'required'],
			['name', 'required'],
			['label', 'required'],
			['unique' => ['table' => 'table name', 'key' => 'primary/unique key', 'composite' => 'parent ids (optional)'], 'optional'],
			['help_text', 'optional'],
			['readonly', 'optional'],
			['required', 'optional'],
			['validation', 'optional'],
			['class_list', 'optional'],
			['no_edit', 'optional'],
			['attributes', 'optional'],
			['allow_null', 'optional'], // When required is false
			// class_list [numeric, alphanumeric, alphabetic]
			// attributes [data-currency, 'data-visibility-name' => 'field', 'data-visibility-value' => 'field_value'] data-currency must have numeric in class_list and type input
		];
		return  [
			// Custom props: []
			['type' => 'input', 'label' => 'Text', 'name' => 'text'],
			['type' => 'email', 'label' => 'Email', 'name' => 'email'],
			['type' => 'number', 'label' => 'Number', 'name' => 'number'],
			['type' => 'time', 'label' => 'Time Widget', 'name' => 'time'],
			['type' => 'textarea', 'label' => 'Textarea', 'name' => 'text', 'attributes' => ['maxlength' => 150]],
			// Custom props: [add-min-date => date, add-max-date => date]
			['type' => 'date-widget', 'label' => 'Date Widget', 'name' => 'date'],
			// Custom props: [multiple => bool, update => field name, change => ajax fn, options => select_options array, add_options => [master, label]]
			['type' => 'select-widget', 'label' => 'Select Widget', 'name' => 'select'],
			// Custom props: [size => [x, y], accept => [...filetypes], path => upload dir]
			['type' => 'image', 'label' => 'Image', 'name' => 'img', 'path' => 'path', 'required' => true, 'size' => [1600, 700], 'accept' => ['png', 'jpeg', 'webp']],
			['type' => 'wysiwyg', 'label' => 'WYSIWYG - Styled Textarea Widget', 'name' => ''],
			// Custom props: [on_state, off_state]
			['type' => 'checkbox', 'label' => 'Bootstrap Switch Checkbox', 'name' => ''],
			// Custom props: [options => select_options]
			['type' => 'radio', 'label' => 'Radio', 'name' => ''],
			// Custom props: [multiple]
			['type' => 'file', 'label' => 'File Input', 'name' => ''],
			// Custom props: [multiple, prepend_text]
			['type' => 'list', 'label' => 'List Input', 'name' => ''],
			// Custom props: [footer => view file, table-inline => bool] ['table' => 'map_table_name', 'key' => 'FK in input-table fields', 'edit_key' => 'PK of this form']
			['type' => 'input-table', 'label' => 'Input Table', 'name' => '', 'fields' => 'input_template_fn'],
			// Custom props: []
			['type' => 'tags', 'label' => 'Tags Input', 'name' => ''],
			// Custom props: []
			['type' => 'color', 'label' => 'Color', 'name' => 'color'],
			// Key field is required
			['type' => 'key', 'label' => 'ID', 'name' => 'id'],
		];
	}

	private function input_template_fn() {
		return [
			['type' => 'select-widget', 'label' => 'Table Select', 'name' => 'table_select', 'options' => []],
			['type' => 'form_input type: text/number/email/textarea', 'label' => 'Table Input', 'name' => 'table_input', 'attributes' => ['size' => 5]],
		];
	}

	private function example_config_add() {
		$city_options_config = [
			'parent' => $this->state_config,
			'options' => $this->city_config,
		];
		$city_options = $this->select_parent_options($city_options_config, true);
	}

	//======================================================================
	// Generals
	//======================================================================

	public function set_validation($data) {
		foreach ($data  as $field) {
			$rules = $field['rules'] ?? [];
			$field_name = $field['name'];
			if (isset($field['required']) && $field['required'] == true && $field['type'] != 'image' && $field['type'] != 'file') {
				$rules[] = 'required';
			}
			if ($field['type'] == 'image') {
				$rules[] = 'is_image';
			}
			if (isset($field['unique'])) {
				$unique = $field['unique'];
				$table = $unique['table'];
				$key = $unique['key'];
				$edit = $this->input->post($key) ?? "";
				$composite = $unique['composite'] ?? "";
				if ($composite != "") {
					$key = "$composite>>$key";
				}
				$rules[] = "edit_unique[$table.$field_name.$key.$edit]";
			}
			if (isset($field['multiple']) && $field['multiple'] == true) {
				$field_name .= '[]';
			}
			if (count($rules) > 0) {
				$this->form_validation->set_rules($field_name, $field['label'], join('|', $rules));
			}
		}
	}

	/**
	 * @param string $table Table name
	 * @param string $option_value Option Key
	 * @param string $option_name Option Name Optional
	 * @param string $filter Select Conditions Optional
	 * @param string $filter_in WHERE IN Conditions Optional
	 * @param string $select First Option as Select
	 */
	public function select_options($table, $option_value, $option_name = '', $filter = [], $filter_in = [], $select = true, $order = '') {
		$select_0 = [
			'option_name' => 'Select',
			'option_value' => '',
		];
		if (!$this->db_setup) {
			return [$select_0];
		}
		if ($option_name == '') {
			$option_name = $option_value;
		}
		if ($filter != []) {
			$this->db->where($filter);
		}
		if ($filter_in != [] && $filter_in[1] != []) {
			$this->db->where_in($filter_in[0], $filter_in[1]);
		}
		if ($order != '') {
			$this->db->order_by($order);
		} else {
			$this->db->order_by('option_name', 'ASC');
		}
		$result = $this->db
			->select($option_name . ' as option_name, ' . $option_value . ' as option_value')
			->get($table)
			->result_array();

		if ($select) {
			array_unshift($result, $select_0);
		}

		return $result;
	}

	public function select_category_options($select = true, $table = "categories", $option_name = "category_name", $option_value = "id", $parent_field = "parent_category") {
		$category_nested_names = [];
		for ($cat_i = CATEGORY_VIEW_DEPTH; $cat_i > 0; $cat_i--) {
			$category_nested_names[] = "cp$cat_i.$option_name";
		}
		$this->db
			->from("$table cp1");
		for ($cat_i = 2; $cat_i <= CATEGORY_VIEW_DEPTH; $cat_i++) {
			$cat_ip = $cat_i - 1;
			$this->db
				->join("$table cp$cat_i", "cp$cat_i.$option_value = cp$cat_ip.$parent_field", 'LEFT');
		}
		$result = $this->db
			->select('CONCAT_WS(" > ", ' . join(', ', $category_nested_names) . ") as option_name, cp1.$option_value as option_value")
			->order_by('option_name', 'ASC')
			->get()
			->result_array();

		if ($select) {
			array_unshift($result, [
				'option_name' => 'Select',
				'option_value' => '',
			]);
		}
		return $result;
	}

	/**
	 * select_parent_options
	 *
	 * @param array $relation
	 * $relation = [
	 * 	'options'	=> 	TemplateConfig,
	 * 	'parent'	=> 	TemplateConfig
	 * ]
	 * @param bool $select
	 *
	 * @return array
	 */
	public function select_parent_options($relation, $select = true, $parent_name_select = null, $option_name_select = null, $option_separator = ' > ') {
		$table = $relation['options']->table;
		$field_name = $option_name_select ?? "t1.{$relation['options']->display_name}";
		$field_id = $relation['options']->id;
		$parent_key = $relation['options']->parent_field;
		$parent_table = $relation['parent']->table;
		$parent_name = $parent_name_select ?? "t2.{$relation['parent']->display_name}";
		$parent_id = $relation['parent']->id;
		$result = $this->db
			->select("CONCAT_WS('$option_separator', {$parent_name}, {$field_name}) as option_name, t1.{$field_id} as option_value")
			->from("{$table} t1")
			->join("{$parent_table} t2", "t2.{$parent_id} = t1.{$parent_key}", 'LEFT')
			->order_by('option_name', 'ASC')
			->get()
			->result_array();
		if ($select) {
			array_unshift($result, [
				'option_name' => 'Select',
				'option_value' => '',
			]);
		}
		return $result;
	}

	public function select_options_array($options, $field1, $field2, $type = 'S') {
		$opt_array = array();
		if ($type == 'S')
			$opt_array = array('' => 'Select');
		foreach ($options as $value) {
			$opt_array[$value[$field1]] = $value[$field2];
		}
		return $opt_array;
	}

	public function get_edit_row($table, $edit = '', $key = 'id', $return_type = 'array') {
		if (!$this->db_setup) {
			return [];
		}
		if ($edit == '') {
			$edit = $this->input->get_post('edit');
		}
		return $this->db->where($key, $edit)->get($table)->row(0, $return_type);
	}

	public function get_edit_blank($table) {
		if (!$this->db_setup) {
			return [];
		}
		$fields = $this->db->list_fields($table);
		foreach ($fields as $ki => $key) {
			$fields[$key] = '';
			unset($fields[$ki]);
		}
		return $fields;
	}

	public function get_edit_map($table, $key, $value = '') {
		if (!$this->db_setup) {
			return [];
		}

		if ($key !== null) {
			$this->db->where($key, $value);
		}
		return $this->db->get($table)->result_array();
	}

	public function save_table_data($table, $post_data, $template, $key) {
		if (!$this->db_setup) {
			return 1;
		}
		$post = [];
		foreach ($template as $field) {
			if ((isset($field['validation']) && $field['validation'] == false)) {
				continue;
			}
			$field_name = $field['name'];
			if (($field['type'] == 'image' || $field['type'] == 'image-list' || $field['type'] == 'file' || $field['type'] == 'input-table' || $field['type'] == 'list' || empty($field_name)) && !isset($post_data[$field_name])) {
				continue;
			}
			$field_data = $post_data[$field_name];
			if ($field['type'] == 'date-widget') {
				$field_data = date(date_format, strtotime($post_data[$field_name]));
			}
			if ($field['type'] == 'datetime-widget') {
				$field_data = date(date_time_format, strtotime($post_data[$field_name]));
			}
			if ($field['type'] == 'time') {
				$field_data = date(time_format, strtotime($post_data[$field_name]));
			}
			if ($field['type'] == 'wysiwyg') {
				$field_data = remove_html_comments($post_data[$field_name]);
			}
			if (isset($field['allow_null']) && $field['allow_null'] == true && $post_data[$field_name] == '') {
				$field_data = null;
			}
			$post[$field_name] = $field_data;
		}
		$rows = $this->db->where([$key => $post[$key]])->get($table)->num_rows();
		if ($post_data[$key] != '' && $rows != 0) {
			$post[$key] = $post_data[$key];
			$post['updated_date'] = date(date_time_format);
			$save = $this->db->update($table, $post, [$key => $post[$key]]);
			if ($save) {
				return $post[$key];
			} else {
				return false;
			}
		} else {
			$post['created_date'] = date(date_time_format);
			$save = $this->db->insert($table, $post);
			if ($save) {
				if (($post[$key] ?? "") != "") {
					return $post[$key];
				}
				$insert_id = $this->db->insert_id();
				if ($insert_id) {
					return $insert_id;
				}
				return 1;
			} else {
				return false;
			}
		}
	}

	public function get_array_options($array_options, $select = true) {
		$options = array_map(function ($key, $value) {
			return ['option_value' => $key, 'option_name' => $value];
		}, array_keys($array_options), $array_options);
		if ($select) {
			array_unshift($options, ['option_value' => '', 'option_name' => 'Select']);
		}
		return $options;
	}

	/**
	 * save_table_data_multiple
	 *
	 * @param mixed $table
	 * @param mixed $table_data
	 * @param mixed $template
	 * @param mixed $key
	 *
	 * @return array insert ID/Key
	 */
	public function save_table_data_multiple($table, $table_data, $template, $key, $select = 0) {
		$this->form_validation->set_error_delimiters('<label class="error mt-0">', '</label>');
		$id = [];
		$errors = [];
		foreach ($table_data as $post_data) {
			$_POST = $post_data;
			$this->set_validation($template);
			if ($this->form_validation->run()) {
				$id[] = $this->save_table_data($table, $post_data, $template, $key);
			} else {
				if ($select === 0) {
					$errors[] = $post_data[array_keys($post_data)[0]] . '<br>' . validation_errors('');
				} else {
					$errors[] = $post_data[$select] . '<br>' . validation_errors('');
				}
			}
			$this->form_validation->reset_validation();
		}
		return ['ids' => $id, 'errors' => $errors];
	}

	public function save_image($field, $path = 'assets/uploads', $accept = null, $max_size = null, $unlink_image = null) {
		if (isset($_FILES[$field]) && $_FILES[$field]['name'] != '') {
			if (!file_exists($path)) {
				mkdir($path, 0777, true);
			}
			if ($accept == null) {
				$accept = 'jpg|png|jpeg|webp';
			}
			if ($max_size == null) {
				$max_size = getMaximumFileUploadSizeKB();
			}
			$this->load->library('upload');
			$config['upload_path'] = dirname($_SERVER["SCRIPT_FILENAME"]) . '/' . $path . '/';
			$config['allowed_types'] = $accept;
			$config['max_size'] = $max_size;
			$config['encrypt_name'] = true;
			$this->upload->initialize($config);
			// echo $_FILES[$field]['name'];
			$upload_status = false;
			if (is_array($_FILES[$field]['name'])) {
				$upload_status = $this->upload->do_upload($field . '[]');
			} else {
				$upload_status = $this->upload->do_upload($field);
			}
			if ($upload_status) {
				// if ($image != '') {
				// 	$this->generals_func->fileDelete($config['upload_path'], $image);
				// }
				$data = $this->upload->data();
				// echo json_encode($data);
				$image = $data['file_name'];
				if ($unlink_image) {
					if (file_exists($path . '/' . $unlink_image)) {
						unlink($path . '/' . $unlink_image);
					}
				}
				return $image;
			} else {
				// $data['message'] = $this->generals_func->show_alert('err', 'Error!' . $this->upload->display_errors());
				// echo $field;
				// echo json_encode($this->upload->display_errors('', ''));
			}
		}
		// echo json_encode($_FILES[$field]['name']);
		return false;
	}

	public function save_files($field, $path = 'assets/uploads', $accept = null, $max_size = null, $preserve_index = false, $db_images = null, $old_images = null) {
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		if ($accept == null) {
			$accept = 'jpg|png|jpeg|webp';
		}
		if ($max_size == null) {
			$max_size = getMaximumFileUploadSizeKB();
		}
		$config['upload_path'] = dirname($_SERVER["SCRIPT_FILENAME"]) . '/' . $path;
		$config['allowed_types'] = $accept;
		$config['max_size'] = $max_size;
		$config['encrypt_name'] = true;

		$this->load->library('upload', $config);

		$files = $_FILES;
		$images = [];
		$delete_images = [];
		foreach ($files[$field]['name'] as $key => $image) {
			$_FILES[$field]['name'] = $files[$field]['name'][$key];
			$_FILES[$field]['type'] = $files[$field]['type'][$key];
			$_FILES[$field]['tmp_name'] = $files[$field]['tmp_name'][$key];
			$_FILES[$field]['error'] = $files[$field]['error'][$key];
			$_FILES[$field]['size'] = $files[$field]['size'][$key];

			$this->upload->initialize($config);

			$data = null;
			$upload_status = $this->upload->do_upload($field);
			$data = $this->upload->data();
			if ($upload_status) {
				$upload_image = ['status' => true, 'image' => $data['file_name']];
				if ($old_images[$key] ?? null) {
					$delete_images[] = $old_images[$key];
				}
			} else {
				if ($preserve_index && ($old_images[$key] ?? null) != null) {
					$upload_image = ['status' => true, 'image' => $old_images[$key]];
				} else {
					$unlink_image = $db_images[$key] ?? null;
					$upload_image = ['status' => false, 'image' => $unlink_image, 'error' => $this->upload->display_errors('', ''),];
				}
			}
			if ($preserve_index == true) {
				$images[$key] = $upload_image;
			} else {
				$images[] = $upload_image;
			}
		}
		foreach ($delete_images as $delete_image) {
			if (file_exists($path . $delete_image)) {
				unlink($path . $delete_image);
			}
		}

		return $images;
	}

	public function save_table_map($table, $foreign_key, $foreign_value, $fields, $formatting = null, $extras = []) {
		if (!$this->db_setup) {
			return [];
		}
		$post_map = [];
		$field_rows = $this->input->post($fields[0]) ?? [];
		foreach ($field_rows as $ii => $name) {
			if ($foreign_key !== null) {
				$post_row = [
					$foreign_key => $foreign_value,
				];
			}
			foreach ($fields as $fi => $field_name) {
				if ($field_name == "sort_order") {
					$sort_order_key = $extras['sort_key'] ?? "";
					if ($sort_order_key == "") {
						continue;
					}
					$post_row[$field_name] = $this->input->post($field_name)[$sort_order_key][$ii];
				} else {
					$post_row[$field_name] = $this->input->post($field_name)[$ii];
				}
				if (isset($formatting[$fi]) && $formatting[$fi] != "") {
					$post_row[$field_name] = call_user_func($formatting[$fi], $post_row[$field_name]);
				}
			}
			$post_map[] = $post_row;
		}

		if ($foreign_key === null) {
			$foreign_key = 1;
			$foreign_value = 1;
		}
		$this->db->delete($table, [$foreign_key => $foreign_value]);

		if (count($post_map) > 0) {
			return $this->db->insert_batch($table, $post_map);
		}
		return 0;
	}

	public function save_table_map_manual($table, $foreign_key, $foreign_value, $data) {
		$post_map = $data;
		$this->db->delete($table, [$foreign_key => $foreign_value]);

		if (count($post_map) > 0) {
			$this->db->insert_batch($table, $post_map);
		}
	}

	public function get_data_rows($table, $select = '*', $where = [], $order_by = null, $limit = null, $offset = null) {
		if (!$this->db_setup) {
			return [];
		}
		if ($order_by) {
			$this->db->order_by($order_by);
		}
		if ($limit) {
			$this->db->limit($limit);
		}
		if ($offset) {
			$this->db->offset($offset);
		}
		if ($select == null) {
			$select = '*';
		}
		return $this->db->select($select)->get_where($table, $where)->result_array();
	}

	public function edit_unique($value, $params) {
		$this->form_validation->set_message('edit_unique', "%s Must be unique.");

		list($table, $field, $current_id) = explode(".", $params);

		$query = $this->db->select()->from($table)->where($field, $value)->limit(1)->get();

		if ($query->row() && $query->row()->id != $current_id) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function show_alert($type, $message) {
		$alert_class = 'alert-info';
		switch ($type) {
			case 'suc':
				$alert_class = 'alert-success';
				break;
			case 'err':
				$alert_class = 'alert-danger';
				break;
			case 'warn':
				$alert_class = 'alert-warning';
				break;
			case 'info':
				$alert_class = 'alert-info';
				break;
			default:
				$alert_class = 'alert-primary';
				break;
		}

		$alert = '<div data-notify="container" class="float-alert alert ' . $alert_class . '" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button><span data-notify="title"></span><span data-notify="message">' . $message . '</span></div>';
		return $alert;
	}

	/**
	 * Assign text data to foreign key values in table data list
	 *
	 * @param mixed $table_data
	 * @param mixed $map_data
	 * * `table` - ref table
	 * * `key` - primary key of ref table
	 * * `field` - search text of ref table
	 * * `name` - foriegn key of main table
	 *
	 * @return void
	 */
	public function excel_data_to_db_map($table_data, $map_data) {
		$db_data = [];
		if (count($map_data) == 0) {
			return $table_data;
		}
		foreach ($table_data as $tri => $row) {
			$di = 0;
			foreach ($map_data as $mdi => $map) {
				if (!$this->db_setup) {
					continue;
				}
				$col_id = $this->db
					->select($map['key'])
					->limit(1)
					->get_where($map['table'], [$map['field'] => $row[$map['name']]])
					->row_array();
				if ($col_id) {
					$table_data[$tri][$mdi] = $row[$map['name']];
					$table_data[$tri][$map['name']] = $col_id[$map['key']];
					$di = 1;
				}
			}
			if ($di == 1) {
				$db_data[] = $table_data[$tri];
			}
		}
		return $db_data;
	}

	public function submit_import($master, $master_name = "") {
		if ($master_name == "") {
			$master_name = $master;
		}
		$import_template = $this->{$master . "_import"}();
		$form_template = $this->{$master . "_add"}();
		$table_heads = $import_template['table_heads'];
		$map_data = $import_template['map_data'] ?? [];
		$table = $import_template['table'];
		$table_data = @Excel_export::importExcelFile('upload_file', true, $table_heads);
		$table_data = $this->excel_data_to_db_map($table_data, $map_data);
		$save_table = $this->save_table_data_multiple($table, $table_data, $form_template, 'id');
		$ids = $save_table['ids'];
		$errors = $save_table['errors'];
		$alert = count($ids) > 0 ? 'suc' : 'err';
		$message = $this->show_alert($alert, count($ids) . " $master_name added");
		foreach ($errors as $error) {
			$message .= $this->show_alert('err', $error);
		}
		return $message;
	}

	public function get_config($config_key = null) {
		if (!$this->db_setup) {
			return [];
		}
		if ($config_key) {
			$this->db->where_in('config_key', $config_key);
		}
		$config = array_column($this->db->get('admin_config')->result_array(), 'config_value', 'config_key');
		if ($config_key != null && !is_array($config_key)) {
			return $config[$config_key] ?? null;
		}
		return $config;
	}

	public function set_config($config_items, $post_data) {
		$status = false;
		foreach ($config_items as $config) {
			$set_config = [
				'config_key' => $config,
				'config_value' => remove_html_comments($post_data[$config]),
				'config_date' => date(date_time_format),
				'config_user' => $this->session->userdata('user')['id'] ?? "",
			];
			$status = $this->db->replace('admin_config', $set_config);
		}
		return $status;
	}

	/**
	 * get_export
	 *
	 * @param TemplateConfig $config
	 * @param array $table_heads
	 *
	 * @return array
	 */
	public function get_export($config, $table_heads, $filter_data) {
		$joins = $this->TemplateModel->{$config->table_template}()['joins'] ?? [];
		$table_alias = $this->TemplateModel->{$config->table_template}()['table_alias'] ?? "a";
		$view_filters = $this->TemplateModel->{$config->view_template}()['filter'] ?? [];
		$sort_order = $this->TemplateModel->{$config->table_template}()['sort_order'] ?? "$table_alias.{$config->id} DESC";
		$where = $this->TemplateModel->{$config->table_template}()['where'] ?? null;
		foreach ($view_filters as &$filter) {
			$filter_name = $filter['name'];
			$filter_value = $filter_data[$filter_name] ?? "";
			$filter_name = str_replace("--", ".", $filter_name);
			if ($filter_value == '') continue;
			if ($filter['type'] == 'date') {
				$filter_value = date(date_format, strtotime($filter_value));
				$filter_date_type = $filter['date_type'];
				$filter_name = explode("-", $filter_name)[0];
				$filter_name = "DATE({$filter_name}) {$filter_date_type}";
			}
			$this->db->where($filter_name, $filter_value);
		}
		$this->db->order_by($sort_order);
		$this->db
			->select(array_keys($table_heads))
			->from("{$config->table} {$table_alias}");

		if ($where) {
			$this->db->where($where);
		}

		foreach ($joins as $join) {
			$join_table = $join['table'];
			$join_alias = $join['alias'];
			$join_condition = $join['condition'];
			$join_type = $join['type'] ?? "";
			$this->db->join("$join_table $join_alias", $join_condition, $join_type);
		}
		$results = $this->db->get()->result_array();
		$table_heads_q = [];
		foreach ($table_heads as $thead => $tlabel) {
			$thead_ex = preg_split('/ as /i', $thead);
			$thead_ex = explode('.', trim(end($thead_ex)));
			$table_heads_q[trim(end($thead_ex))] = $tlabel;
		}
		return [$table_heads_q, $results];
	}

	public function get_array_case_select($options, $field) {
		$select = "CASE $field";
		foreach ($options as $key => $value) {
			$select .= " WHEN '$key' THEN '$value'";
		}
		$select .= "ELSE $field END AS {$field}_case";
		return $select;
	}
}

class TemplateConfig {
	public $access = '';
	public $form_template = '';
	public $table_template = '';
	public $view_template = '';
	public $table = '';
	public $id = '';
	public $display_name = '';
	public $status_field = '';
	public $parent_field = null;

	public function __construct(
		$access,
		$form_template,
		$table_template,
		$view_template,
		$table,
		$id = "",
		$display_name = "",
		$status_field = "",
		$parent_field = null,
		$def = ""
	) {
		$this->access = $access;
		$this->form_template = $form_template;
		$this->table_template = $table_template;
		$this->view_template = $view_template;
		$this->table = $table;
		$this->id = $id;
		$this->display_name = $display_name;
		$this->status_field = $status_field;
		$this->parent_field = $parent_field;
	}

	public function get_options($filter = [], $select = true, $order = null) {
		/** @var CI */
		$ci = &get_instance();
		$option_value = strpos($this->id, '.') ? $this->id : "table.{$this->id}";
		$option_name = strpos($this->display_name, '.') ? $this->display_name : "table.{$this->display_name}";
		if ($this->status_field) {
			$filter[$this->status_field] = '1';
		}
		return $ci->TemplateModel->select_options("$this->table table", $option_value, $option_name, $filter, null, $select, $order);
	}

	public function get_row($row_id, $return_type = "array") {
		/** @var CI */
		$ci = &get_instance();
		return $ci->TemplateModel->get_edit_row($this->table, $row_id, $this->id, $return_type);
	}
}

// https://www.php.net/manual/en/function.array-multisort.php
function array_msort($array, $cols) {
	$colarr = array();
	foreach ($cols as $col => $order) {
		$colarr[$col] = array();
		foreach ($array as $k => $row) {
			$colarr[$col]['_' . $k] = strtolower($row[$col]);
		}
	}
	$eval = 'array_multisort(';
	foreach ($cols as $col => $order) {
		$eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
	}
	$eval = substr($eval, 0, -1) . ');';
	eval($eval);
	$ret = array();
	foreach ($colarr as $col => $arr) {
		foreach ($arr as $k => $v) {
			$k = substr($k, 1);
			if (!isset($ret[$k])) $ret[$k] = $array[$k];
			$ret[$k][$col] = $array[$k][$col];
		}
	}
	return $ret;
}
