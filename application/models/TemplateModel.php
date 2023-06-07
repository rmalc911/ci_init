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

	// Users / Users
	public function user_view() {
		return [
			'head' => 'Users',
			'links' => [
				// 'sort' => 'users/sort_users',
				'add' => 'users/add_user',
				'view' => 'users/view_users',
			],
			'form_action' => ad_base_url('users/submit_user'),
			// 'sort_submit' => ad_base_url('users/submit_sort_users'),
			'form_ajax' => true,
		];
	}

	public function user_table() {
		return [
			'heads' => ['Sl. no', 'Name', 'Mobile', 'Password', 'Action'],
			'src' => 'ajax',
			'data' => 'ajaxtables/users',
			'text_fields' => ['display_name', 'user_mobile'],
		];
	}

	public function user_add() {
		$config = $this->user_config;
		$user_access_navs = $this->get_user_access_navs();
		return  [
			['type' => 'input', 'label' => 'Name', 'name' => 'display_name', 'required' => true, 'unique' => ['table' => $config->table, 'key' => $config->id]],
			['type' => 'input', 'label' => 'Mobile No.', 'name' => 'user_mobile', 'required' => true, 'unique' => ['table' => $config->table, 'key' => $config->id], 'help_text' => 'Login User ID'],
			['type' => 'input', 'label' => 'Email', 'name' => 'user_email', 'required' => false],
			['type' => 'custom', 'name' => 'user_access', 'validation' => false, 'view' => 'users/map-rights', 'params' => ['navs' => $user_access_navs]],
			['type' => 'hidden', 'label' => 'ID', 'name' => 'login_password'],
			['type' => 'hidden', 'label' => 'ID', 'name' => 'user_name'],
			['type' => 'key', 'label' => 'ID', 'name' => 'id'],
		];
	}

	public function get_user_access_navs() {
		return  [
			'Masters' => [
				['label' => 'States', 'name' => 'state', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'location/view_states'],
				['label' => 'Cities', 'name' => 'city', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'location/view_cities'],
				['label' => 'Users', 'name' => 'user', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'users/view_users'],
			],
			'Website' => [
				['label' => 'Banners', 'name' => 'banner', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_banners'],
				['label' => 'About Us', 'name' => 'about_us', 'options' => ['v', 'e'], 'url' => 'website/about_us_config'],
				['label' => 'Videos', 'name' => 'videos', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_videos'],
				['label' => 'Page Banners', 'name' => 'page_banners', 'options' => ['v', 'e'], 'url' => 'website/page_banners'],
				['label' => 'Industries', 'name' => 'industries', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_industries'],
				['label' => 'Projects', 'name' => 'projects', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_projects'],
				// ['label' => 'Service Categories', 'name' => 'service_categories', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_service_categories'],
				['label' => 'Services', 'name' => 'services', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_services'],
				['label' => 'Production Facilities', 'name' => 'production_facilities', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_production_facilities'],
				['label' => 'Testimonials', 'name' => 'testimonials', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_testimonials'],
				['label' => 'Awards', 'name' => 'awards', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_awards'],
				['label' => 'Blogs', 'name' => 'blogs', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_blogs'],
				['label' => 'Careers', 'name' => 'careers', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_careers'],
			],
			'Page Content' => [
				['label' => 'About Us (Overview)', 'name' => 'about_us_overview', 'options' => ['v', 'e'], 'url' => 'website/about_us_content'],
				['label' => 'Our Team', 'name' => 'team_members', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_team_members'],
				['label' => 'About Us (Mission & Vision)', 'name' => 'about_mission_vision', 'options' => ['v', 'e'], 'url' => 'website/about_us_mission_vision'],
				['label' => 'Clientele', 'name' => 'clientele', 'options' => ['v', 'a', 'e', 'b', 'd'], 'url' => 'website/view_clientele'],
			],
			'Enquiries' => [
				['label' => 'Career Applications', 'name' => 'career_applications', 'options' => ['v'], 'url' => 'website/view_career_applications'],
				['label' => 'Contact Us', 'name' => 'contact_us', 'options' => ['v'], 'url' => 'website/view_contact_us'],
			],
			'Config' => [
				['label' => 'Email', 'name' => 'email_config', 'options' => ['v', 'e'], 'url' => 'home/email_config'],
				['label' => 'Contact Details', 'name' => 'contact_details', 'options' => ['v', 'e'], 'url' => 'home/contact_details'],
				['label' => 'Static Pages SEO', 'name' => 'seo_static_pages', 'options' => ['v', 'e'], 'url' => 'home/seo_config/static'],
				['label' => 'Dynamic Pages SEO', 'name' => 'seo_dynamic_pages', 'options' => ['v', 'e'], 'url' => 'home/seo_config/dynamic'],
			],
		];
	}

	public function save_user_access_map($user_id, $post, $login_user_id = null) {
		$navs = $this->get_user_access_navs();
		foreach ($navs as $pages) {
			foreach ($pages as $page) {
				$page_access = $this->db->get_where('user_access_map', ['user' => $user_id, 'page' => $page['name']], 1)->row_array();
				$page_update = [
					'user' => $user_id,
					'page' => $page['name'],
					'view_data' => isset($post[$page['name'] . '~v']) ? '1' : '0',
					'add_data' => isset($post[$page['name'] . '~a']) ? '1' : '0',
					'edit_data' => isset($post[$page['name'] . '~e']) ? '1' : '0',
					'block_data' => isset($post[$page['name'] . '~b']) ? '1' : '0',
					'delete_data' => isset($post[$page['name'] . '~d']) ? '1' : '0',
					'updated_date' => date(date_time_format),
					'updated_by' => $login_user_id,
				];
				if ($page_access) {
					$this->db->update('user_access_map', $page_update, ['id' => $page_access['id']]);
				} else {
					$this->db->insert('user_access_map', $page_update);
				}
			}
		}
	}

	// Banners
	public function web_banner_view() {
		return [
			'head' => 'Website Banners',
			'links' => [
				'sort' => 'website/sort_banners',
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
			'sort_order' => 'sort_order',
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
