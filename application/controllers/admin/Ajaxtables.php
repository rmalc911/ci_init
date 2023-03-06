<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajaxtables extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('datatables');
	}

	public function web_banners() {
		/**
		 * $options 
		 * @var TemplateConfig
		 */
		$options = $this->TemplateModel->banner_config;
		$this->_ajaxtable_template($options, [1]);
	}

	function __destruct() {
		$response = $this->datatables->generate();
		echo $response;
	}
}
