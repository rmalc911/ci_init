<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller {
	private $data = [];

	public function __construct() {
		parent::__construct();
		// $this->load->model('WebModel');
	}

	public function index() {
		$this->load->view('site/home', $this->data);
	}
}
