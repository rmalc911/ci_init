<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TemplateModel extends MY_Model {
	/** @var TemplateConfig */
	public $banner_config;

	public function __construct() {
		parent::__construct();
		$this->banner_config = new TemplateConfig('web_banners', 'web_banner_add', 'web_banner_table', 'web_banner_view', 'web_banners', 'id', 'banner_name', 'status');
	}

	// Banners
	public function web_banner_view() {
		return [
			'head' => 'Website Banners',
			'links' => [
				// 'sort' => 'website/sort_banners',
				'add' => 'website/add_banner',
				'view' => 'website/view_banners',
			],
			'form_action' => ad_base_url('website/submit_banner'),
			'sort_submit' => ad_base_url('website/submit_sort_banners'),
			'form_ajax' => true,
		];
	}

	public function web_banner_table() {
		return [
			'heads' => ['Sl. no', 'Name', 'Image', 'Action'],
			'src' => 'ajax',
			'data' => 'ajaxtables/web_banners',
			'text_fields' => ['banner_name'],
			'img_fields' => ['banner_img' => BANNER_UPLOAD_PATH],
		];
	}

	public function web_banner_add() {
		return  [
			['type' => 'image', 'label' => 'Web Image', 'name' => 'banner_img', 'path' => BANNER_UPLOAD_PATH, 'required' => true, 'size' => [600, 400], 'accept' => ['png', 'jpeg', 'webp']],
			['type' => 'input', 'label' => 'Title', 'name' => 'banner_name', 'required' => true],
			['type' => 'textarea', 'label' => 'Banner Text', 'name' => 'banner_text', 'required' => false, 'attributes' => ['maxlength' => 150]],
			['type' => 'input', 'label' => 'Link', 'name' => 'banner_link', 'required' => false],
			['type' => 'key', 'label' => 'ID', 'name' => 'id'],
		];
	}
}
