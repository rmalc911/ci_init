<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Update_ extends CI_Migration {

	public function up() {
		$this->db->query(
			""
		);
	}

	public function down() {
		$this->dbforge->drop_table('');
	}
}
