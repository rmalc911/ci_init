<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migrate extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->data['message'] = $this->session->flashdata('message');
		$login = $this->TemplateModel->verify_admin();
		if (!$login) {
			return die();
		}
		$this->db = @DB('migrator');
	}

	public function migrate() {
		$this->load->library('migration');
		$this->db->query("SET FOREIGN_KEY_CHECKS=0;");
		$this->db->trans_start();
		if ($this->migration->current() === false) {
			$this->db->trans_rollback();
			show_error($this->migration->error_string());
		}
		$complete = $this->db->trans_complete();
		if (!$complete) {
			$this->db->trans_rollback();
		}
		$this->db->query("SET FOREIGN_KEY_CHECKS=1;");
	}

	public function new() {
		$migration_config_path = FCPATH . "application/config/migration.php";
		$migrations_dir = FCPATH . 'application/migrations';
		$filecount = count(glob("{$migrations_dir}/*_update_*.php")) + 1;
		$contents = file_get_contents("{$migrations_dir}/sample.php");
		$timestamp = date("YmdHis");

		$this->load->library('migration');
		$current = $this->db->get('z_ci_migrations', 1)->row()->version;
		$update = $this->db->update('z_ci_migrations', ['version' => $timestamp], ['1' => '1']);

		$migration_config = file_get_contents($migration_config_path);
		$migration_config = str_replace($current, $timestamp, $migration_config);
		file_put_contents($migration_config_path, $migration_config);

		$search = "Migration_Update_";
		$length = strlen($search);
		$start = strpos($contents, $search,);
		$end = $start + $length;
		$contents = substr($contents, 0, $end) . $filecount . substr($contents, $end);

		file_put_contents("{$migrations_dir}/{$timestamp}_update_{$filecount}.php", $contents);
		echo $timestamp;
	}
}
