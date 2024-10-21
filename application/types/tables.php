<?php

namespace db;

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'types/database.php');

trait table {
	public int $id;
	/** @var enum `1`|`0` */ public enum $table_status;
	public ?datetime $created_date;
	public ?datetime $updated_date;
}
