<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TemplateModel extends MY_Model {
	/** @var TemplateConfig */ public $profile_config;
	/** @var TemplateConfig */ public $banner_config;
	/** @var TemplateConfig */ public $career_config;
	/** @var TemplateConfig */ public $career_application_config;
	/** @var TemplateConfig */ public $contact_us_config;
	// Config
	/** @var TemplateConfig */ public $email_config;

	public function __construct() {
		parent::__construct();
		$this->profile_config = new TemplateConfig('profile', 'profile_form', null, 'profile_view', null,);
		$this->user_config = new TemplateConfig('users', 'user_add', 'user_table', 'user_view', 'users', 'id', 'user_name', 'user_status',);
		$this->testimonial_config = new TemplateConfig('testimonials', 'testimonial_add', 'testimonial_table', 'testimonial_view', 'testimonials', 'id', 'testimonial_title', 'testimonial_status',);
		$this->blog_config = new TemplateConfig('blogs', 'blog_add', 'blog_table', 'blog_view', 'blogs', 'id', 'blog_title', 'blog_status',);
		$this->banner_config = new TemplateConfig('web_banners', 'web_banner_add', 'web_banner_table', 'web_banner_view', 'web_banners', 'id', 'banner_name', 'status');
		$this->career_config = new TemplateConfig('careers', 'career_add', 'career_table', 'career_view', 'careers', 'id', 'career_name', 'career_status');
		$this->career_application_config = new TemplateConfig('career_applications', null, 'career_application_table', 'career_application_view', 'career_applications', 'id', 'applicant_fname');
		$this->contact_us_config = new TemplateConfig('contact_us', null, 'contact_us_table', 'contact_us_view', 'contact_us', 'id', 'contact_name', null);
		$this->email_config = new TemplateConfig('email', 'email_form', null, 'email_view', null,);
	}

	#region Home
	// Home / Profile
	public function profile_view() {
		return [
			'head' => 'Profile',
			'links' => [
				'view' => 'home/config_profile',
			],
		];
	}

	public function profile_form() {
		$config = $this->profile_config;
		return [
			['type' => 'input', 'label' => 'Company Name', 'name' => 'company_name', 'required' => true],
			['type' => 'textarea', 'label' => 'Company Address', 'name' => 'company_address', 'required' => true],
			['type' => 'input', 'label' => 'City', 'name' => 'company_city', 'required' => true],
			['type' => 'input', 'label' => 'Contact Phone', 'name' => 'company_phone', 'required' => true],
			['type' => 'input', 'label' => 'Contact Email', 'name' => 'company_email', 'required' => true],
			['type' => 'input', 'label' => 'Website URL', 'name' => 'company_website', 'required' => false],
			['type' => 'image', 'label' => 'Company Logo', 'name' => PROFILE_LOGO_FIELD, 'size' => [500, 500], 'accept' => ['jpeg', 'png', 'webp'], 'path' => PROFILE_LOGO_UPLOAD_PATH],
			['type' => 'image', 'label' => 'Site Favicon', 'name' => PROFILE_FAVICON_FIELD, 'size' => [200, 200], 'accept' => ['jpeg', 'png', 'webp'], 'path' => PROFILE_LOGO_UPLOAD_PATH, 'help_text' => 'Must be square'],
			// ['type' => 'input-table', 'label' => 'Contact Persons', 'name' => 'company_contact_persons', 'fields' => 'company_contact_persons', 'table-inline' => true],
			['type' => 'input-table', 'label' => 'Social Links', 'name' => 'contact_social_links', 'fields' => 'contact_social_links', 'table-inline' => true, 'validation' => false, 'table' => 'contact_social_links'],
		];
	}

	public function company_contact_persons() {
		return [
			['type' => 'input', 'label' => 'Name', 'name' => 'contact_name', 'required' => true],
			['type' => 'input', 'label' => 'Phone', 'name' => 'contact_phone', 'required' => true],
		];
	}

	public function contact_social_links() {
		$social_media_links = SOCIAL_MEDIA_NAMES;
		return [
			['type' => 'select-widget', 'label' => 'Social Media', 'name' => 'social_icon_class', 'options' => $social_media_links, 'required' => true, 'attributes' => ['data-icon-class' => 'fab mr-1 fa-fw fa-']],
			['type' => 'input', 'label' => 'Link', 'name' => 'social_icon_url', 'required' => true],
		];
	}

	public function profile_img_config() {
		return [
			PROFILE_LOGO_FIELD => PROFILE_LOGO_UPLOAD_PATH,
			PROFILE_FAVICON_FIELD => PROFILE_LOGO_UPLOAD_PATH,
		];
	}
	#endregion

	#region Users / Users
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
			'data' => 'ajaxtables/dt_users',
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
			['type' => 'hidden', 'label' => 'ID', 'name' => 'user_type'],
			['type' => 'key', 'label' => 'ID', 'name' => 'id'],
		];
	}

	public function get_user_access_navs() {
		return  [
			'' => [
				['config' => $this->profile_config, 'options' => ['v', 'e'], 'icon' => 'fa fa-user'],
				// users
				['options' => ['v', 'a', 'e', 'b', 'd'], 'config' => $this->user_config, 'icon' => 'fas fa-user-cog'],
			],
			'Enquiries' => [
				// contact us
				['options' => ['v'], 'config' => $this->contact_us_config, 'icon' => 'fas fa-phone'],
			],
			'About Us' => [
				['config' => $this->about_us_config, 'options' => ['v', 'e'], 'icon' => 'fa fa-info'],
			],
			// 'Enquiries' => [
			// 	['config' => $this->career_application_config, 'options' => ['v'], 'icon' => 'fa fa-user'],
			// 	['config' => $this->contact_us_config, 'options' => ['v'], 'icon' => 'fa fa-user'],
			// ],
			'Config' => [
				['options' => ['v', 'e'], 'config' => $this->email_config, 'icon' => 'fas fa-envelope'],
			],
		];
	}

	public function save_user_access_map($user_id, $post, $login_user_id = null) {
		$navs = $this->get_user_access_navs();
		foreach ($navs as $pages) {
			foreach ($pages as $page) {
				/** @var TemplateConfig $page_config */
				$page_config = $page['config'];
				$page_name = $page_config->access;
				$page_access = $this->db->get_where('user_access_map', ['user' => $user_id, 'page' => $page_name], 1)->row_array();
				$page_update = [
					'user' => $user_id,
					'page' => $page_name,
					'view_data' => isset($post[$page_name . '~v']) ? '1' : '0',
					'add_data' => isset($post[$page_name . '~a']) ? '1' : '0',
					'edit_data' => isset($post[$page_name . '~e']) ? '1' : '0',
					'block_data' => isset($post[$page_name . '~b']) ? '1' : '0',
					'delete_data' => isset($post[$page_name . '~d']) ? '1' : '0',
					'updated_date' => date(date_time_format),
					'updated_by' => $login_user_id,
				];
				if ($page_access) {
					$this->db->update('user_access_map', $page_update, ['id' => $page_access['id'], 'user' => $user_id, 'page' => $page_name,]);
				} else {
					$this->db->insert('user_access_map', $page_update);
				}
			}
		}
	}
	#endregion

	#region Banners
	public function banner_view() {
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

	public function banner_table() {
		return [
			'heads' => ['Sl. no', 'Name', 'Image', 'Action'],
			'src' => 'ajax',
			'data' => 'ajaxtables/dt_banners',
			'text_fields' => ['banner_name'],
			'img_fields' => $this->banner_img_config(),
			'sort_order' => 'sort_order',
		];
	}

	public function banner_add() {
		return  [
			['type' => 'image', 'label' => 'Web Image', 'name' => 'banner_img', 'path' => BANNER_UPLOAD_PATH, 'required' => true, 'size' => [600, 400], 'accept' => ['png', 'jpeg', 'webp']],
			['type' => 'input', 'label' => 'Title', 'name' => 'banner_name', 'required' => false],
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
	#endregion

	#region About Us
	public function about_us_view() {
		return [
			'head' => 'About Us',
			'links' => [
				'view' => 'website/config_about_us',
			],
		];
	}

	public function about_us_form() {
		return  [
			['type' => 'image', 'label' => 'Image', 'name' => 'about_us_img', 'path' => ABOUTUS_UPLOAD_PATH, 'required' => true, 'size' => [600, 600], 'accept' => ['png', 'jpeg', 'webp']],
			['type' => 'input', 'label' => 'Title', 'name' => 'about_us_title', 'required' => true],
			['type' => 'wysiwyg', 'label' => 'Description', 'name' => 'about_us_description', 'required' => true],
			['type' => 'key', 'label' => 'ID', 'name' => 'id'],
		];
	}

	public function about_us_img_config() {
		return [
			'about_us_img' => ABOUTUS_UPLOAD_PATH,
		];
	}
	#endregion

	#region Website / Testimonials
	public function testimonial_view() {
		return [
			'head' => 'Testimonials',
			'links' => [
				'sort' => 'website/sort_testimonials',
				'add' => 'website/add_testimonial',
				'view' => 'website/view_testimonials',
			],
			'form_action' => ad_base_url('website/submit_testimonial'),
			// 'sort_submit' => ad_base_url('website/submit_sort_testimonials'),
			'form_ajax' => true,
		];
	}

	public function testimonial_table() {
		return [
			'heads' => ['Sl. no', 'Name', 'Designation', 'Action'],
			'src' => 'ajax',
			'data' => 'ajaxtables/dt_testimonials',
			'text_fields' => ['testimonials_name', 'testimonials_designation'],
			'sort_order' => 'sort_order',
		];
	}

	public function testimonial_add() {
		$testimonial_config = $this->testimonial_config;
		return [
			['type' => 'input', 'label' => 'Name', 'name' => 'testimonials_name', 'required' => true],
			['type' => 'input', 'label' => 'Designation', 'name' => 'testimonials_designation', 'required' => true],
			['type' => 'textarea', 'label' => 'Description', 'name' => 'testimonials_description', 'required' => true],
			['type' => 'key', 'name' => $testimonial_config->id],
		];
	}
	#endregion

	#region Website / Blogs
	public function blog_view() {
		return [
			'head' => 'Blogs',
			'links' => [
				// 'sort' => 'website/sort_blogs',
				'add' => 'website/add_blog',
				'view' => 'website/view_blogs',
			],
			'form_action' => ad_base_url('website/submit_blog'),
			// 'sort_submit' => ad_base_url('website/submit_sort_blogs'),
			'form_ajax' => true,
		];
	}

	public function blog_table() {
		return [
			'heads' => ['Sl. no', 'Title', 'Date', 'Image', 'Action'],
			'src' => 'ajax',
			'data' => 'ajaxtables/dt_blogs',
			'text_fields' => ['blog_title', 'blog_date' => 'DATE'],
			'sort_order' => 'blog_date DESC',
			'img_fields' => $this->blog_img_config(),
		];
	}

	public function blog_add() {
		$blog_config = $this->blog_config;
		return [
			['type' => 'input', 'label' => 'Title', 'name' => 'blog_title', 'required' => true, 'unique' => ['table' => $blog_config->table, 'key' => $blog_config->id]],
			['type' => 'input', 'label' => 'URL', 'name' => 'blog_url_title', 'required' => false, 'readonly' => true, 'help_text' => 'Auto generated'],
			['type' => 'date-widget', 'label' => 'Date', 'name' => 'blog_date', 'required' => true],
			['type' => 'image', 'label' => 'Image', 'name' => 'blog_image', 'size' => [1600, 600], 'accept' => ['jpeg', 'png', 'webp'], 'path' => BLOG_IMAGE_UPLOAD_PATH],
			['type' => 'wysiwyg', 'label' => 'Content', 'name' => 'blog_content', 'required' => true],
			['type' => 'hidden', 'label' => 'Preview', 'name' => 'blog_content_preview'],
			['type' => 'key', 'name' => $blog_config->id],
		];
	}

	public function blog_img_config() {
		return [
			'blog_image' => BLOG_IMAGE_UPLOAD_PATH,
		];
	}
	#endregion

	#region Website / Careers
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
	#endregion

	#region Enquiries / Career Applications
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
			'sort_order' => 'date DESC',
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
	#endregion

	#region Website / Contact Us
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
			'sort_order' => 'contact_date DESC',
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
	#endregion

	#region Website / Album
	public function album_view() {
		return [
			'head' => 'Albums',
			'links' => [
				// 'sort' => 'website/sort_albums',
				'add' => 'website/add_album',
				'view' => 'website/view_albums',
			],
			'form_action' => ad_base_url('website/submit_album'),
			// 'sort_submit' => ad_base_url('website/submit_sort_albums'),
			'form_ajax' => true,
		];
	}

	public function album_table() {
		$config = $this->album_config;
		return [
			'heads' => ['Sl. no', 'Name', 'Images', 'Action'],
			'src' => 'ajax',
			'data' => 'ajaxtables/dt_albums',
			'text_fields' => [$config->display_name],
		];
	}

	public function album_add() {
		$config = $this->album_config;
		return  [
			['type' => 'input', 'label' => 'Name', 'name' => $config->display_name, 'required' => true, 'unique' => ['table' => $config->table, 'key' => $config->id]],
			['type' => 'hidden', 'name' => 'url_path'],
			['type' => 'key', 'label' => '', 'name' => $config->id],
		];
	}

	public function website_media_view() {
		return [
			'head' => 'Media Gallery',
			'links' => [
				'add' => 'website/add_album',
				'view' => 'website/view_albums',
			],
			'form_action' => ad_base_url('website/submit_media'),
			'sort_submit' => ad_base_url('website/submit_sort_media'),
			'form_ajax' => true,
		];
	}

	public function website_media_table() {
		return [
			'heads' => ['Sl. no', 'Name', 'Images', 'Action'],
			'src' => 'ajax',
			'data' => 'ajaxtables/media_albums',
			'sort_order' => 'sort_order',
		];
	}

	public function website_media_add() {
		return  [
			['type' => 'input', 'label' => 'Name', 'name' => 'album_name', 'required' => true, 'unique' => ['table' => 'media_albums', 'key' => 'id']],
			['type' => 'key', 'label' => 'ID', 'name' => 'id'],
		];
	}
	#endregion

	#region Home / Email
	public function email_view() {
		return [
			'head' => 'Email Config',
			'links' => [
				'view' => 'home/email_config',
			],
		];
	}

	public function email_form() {
		return [
			['type' => 'radio', 'label' => 'Sendmail Mode', 'name' => 'sendmail_mode', 'required' => true],
			['type' => 'textarea', 'label' => 'Alert To Email ID', 'name' => 'alert_to_email_id', 'required' => true],
			['type' => 'input', 'label' => 'Alert From Email ID', 'name' => 'alert_from_email_id', 'required' => true],
			['type' => 'input', 'label' => 'Alert From Name', 'name' => 'alert_from_name', 'required' => true],
			['type' => 'textarea', 'label' => 'Sendinblue API Key', 'name' => 'sendinblue_api_key', 'required' => true],
		];
	}
	#endregion
}
