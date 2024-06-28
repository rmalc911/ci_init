<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['migration_enabled'] = TRUE;
$config['migration_type'] = 'timestamp';
$config['migration_table'] = 'z_ci_migrations';
$config['migration_auto_latest'] = FALSE;
$config['migration_version'] = 10000000000001;
$config['migration_path'] = APPPATH . 'migrations/';
