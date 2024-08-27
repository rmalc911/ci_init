<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function user_edit_prefill($edit) {
		$edit['user_type'] = 'user';
		return $edit;
	}

	public function user_process_submit($post_data) {
		$post_data['user_name'] = $post_data['display_name'];
		if ($post_data['login_password'] == '') {
			$this->load->library('encription_utility');
			$pw = $this->encription_utility->getSaltPassword('password');
			$post_data['login_password'] = $pw;
		}
		return $post_data;
	}

	public function user_after_submit($post_data, $update) {
		$user_data = $this->session->userdata('user');
		$this->TemplateModel->save_user_access_map($update, $post_data, $user_data['id']);
		return true;
	}

	public function user_edit_map($edit) {
		// $edit['user_type'] = 'user';
		$edit['user_access'] = $this->TemplateModel->get_user_access_map($edit);
		return $edit;
	}

	public function reset_password() {
		$new_password = $this->input->post('password');
		$user_id = $this->input->post('user_id');
		$user = $this->TemplateModel->get_edit_row('users', $user_id, 'id');
		if ($new_password != '' && $user_id != '' && $user) {
			$this->load->library('encription_utility');
			$enc_password = $this->encription_utility->getSaltPassword($new_password);
			$status = $this->db->update('users', ['login_password' => $enc_password], ['id' => $user_id]);
			$alert = $status ? $this->TemplateModel->show_alert('suc', 'Successfully updated') : $this->TemplateModel->show_alert('err', 'Failed to update');
			$this->session->set_flashdata('message', $alert);
		}
		$redirect = "users/view_users";
		if ($user['user_type'] == "customer") {
			$redirect = "customers/view_customers";
		}
		redirect(ad_base_url($redirect));
	}
}
