<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajaxtables extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('datatables');
		if (!$this->TemplateModel->db_setup) {
			$this->generate_empty();
			die();
		}
	}

	public function album_table() {
		/** @var TemplateConfig */ $config = $this->TemplateModel->album_config;
		$this->datatables
			->add_column(
				'album_images',
				'<a href="' . ad_base_url("website/view_gallery?edit=$1") . '" class="btn btn-primary btn-sm btn-round"><i class="fa fa-image"></i> View</a>',
				"id",
			);
	}

	public function user_table() {
		$this->db->where('user_type', 'user');
		$this->datatables
			->add_column(
				'password',
				'<div class="d-flex justify-content-center">
				<button class="btn btn-secondary btn-sm btn-round btn-icon" data-popup-view="reset_user_password" data-id="$1" data-no-btn="1" data-modal-size="swal-sm"><i class="fa fa-key"></i></button>
				</div>',
				'id'
			);
	}

	public function career_applications() {
		$this->action_field = false;
		/**
		 * $options
		 * @var TemplateConfig
		 */
		$options = $this->TemplateModel->career_application_config;
		$this->db->order_by('date', 'DESC');
		$this->datatables
			->unset_column('date')
			->unset_column('applicant_resume')
			->add_column(
				'about',
				'<button class="btn btn-info btn-icon btn-sm btn-round" data-popup-view="view_career_about" data-id="$1"><i class="fas fa-user fa-fw"></i></button>',
				'id'
			)
			->add_column(
				'resume',
				'<a target="_blank" href="$1" class="btn btn-primary btn-sm btn-round"><i class="fa fa-file"></i> View</a>',
				'applicant_resume'
			)
			->add_column(
				'date',
				'$1',
				'date'
			);
		$this->_ajaxtable_template($options, [1], 'c.career_name');
	}

	public function contact_us() {
		$this->action_field = false;
		/**
		 * $options
		 * @var TemplateConfig
		 */
		$options = $this->TemplateModel->contact_us_config;
		$this->datatables
			->edit_column(
				'contact_message',
				'<button class="btn btn-info btn-icon btn-sm btn-round" data-content="$2" data-popup-view="view_contact_message" data-id="$1"><i class="far fa-envelope fa-fw"></i></button>',
				'id,contact_message'
			);
		$this->_ajaxtable_template($options, [1, 2, 3, 4, 5]);
	}

	function __destruct() {
		if (!$this->TemplateModel->db_setup) {
			// $this->generate_empty();
			return;
		}
		$response = @$this->datatables->generate();
		echo $response;
	}
}
