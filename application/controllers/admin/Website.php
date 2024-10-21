<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Website extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->data['message'] = $this->session->flashdata('message');
		$this->load->model('WebModel');
	}

	public function view_gallery() {
		$this->TemplateModel->verify_access('website_media', 'view_data');
		$this->data['view_template'] = $this->TemplateModel->website_media_view();
		$album_id = $this->input->get('edit');
		$this->data['album_id'] = $album_id;
		$this->data['images'] = $this->TemplateModel->get_edit_map('media_images', 'album_id', $album_id);
		$this->template('website/view_gallery', $this->data);
	}
}
