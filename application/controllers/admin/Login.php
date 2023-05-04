<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller {
	private $login_redirect = ADMIN_LOGIN_REDIRECT;

	public function index() {
		$data['message'] = $this->session->flashdata('login');
		$init = $this->db->get_where('users', ['user_mobile' => 'admin'])->num_rows();
		if ($init == 0) {
			$this->load->library('encription_utility');
			$pw = $this->encription_utility->getSaltPassword('password');
			$post = [
				'id' => 0,
				'user_name' => 'admin',
				'display_name' => 'admin',
				'user_mobile' => 'admin',
				'user_email' => NULL,
				'user_role' => NULL,
				'user_status' => '1',
				'created_date' => NULL,
				'updated_date' => NULL,
				'login_password' => $pw,
				'last_login' => NULL,
			];
			$this->db->insert('users', $post);
		}
		$this->load->view(ADMIN_VIEWS_PATH . 'pages/login', $data);
	}

	public function validate() {
		$this->session->unset_userdata('user');
		$post_data = $this->input->post();
		$this->load->library('encription_utility');
		$enc_password = $this->encription_utility->getSaltPassword($post_data['password']);
		$user = $this->db->get_where('users', ['user_mobile' => $post_data['username'], 'login_password' => $enc_password, 'user_status' => '1'], 1)->row_array();
		if (!$user) {
			$this->session->set_flashdata('login', 'Invalid Credentials!');
			redirect(ad_base_url());
		}
		$this->session->set_userdata('user', $user);
		$this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], ['user_mobile' => $post_data['username']]);
		if ($user['user_mobile'] != 'admin') {
			$view_access = $this->db->get_where('user_access_map', ['user' => $user['id'], 'view_data' => '1'], 1)->row_array();
			if (!$view_access) {
				$this->session->set_flashdata('login', 'No access provided, please ask admin to update your user rights');
				$this->logout();
			} else {
				$navs = $this->TemplateModel->get_user_access_navs();
				$break = 0;
				foreach ($navs as $pages) {
					foreach ($pages as $page) {
						if ($view_access['page'] == $page['name']) {
							$this->login_redirect = ADMIN_PATH . $page['url'];
							$break = 1;
							break;
						}
					}
					if ($break == 1) {
						break;
					}
				}
			}
		}
		redirect_base($this->login_redirect);
	}

	public function logout() {
		$this->session->unset_userdata('user');
		$this->session->unset_userdata('company_login');
		redirect(ad_base_url('login'));
	}

	public function admin_access_update() {
		$navs = $this->TemplateModel->get_user_access_navs();
		$post = [];

		foreach ($navs as $pages) {
			foreach ($pages as $page) {
				$post[$page['name'] . '~v'] = '1';
				$post[$page['name'] . '~a'] = '1';
				$post[$page['name'] . '~e'] = '1';
				$post[$page['name'] . '~b'] = '1';
				$post[$page['name'] . '~d'] = '1';
			}
		}
		$admin_id = $this->TemplateModel->get_edit_row('users', 'admin', 'user_name');
		$this->TemplateModel->save_user_access_map($admin_id['id'], $post);
	}
}
