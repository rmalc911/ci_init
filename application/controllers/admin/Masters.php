<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Masters extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function config_type_profile_edit() {
		$user_data = $this->session->userdata('user');
		$type_id = $user_data['user_id'];
		return $this->TemplateModel->get_edit_row('type_table', $type_id, 'id');
	}

	public function config_type_profile_submit($post_data) {
		$user_data = $this->session->userdata('user');
		$type_id = $user_data['user_id'];
		/** @var \TemplateConfig */ $config = $this->TemplateModel->type_config;
		$form_template = $this->data['form_template'];
		$post_data[$config->id] = $type_id;
		$update = $this->TemplateModel->save_table_data($config->table, $post_data, $form_template, $config->id);
		$user_data = [
			'user_id' => $update,
			'user_type' => 'type',
			// 'user_name' => $post_data['type_name'],
			// 'display_name' => $post_data['type_name'],
			'user_mobile' => $post_data['type_phone'],
			'user_email' => $post_data['type_email'],
		];
		$save_user = $this->TemplateModel->save_user_account($user_data);
		return $update;
	}

	public function type_after_submit($post_data, $update) {
		$user_data = [
			'user_id' => $update,
			'user_type' => 'executive',
			'user_name' => $post_data['executive_name'],
			'display_name' => $post_data['executive_name'],
			'user_mobile' => $post_data['executive_mobile'],
			'user_email' => $post_data['executive_email'],
		];
		$save_user = $this->TemplateModel->save_user_account($user_data);
		return $save_user;
	}
}
