<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TemplateModel extends MY_Model {
	/** @var TemplateConfig */
	public $banner_config;

	/** @var TemplateConfig */
	public $career_config;

	/** @var TemplateConfig */
	public $career_application_config;

	/** @var TemplateConfig */
	public $contact_us_config;

	public function __construct() {
		parent::__construct();
		$this->banner_config = new TemplateConfig('web_banners', 'web_banner_add', 'web_banner_table', 'web_banner_view', 'web_banners', 'id', 'banner_name', 'status');
		$this->career_config = new TemplateConfig('careers', 'career_add', 'career_table', 'career_view', 'careers', 'id', 'career_name', 'career_status');
		$this->career_application_config = new TemplateConfig('career_applications', null, 'career_application_table', 'career_application_view', 'career_applications', 'id', 'applicant_fname');
		$this->contact_us_config = new TemplateConfig('contact_us', null, 'contact_us_table', 'contact_us_view', 'contact_us', 'id', 'contact_name', null);
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
			'data' => 'ajaxtables/dt_banners',
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

	public function banner_img_config() {
		return [
			'banner_img' => BANNER_UPLOAD_PATH,
		];
	}

	// Website / Careers
	public function career_view() {
		return [
			'head' => 'Job Corner',
			'links' => [
				// 'sort' => 'website/sort_careers',
				'add' => 'website/add_career',
				'view' => 'website/view_careers',
			],
			'form_action' => ad_base_url('website/submit_career'),
			// 'sort_submit' => ad_base_url('website/submit_sort_careers'),
			'form_ajax' => true,
		];
	}

	public function career_table() {
		return [
			'heads' => ['Sl. no', 'Name', 'Action'],
			'src' => 'ajax',
			'data' => 'ajaxtables/dt_careers',
			'text_fields' => ['career_name'],
		];
	}

	public function career_add() {
		return  [
			['type' => 'input', 'label' => 'Name', 'name' => 'career_name', 'required' => true, 'unique' => ['table' => 'careers', 'key' => 'id']],
			['type' => 'wysiwyg', 'label' => 'Description', 'name' => 'career_desc', 'required' => false],
			['type' => 'hidden', 'label' => '', 'name' => 'career_desc_preview'],
			['type' => 'key', 'label' => 'ID', 'name' => 'id'],
		];
	}


	// Enquiries / Career Applications
	public function career_application_view($queries = true) {
		$career_options = $queries ? $this->career_config->get_options() : [];
		return [
			'head' => 'Career Applications',
			'links' => [
				'view' => 'website/view_career_applications',
			],
			'filter' => [
				['type' => 'select', 'label' => 'Career', 'name' => 'career_id', 'filter_options' => $career_options],
				['type' => 'date', 'name' => 'date-1', 'label' => 'From Date', 'date_type' => '>='],
				['type' => 'date', 'name' => 'date-2', 'label' => 'To Date', 'date_type' => '<='],
			],
			'export' => 'enquiries/export_career_applications',
		];
	}

	public function career_application_table() {
		return [
			'heads' => ['Sl. no', 'Career', 'First Name', 'Last Name', 'Email', 'Phone', 'About', 'Resume', 'Date'],
			'src' => 'ajax',
			'data' => 'ajaxtables/career_applications',
			'text_fields' => ['applicant_fname', 'applicant_lname', 'applicant_email', 'applicant_phone', 'applicant_resume', 'date' => 'DATETIME'],
			'joins' => [
				[
					'table' => 'careers',
					'alias' => 'c',
					'condition' => 'c.id = a.career_id',
				]
			]
		];
	}

	public function get_career_application_export($filter) {
		$config = $this->career_application_config;
		$table_heads = [
			'career_name' => 'Career',
			'applicant_fname' => 'First Name',
			'applicant_lname' => 'Last Name',
			'applicant_email' => 'Email',
			'applicant_phone' => 'Phone',
			'applicant_about' => 'Message',
			'applicant_resume' => 'Resume',
			'date' => 'Date',
		];
		return $this->get_export($config, $table_heads, $filter);
	}

	// Website / Contact Us
	public function contact_us_view() {
		return [
			'head' => 'Contact Us',
			'links' => [
				'view' => 'website/view_contact_us',
			],
			'filter' => [
				['type' => 'date', 'name' => 'contact_date-1', 'label' => 'From Date', 'date_type' => '>='],
				['type' => 'date', 'name' => 'contact_date-2', 'label' => 'To Date', 'date_type' => '<='],
			],
			'export' => 'enquiries/export_contact_us',
		];
	}

	public function contact_us_table() {
		return [
			'heads' => ['Sl. no', 'Name', 'Email', 'Message', 'Date'],
			'src' => 'ajax',
			'data' => 'ajaxtables/contact_us',
			'text_fields' => ['contact_name', 'contact_email', 'contact_message', 'contact_date' => 'DATETIME'],
		];
	}

	public function get_contact_us_export($filter) {
		$config = $this->contact_us_config;
		$table_heads = [
			'contact_name' => 'Name',
			// 'submit_page' => 'Submit Page',
			'contact_email' => 'Email',
			'contact_message' => 'Message',
			'contact_date' => 'Date',
		];
		return $this->get_export($config, $table_heads, $filter);
	}
}
