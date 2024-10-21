<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller {
	private $data = [];

	public function __construct() {
		parent::__construct();
		$this->load->model('WebModel');
		$this->data['nav_active'] = 'home';
		$this->data['profile_config'] = $this->WebModel->get_profile_config();
	}

	public function _remap($method, $params = []) {
		// die(json_encode([
		// 	'method' => $method,
		// 	'params' => $params,
		// ]));
		if (method_exists($this, $method)) {
			return $this->{$method}(...$params);
		}
		$this->data['routed'] = true;
		if (empty($params)) return $this->seller($method);
		return $this->event($method, $params[0]);
	}

	public function index() {
		$this->data['nav_active'] = 'home';
		$this->load->view('site/home', $this->data);
	}

	public function contact() {
		$this->data['nav_active'] = 'contact';
		$this->load->view('site/contact', $this->data);
	}

	public function submit_contact() {
		$res = $this->WebModel->save_contact_form($this->input->post());
		$message = $res ? "Hi we have received your message, our team will get back to you soon. Thank You." : "";
		echo json_encode(['success' => $res, 'message' => $message]);
	}

	public function careers() {
		$this->data['nav_active'] = 'careers';
		$this->data['careers'] = $this->WebModel->get_careers();
		$this->load->view('site/careers', $this->data);
	}

	public function apply_career() {
		$res = $this->WebModel->save_career_application($this->input->post());
		$message = ($res === true) ? "Hi, thank you for applying, our team will get back to you soon." : (is_array($res) ? $res[0] : "");
		echo json_encode(['success' => ($res === true), 'message' => $message]);
	}

	public function blogs($url_title = "") {
		$this->data['nav_active'] = 'blogs';
		$page = $this->input->get('page') ?? 1;
		$per_page = 6;
		if ($url_title != '') {
			/** @var \db\blogs data['blog'] */
			$this->data['blog'] = $this->WebModel->get_blog_details($url_title);
			if (!$this->data['blog']) {
				return show_404();
			}
			$blog_id = $this->data['blog']->id;
			/** @var \db\blogs[] data['blogs'] */
			$this->data['blogs'] = $this->WebModel->get_blogs($per_page, $page, $blog_id);
			return $this->load->view('site/blog_view', $this->data);
		}
		$this->data['blogs'] = $this->WebModel->get_blogs($per_page, $page);
		$blogs_count = $this->WebModel->get_blog_count();
		$this->data['pagination'] = [
			'page' => $page,
			'per_page' => $per_page,
			'total' => $blogs_count,
		];
		$this->load->view('site/blogs', $this->data);
	}

	public function gallery($album_id = null) {
		$page = 'gallery';
		$this->data['nav_active'] = $page;
		if ($album_id) {
			$this->data['album'] = $this->WebModel->get_album($album_id);
			return $this->load->view('site/album', $this->data);
		}
		$this->data['gallery'] = $this->WebModel->get_albums();
		$this->load->view('site/gallery', $this->data);
	}

	public function terms() {
		$this->data['nav_active'] = 'terms';
		$this->data['tnc_content'] = $this->WebModel->get_config('tnc_content');
		$this->load->view('site/terms', $this->data);
	}
}
