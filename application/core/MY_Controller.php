<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	public $data = [];
	public $popover_btn = "";
	public $action_field = true;
	public $init_dtable = true;

	public function __construct() {
		parent::__construct();
		$this->load->helper('inflector');
		$this->load->helper('text');
		// $this->load->helper('access');
		$this->load->library('Excel_export');
		$this->popover_btn = '<button class="btn btn-$3 btn-border btn-sm btn-rounded json-format txn-id-btn" type="button" data-toggle="popover" data-content=\'$2\' data-label="$1" data-original-title="$1 <span class=\'close \'>&times;</span">$1</button>';
		$this->data['config'] = $this->TemplateModel->get_config();
	}

	protected function _view_template(TemplateConfig $options) {
		$this->data['message'] = $this->session->flashdata('message');
		$this->TemplateModel->verify_access($options->access, 'view_data');
		$this->data['table_template'] = $this->TemplateModel->{$options->table_template}();
		$this->data['view_template'] = $this->TemplateModel->{$options->view_template}();
		$this->template('templates/view_template', $this->data);
	}

	protected function _add_template(TemplateConfig $options, callable $edit_map = null, callable $edit_prefill = null) {
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['edit'] = $this->TemplateModel->get_edit_row($options->table, '', $options->id);
		$this->data['form_template'] = $this->TemplateModel->{$options->form_template}($this->data['edit'][$options->id] ?? "");
		$this->data['view_template'] = $this->TemplateModel->{$options->view_template}();
		if ($edit_prefill) {
			$this->data['edit'] = $edit_prefill($this->data['edit']);
		}
		if ($this->data['edit']) {
			if ($edit_map) {
				$this->data['edit'] = $edit_map($this->data['edit']);
			}
			$this->TemplateModel->verify_access($options->access, 'edit_data');
		} else {
			$this->TemplateModel->verify_access($options->access, 'add_data');
		}
		$this->template('templates/add_template', $this->data);
	}

	protected function _sort_template(TemplateConfig $options) {
		$this->data['message'] = $this->session->flashdata('message');
		$this->data['view_template'] = $this->TemplateModel->{$options->view_template}();
		$default_sort_order = "ASC";
		$table_sort = $this->TemplateModel->{$options->table_template}()['sort_order'] ?? [$options->id, $default_sort_order];
		$img_fields = $this->TemplateModel->{$options->table_template}()['img_fields'] ?? [];
		if (is_array($table_sort)) {
			$sort_order = $table_sort[0];
			$sort_direction = $default_sort_order;
		} else {
			$table_sort = explode(" ", $table_sort);
			$sort_order = $table_sort[0];
			$sort_direction = $default_sort_order;
		}
		$table_alias = $options->table;
		$this->TemplateModel->verify_access($options->access, 'edit_data');
		$filter = [];
		if ($options->parent_field !== null) {
			$filter[$options->parent_field] = null;
		}
		if ($img_fields) {
			$this->db->select(array_keys($img_fields));
		}
		$this->data['img_fields'] = $img_fields;
		$this->data['sort_list'] = $options->get_options($filter, false, "ISNULL($table_alias.$sort_order) $sort_direction, $table_alias.$sort_order $sort_direction, $table_alias.{$options->id} $sort_direction");
		$this->template('templates/sort_template', $this->data);
	}

	protected function _submit_sort(TemplateConfig $options) {
		$view_template = $this->TemplateModel->{$options->view_template}();
		$return_url = ad_base_url($view_template['links']['view']);
		$table = $options->table;
		$sort = $this->input->post('sort');
		$sort_data = json_decode($sort);
		$update = [];
		if ($sort == '') {
			$alert = $this->TemplateModel->show_alert('', 'No Change');
			$this->session->set_flashdata('message', $alert);
			redirect($return_url);
		}
		$id = $options->id;
		foreach ($sort_data as $i => $sort_val) {
			array_push($update, [$id => $sort_val, 'sort_order' => $i]);
		}

		$status = $this->db->update_batch($table, $update, $id);
		$alert = $status >= 1 ? $this->TemplateModel->show_alert('suc', 'Successfully updated') : $this->TemplateModel->show_alert('err', 'Failed to update');
		$this->session->set_flashdata('message', $alert);
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
			$update = $this->TemplateModel->save_table_data($options->table, $post_data, $this->data['form_template'], $options->id);
			if ($after_submit) {
				$update = $after_submit($post_data, $update);
			}
			$error_data = $this->db->error()['message'];
			if ($update == true) {
				$this->db->trans_complete();
			}
			$return_url = ad_base_url($this->data['view_template']['links']['view']);
			echo json_encode(['save_id' => $update, 'status' => $update !== false, 'return_url' => $return_url, 'errors' => $this->form_validation->error_array(), 'error_data' => $error_data]);
		} else {
			echo json_encode(['status' => false, 'errors' => $this->form_validation->error_array()]);
		}
	}

	public function view_wildcard($path, $option) {
		if (method_exists($path, "view_{$option}")) {
			$this->{"view_{$option}"}();
		} else {
			$option = singular($option);
			$this->_view_template($this->TemplateModel->{"{$option}_config"});
		}
	}

	public function add_wildcard($path, $option) {
		if (method_exists($path, "add_{$option}")) {
			$this->{"add_{$option}"}();
		} else {
			$option = singular($option);
			/** @var TemplateConfig */
			$config = $this->TemplateModel->{"{$option}_config"};
			$this->_add_template($config, function ($edit) use ($option, $config) {
				$input_tables = array_filter(
					array_column($this->TemplateModel->{$config->form_template}(), null, 'name'),
					function ($val) {
						return $val['type'] == 'input-table';
					}
				);
				foreach ($input_tables as $ti => $input_table) {
					if (($input_table['table'] ?? "") != "") {
						$edit[$input_table['name']] = $this->TemplateModel->get_edit_map($input_table['table'], $input_table['key'], ($edit[$input_table['edit_key']] ?? ""));
					}
				}
				if (method_exists($this, "{$option}_edit_map")) {
					$edit = $this->{"{$option}_edit_map"}($edit);
				}
				return $edit;
			}, function ($edit) use ($option) {
				if (method_exists($this, "{$option}_edit_prefill")) {
					$edit = $this->{"{$option}_edit_prefill"}($edit);
				}
				return $edit;
			});
		}
	}

	public function submit_wildcard($path, $option) {
		if (method_exists($path, "submit_{$option}")) {
			$this->{"submit_{$option}"}();
		} else {
			$option = singular($option);
			$this->_submit_template($this->TemplateModel->{"{$option}_config"}, function ($post_data) use ($option) {
				/** @var TemplateConfig */
				$config = $this->TemplateModel->{"{$option}_config"};
				if (method_exists("TemplateModel", "{$option}_img_config")) {
					$form_template = array_column($this->TemplateModel->{$config->form_template}(), null, 'name');
					$img_configs = $this->TemplateModel->{"{$option}_img_config"}();
					foreach ($img_configs as $img_field => $path) {
						$edit = $this->TemplateModel->{$option . "_config"}->get_row($post_data['id']);
						$file_field = $_FILES[$img_field]['name'];
						$img_form_template = $form_template[$img_field];
						$accept = $img_form_template['accept'];
						if (in_array('jpeg', $accept)) {
							$accept[] = 'jpg';
							$accept[] = 'jpe';
						}
						$accept = join("|", $accept);
						if (is_array($file_field)) {
							$images = $this->TemplateModel->save_files($img_field, $path, $accept, null, true, explode(IMG_SPLIT, $edit[$img_field] ?? ''), $post_data['old_img']);
							$image = join(IMG_SPLIT, array_column($images, 'image'));
						} else {
							$image = $this->TemplateModel->save_image($img_field, $path, $accept, null, $edit[$img_field] ?? null);
						}
						if ($image) {
							$post_data[$img_field] = $image;
						}
						if (!$image && isset($file_field) && !empty($file_field)) {
							echo json_encode(['status' => false, 'errors' => [$img_field => $this->upload->display_errors('', '')]]);
							die();
						}
					}
				}
				if (method_exists($this, "{$option}_process_submit")) {
					$post_data = $this->{"{$option}_process_submit"}($post_data);
				}
				return $post_data;
			}, function ($post_data, $update) use ($option) {
				if (method_exists($this, "{$option}_after_submit")) {
					return $this->{"{$option}_after_submit"}($post_data, $update);
				}
				return true;
			});
		}
	}

	public function sort_wildcard($path, $option) {
		if (method_exists($path, "sort_{$option}")) {
			$this->{"sort_{$option}"}();
		} else {
			$option = singular($option);
			$this->_sort_template($this->TemplateModel->{"{$option}_config"});
		}
	}

	public function submit_sort_wildcard($path, $option) {
		if (method_exists($path, "submit_sort_{$option}")) {
			$this->{"submit_sort_{$option}"}();
		} else {
			$option = singular($option);
			$this->_submit_sort($this->TemplateModel->{"{$option}_config"});
		}
	}

	public function dt_wildcard($path, $option) {
		$option = singular($option);
		if (method_exists($path, "dt_{$option}")) {
			$this->{"dt_{$option}"}();
		} else {
			if (method_exists($this, "{$option}_table")) {
				$this->{"{$option}_table"}();
			}
			/** @var TemplateConfig */
			$options = $this->TemplateModel->{"{$option}_config"};
			$table_template = $this->TemplateModel->{$options->table_template}();
			$text_fields = $table_template['text_fields'];
			$select_fields = $table_template['select_fields'] ?? "";
			$search_fields = range(1, count($text_fields) + count(explode(',', $select_fields)));
			$this->_ajaxtable_template($options, $search_fields, $select_fields);
		}
	}

	public function export_wildcard($path, $option) {
		if (method_exists($path, "export_{$option}")) {
			$this->{"export_{$option}"}();
		} else {
			$option = singular($option);
			/** @var TemplateConfig */
			$options = $this->TemplateModel->{"{$option}_config"};
			$this->TemplateModel->verify_access($options->access, 'view_data');
			$filter = $this->input->post();
			list($table_heads, $table_data) = $this->TemplateModel->{"get_{$option}_export"}($filter);
			$view_template = $this->TemplateModel->{$options->view_template}();
			$name = $view_template['head'];
			$file_name = "Download-" . url_title($name) . "-" . date(date_format);
			return @Excel_export::exportExcelTable($table_data, $table_heads, $file_name);
		}
	}

	protected function add_action_col($cols) {
		$action_col = '<div class="text-center"><div class="btn-group" role="group" aria-label="Button group">';
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
		$action_col .= '</div></div>';
		return $action_col;
	}

	protected function add_image_col($path) {
		return '<div class="text-center"><img src="' . base_url($path . '$1') . '" data-original="$1" class="dt-image-col"></div>';
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
		$template = $this->TemplateModel->{$options->view_template}(false);
		$view_filters = $template['filter'] ?? [];
		$text_fields = $this->TemplateModel->{$options->table_template}()['text_fields'] ?? [];
		$img_fields = $this->TemplateModel->{$options->table_template}()['img_fields'] ?? [];
		$table_alias = $this->TemplateModel->{$options->table_template}()['table_alias'] ?? "a";
		$default_sort_order = "ASC";
		$table_sort = $this->TemplateModel->{$options->table_template}()['sort_order'] ?? [$options->id, $default_sort_order];
		if (is_array($table_sort)) {
			$sort_order = $table_sort[0];
			$sort_direction = $table_sort[1] ?? $default_sort_order;
		} else {
			$table_sort = explode(" ", $table_sort);
			$sort_order = $table_sort[0];
			$sort_direction = $table_sort[1] ?? $default_sort_order;
		}
		$joins = $this->TemplateModel->{$options->table_template}()['joins'] ?? [];
		$table = $options->table;

		if (!$options->form_template) {
			$this->action_field = false;
		}

		$status_field = "";
		if ($this->action_field && $options->status_field) {
			$status_field = ", " . $table_alias . "." . $options->status_field;
		}

		$id_field = $options->id;

		if ($select_fields != "") {
			$select_fields .= ", ";
		}

		$text_fields_select = (count($text_fields) > 0) ? join(', ', array_map(function ($key, $val) use ($table_alias) {
			if (substr_count($val, '.')) return $val;
			$column_alias = $table_alias;
			if (substr_count($key, '.')) {
				$key = explode(".", $key);
				$column_alias = $key[0];
				$key = $key[1];
			}
			$select = $column_alias . "." . $val;
			if ($val === "DATETIME") {
				$select = 'DATE_FORMAT(' . $column_alias . "." . $key . ', "' . db_user_date_time . '")';
			}
			if ($val === "DATE") {
				$select = 'DATE_FORMAT(' . $column_alias . "." . $key . ', "' . db_user_date . '")';
			}
			if ($val === "TIME") {
				$select = 'DATE_FORMAT(' . $column_alias . "." . $key . ', "' . db_user_time . '")';
			}
			if ($val === "CASE") {
				$select = $key;
				$key = "";
			}
			if (is_int($key)) $key = $val;
			if (is_string($key) && $key != "") $key = " AS " . $key;
			return $select . $key;
		}, array_keys($text_fields), $text_fields)) . "," : "";

		// [Image column name => Image file path]
		$img_fields_select = (count($img_fields) > 0) ? join(', ', array_map(function ($key) use ($table_alias) {
			// No need for file path in select query
			return $table_alias . "." . $key . " AS " . $key;
		}, array_keys($img_fields))) . "," : "";

		$this->db->order_by("ISNULL($table_alias.$sort_order) $sort_direction, $table_alias.$sort_order $sort_direction, $table_alias.{$options->id} $sort_direction");
		$this->datatables
			->from($table . " $table_alias")
			->select("1 as sl, $select_fields $text_fields_select $img_fields_select $table_alias.$id_field $status_field")
			->unset_column($id_field);

		if ($this->action_field) {
			$action_btns = [
				'edit' => $template['links']['add'],
				'delete' => "$1,$table,$id_field",
			];
			if ($status_field != "") {
				$action_btns['status'] = "$1,$table,$id_field,$2,$options->status_field";
			}
			$this->datatables
				->unset_column($options->status_field)
				->add_column(
					'action',
					$this->add_action_col($action_btns),
					"$id_field, $options->status_field"
				);
		}

		foreach ($img_fields as $img_field => $img_path) {
			$this->datatables
				->edit_column($img_field, $this->add_image_col($img_path), $img_field);
		}

		foreach ($joins as $join) {
			$join_table = $join['table'];
			$join_alias = $join['alias'];
			$join_condition = $join['condition'];
			$join_type = $join['type'] ?? "";
			$this->datatables->join("$join_table $join_alias", $join_condition, $join_type);
		}

		$filter_data = $this->input->post('filter') ?? [];
		foreach ($view_filters as $filter) {
			$filter_name = $filter['name'];
			$filter_value = $filter_data[$filter_name] ?? "";
			if ($filter_value == '') continue;
			if ($filter['type'] == 'date') {
				$filter_value = date(date_format, strtotime($filter_value));
				$filter_date_type = $filter['date_type'];
				$filter_name = explode("-", $filter_name)[0];
				$filter_name = "DATE({$filter_name}) {$filter_date_type}";
			}
			$this->datatables->filter($filter_name, $filter_value);
		}

		$this->setSearchableColumns($searchable_columns);
	}

	protected function template($template_name, $vars = array(), $return = FALSE) {
		$view = $this->load->view(ADMIN_VIEWS_PATH . 'includes/header', $vars, true);

		if (is_array($template_name)) {
			foreach ($template_name as $file_to_load) {
				$view .= $this->load->view(ADMIN_VIEWS_PATH . $file_to_load, $vars, true);
			}
		} else {
			$view .= $this->load->view(ADMIN_VIEWS_PATH . $template_name, $vars, true);
		}

		$view .= $this->load->view(ADMIN_VIEWS_PATH . 'includes/footer', $vars, true);
		if ($return) {
			return $view;
		}
		echo $view;
	}
}
