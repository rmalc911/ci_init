<?php

class MY_Form_validation extends CI_Form_validation {

	public function __construct($rules = array()) {
		$this->CI = &get_instance();
		parent::__construct($rules);
	}
	public function edit_unique($str, $field) {
		sscanf($field, '%[^.].%[^.].%[^.].%[^.]', $table, $field, $columnId, $id);
		$columnIdGroup = explode('>>', $columnId);
		$count = count($columnIdGroup);
		$columnIdName = $columnIdGroup[$count - 1];
		$where = [
			$field => $str,
			$columnIdName . '!=' => $id
		];
		if ($count > 1) {
			$compositeKeys = explode(",", $columnIdGroup[0]);
			foreach ($compositeKeys as $cki => $compositeKey) {
				$compositeVal = $this->CI->input->post($compositeKey);
				$where[$compositeKey] = $compositeVal;
			}
		}
		$set = isset($this->CI->db)
			? ($this->CI->db->get_where($table, $where, 1)->num_rows() === 0)
			: FALSE;
		return $set;
	}
}
