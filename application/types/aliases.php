<?php

namespace dba;

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'types/tables.php');

trait join {
    use \db\table;
    /** @var ?\db\table */ public ?\db\table $city;
}
