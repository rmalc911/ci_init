<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	public $data = [];
	public $popover_btn = "";
	public $action_field = true;

	public function __construct() {
		parent::__construct();
		$this->popover_btn = '<button class="btn btn-$3 btn-border btn-sm btn-rounded json-format txn-id-btn" type="button" data-toggle="popover" data-content=\'$2\' data-label="$1" data-original-title="$1 <span class=\'close \'>&times;</span">$1</button>';
	}

	protected function _view_template(TemplateConfig $options) {
		$this->data['message'] = $this->session->flashdata('message');
		$this->TemplateModel->verify_access($options->access, 'view_data');
		$this->data['table_template'] = $this->TemplateModel->{$options->table_template}();
		$this->data['view_template'] = $this->TemplateModel->{$options->view_template}();
		$this->load->template('templates/view_template', $this->data);
	}

	protected function _add_template(TemplateConfig $options, callable $edit_map = null) {
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['form_template'] = $this->TemplateModel->{$options->form_template}();
		$this->data['view_template'] = $this->TemplateModel->{$options->view_template}();
		$this->data['edit'] = $this->TemplateModel->get_edit_row($options->table);
		if ($this->data['edit']) {
			if ($edit_map) {
				$this->data['edit'] = $edit_map($this->data['edit']);
			}
			$this->TemplateModel->verify_access($options->access, 'edit_data');
		} else {
			$this->TemplateModel->verify_access($options->access, 'add_data');
		}
		$this->load->template('templates/add_template', $this->data);
	}

	protected function _sort_template(TemplateConfig $options) {
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['view_template'] = $this->TemplateModel->{$options->view_template}();
		$this->TemplateModel->verify_access($options->access, 'edit_data');
		$filter = [];
		if ($options->parent_field !== null) {
			$filter[$options->parent_field] = null;
		}
		$this->data['sort_list'] = $options->get_options($filter, false, 'sort_order');
		$this->load->template('templates/sort_template', $this->data);
	}

	protected function _submit_sort(TemplateConfig $options) {
		$view_template = $this->TemplateModel->{$options->view_template}();
		$return_url = ad_base_url($view_template['links']['view']);
		$table = $options->table;
		$sort = $this->input->post('sort');
		$sort_data = json_decode($sort);
		$update = [];
		if ($sort == '') {
			redirect($return_url);
		}
		$id = $options->id;
		foreach ($sort_data as $i => $sort_val) {
			array_push($update, [$id => $sort_val, 'sort_order' => $i]);
		}

		$this->db->update_batch($table, $update, $id);
		redirect($return_url);
	}

	protected function _submit_template(TemplateConfig $options, callable $process_post_data = null, callable $after_submit = null) {
		$this->data['form_template'] = $this->TemplateModel->{$options->form_template}();
		$this->data['view_template'] = $this->TemplateModel->{$options->view_template}();
		$this->TemplateModel->set_validation($this->data['form_template']);
		if ($this->form_validation->run()) {
			$this->db->trans_start();
			$post_data = $this->input->post();
			if ($process_post_data) {
				// Return post data is required!
				$post_data = $process_post_data($post_data);
			}
			$update = $this->TemplateModel->save_table_data($options->table, $post_data, $this->data['form_template'], 'id');
			if ($after_submit) {
				$after_submit($post_data, $update);
			}
			$this->db->trans_complete();
			$return_url = ad_base_url($this->data['view_template']['links']['view']);
			echo json_encode(['save_id' => $update, 'status' => $update !== false, 'return_url' => $return_url, 'errors' => $this->form_validation->error_array()]);
		} else {
			echo json_encode(['status' => false, 'errors' => $this->form_validation->error_array()]);
		}
	}

	protected function add_action_col($cols) {
		$action_col = '<div class="btn-group" role="group" aria-label="Button group">';
		foreach ($cols as $col_name => $col_value) {
			if ($col_name == 'edit') {
				$action_col .= '<a href="' . ad_base_url($col_value . '?edit=$1') . '" class="btn btn-warning btn-sm"><i class="fa fa-fw fa-pencil-alt"></i></a>';
			}
			if ($col_name == 'delete') {
				$action_col .= '<button data-id="' . $col_value . '" class="btn btn-danger btn-sm delete-record"><i class="fa fa-fw fa-trash"></i></button>';
			}
			if ($col_name == 'status') {
				$action_col .= '<button data-id="' . $col_value . '" data-status="$2" class="btn btn-success btn-sm update-status"><i class="fa fa-fw fa-check"></i></button>';
			}
		}
		$action_col .= '</div>';
		return $action_col;
	}

	protected function add_image_col($path) {
		return '<img src="' . base_url($path . '$1') . '" data-original="$1" class="dt-image-col">';
	}

	protected function setSearchableColumns(array $columns) {
		foreach ($columns as $ci) {
			$_POST['columns'][$ci]['searchable'] = true;
		}
	}

	protected function generate_empty() {
		echo json_encode([
			'data' => [],
			'draw' => 0,
			'recordsFiltered' => 0,
			'recordsTotal' => 0,
		]);
	}

	protected function _ajaxtable_template(TemplateConfig $options, array $searchable_columns, string $select_fields = "") {
		$template = $this->TemplateModel->{$options->view_template}();
		$text_fields = $this->TemplateModel->{$options->table_template}()['text_fields'] ?? [];
		$img_fields = $this->TemplateModel->{$options->table_template}()['img_fields'] ?? [];
		$table_alias = $this->TemplateModel->{$options->table_template}()['table_alias'] ?? "a";
		$table = $options->table;

		$status_field = "";
		if ($this->action_field) {
			$status_field = $table_alias . "." . $options->status_field;
		}

		$id_field = $options->id;

		if ($select_fields != "") {
			$select_fields .= ", ";
		}

		$text_fields_select = (count($text_fields) > 0) ? join(', ', array_map(function ($key, $val) use ($table_alias) {
			$select = $table_alias . "." . $val;
			if ($key === "DATETIME") {
				$select = 'DATE_FORMAT(' . $val . ', "' . db_user_date_time . '")';
			}
			if ($key === "DATE") {
				$select = 'DATE_FORMAT(' . $val . ', "' . db_user_date . '")';
			}
			return $select . " AS " . $val;
		}, array_keys($text_fields), $text_fields)) . "," : "";

		// [Image column name => Image file path]
		$img_fields_select = (count($img_fields) > 0) ? join(', ', array_map(function ($key) use ($table_alias) {
			// No need for file path in select query
			return $table_alias . "." . $key . " AS " . $key;
		}, array_keys($img_fields))) . "," : "";

		$this->datatables
			->from($table . " $table_alias")
			->select("1 as sl, $select_fields $text_fields_select $img_fields_select $table_alias.$id_field, $status_field")
			->unset_column($id_field);

		if ($this->action_field) {
			$this->datatables
				->unset_column($options->status_field)
				->add_column(
					'action',
					$this->add_action_col(['edit' => $template['links']['add'], 'delete' => "$1,$table,$id_field", 'status' => "$1,$table,$id_field,$2,$options->status_field"]),
					"$id_field, $options->status_field"
				);
		}

		foreach ($img_fields as $img_field => $img_path) {
			$this->datatables
				->edit_column($img_field, $this->add_image_col($img_path), $img_field);
		}

		$this->setSearchableColumns($searchable_columns);
	}
}
