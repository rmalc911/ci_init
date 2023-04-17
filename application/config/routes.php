<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['admin'] = 'admin/home';
$route['admin/(:any)/(view_([a-zA-Z_]+))'] = 'admin/$1/view_wildcard/$1/$3';
$route['admin/(:any)/(add_([a-zA-Z_]+))'] = 'admin/$1/add_wildcard/$1/$3';
$route['admin/(:any)/(submit_([a-zA-Z_]+))'] = 'admin/$1/submit_wildcard/$1/$3';
$route['admin/(:any)/(dt_([a-zA-Z_]+))'] = 'admin/$1/dt_wildcard/$1/$3';
