<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Masters extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->data['message'] = $this->session->flashdata('message');
	}
}