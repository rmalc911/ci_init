<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller {
	private $data = [];

	public function __construct() {
		parent::__construct();
		$this->load->model('WebModel');
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
}
