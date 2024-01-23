<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Website extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}

	// Blogs (view_blogs, add_blog, submit_blog)
	protected function blog_process_submit($post_data) {
		$post_data['blog_desc_preview'] = wysiwyg_to_preview_text($post_data['blog_desc'], 150);
		return $post_data;
	}

	// Careers (view_careers, add_career, submit_career)
	protected function career_process_submit($post_data) {
		$post_data['career_desc_preview'] = wysiwyg_to_preview_text($post_data['career_desc'], 150);
		return $post_data;
	}
}
