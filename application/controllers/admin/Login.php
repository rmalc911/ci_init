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
				'user_type' => 'admin',
				'user_status' => '1',
				'created_date' => date_time_format('now'),
				'updated_date' => NULL,
				'login_password' => $pw,
				'last_login' => NULL,
			];
			$this->db->insert('users', $post);
		}
		$data['config'] = $this->TemplateModel->get_config();
		$this->load->model('WebModel');
		$data['contact_data'] = $this->WebModel->get_profile_config();
		$this->load->view(ADMIN_VIEWS_PATH . 'pages/login', $data);
	}

	public function validate() {
		$this->session->unset_userdata('user');
		$post_data = $this->input->post();
		$this->load->library('encription_utility');
		$enc_password = $this->encription_utility->getSaltPassword($post_data['password']);
		if (isset($post_data['role'])) {
			$this->db->where('user_type', $post_data['role']);
		}
		$user = $this->db->get_where('users', ['user_mobile' => $post_data['username'], 'login_password' => $enc_password, 'user_status' => '1'])->result_array();
		if (count($user) == 0) {
			$this->session->set_flashdata('login', 'Invalid Credentials!');
			redirect(ad_base_url('login'));
		}
		if (count($user) == 1) {
			$user = $user[0];
		} else {
			$user_roles = array_column($user, 'user_type');
			return redirect(ad_base_url('login/login_roles?username=' . $post_data['username'] . '&password=' . $post_data['password'] . '&roles=' . implode(',', $user_roles)));
		}
		$this->db->update('users', ['last_login' => date(date_time_format)], ['user_mobile' => $post_data['username']]);
		if ($user['user_type'] == 'admin') {
			$this->session->set_userdata('user', $user);
			redirect_base($this->login_redirect);
			return;
		}
		$user_types = [
			"type1" => "type1_table",
			"type2" => "type2_table",
		];
		foreach ($user_types as $user_type => $user_type_table) {
			if ($user['user_type'] == $user_type) {
				$$user_type = $this->db->get_where($user_type_table, ['id' => $user['user_id']])->row_array();
				if ($$user_type[$user_type . '_status'] !== "1") {
					$this->session->unset_userdata('user');
					$this->session->unset_userdata('login');
					$this->session->set_flashdata('login', 'Login disabled, please contact admin.');
					redirect(ad_base_url('login'));
					return;
				}
				$this->session->set_userdata('user', $user);
				redirect_base(ADMIN_PATH . "{$user_type}/config_{$user_type}_profile");
				return;
			}
		}
		$view_access = $this->db->order_by('id')->get_where('user_access_map', ['user' => $user['id'], 'view_data' => '1'], 1)->row_array();
		if (!$view_access) {
			$this->session->set_flashdata('login', 'No access provided, please ask admin to update your user rights');
			$this->logout();
		} else {
			$this->session->set_userdata('user', $user);
			$navs = $this->TemplateModel->get_user_access_navs();
			$break = 0;
			foreach ($navs as $pages) {
				foreach ($pages as $page) {
					/** @var TemplateConfig $page_config */
					$page_config = $page['config'];
					$page_name = $page_config->access;
					if ($view_access['page'] == $page_name) {
						$nav_view = $this->TemplateModel->{$page_config->view_template}(false);
						$nav_links = $nav_view['links'];
						$url = $nav_links['view'];
						$this->login_redirect = ADMIN_PATH . $url;
						$break = 1;
						break;
					}
				}
				if ($break == 1) {
					break;
				}
			}
		}
		redirect_base($this->login_redirect);
	}

	public function login_roles() {
		$this->load->helper('inflector');
		$data['config'] = $this->TemplateModel->get_config();
		$data['message'] = $this->session->flashdata('login');
		$data['roles'] = explode(',', $this->input->get('roles'));
		$data['username'] = $this->input->get('username');
		$data['password'] = $this->input->get('password');
		$this->load->view(ADMIN_VIEWS_PATH . 'pages/login_roles', $data);
	}

	public function logout() {
		$this->session->unset_userdata('user');
		$this->session->unset_userdata('company_login');
		redirect_base(ADMIN_PATH);
	}
}
