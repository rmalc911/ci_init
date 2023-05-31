<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;

$route['admin'] = 'admin/home';
$route['admin/(:any)/(view_([a-zA-Z_]+))'] = 'admin/$1/view_wildcard/$1/$3';
$route['admin/(:any)/(add_([a-zA-Z_]+))'] = 'admin/$1/add_wildcard/$1/$3';
$route['admin/(:any)/(submit_sort_([a-zA-Z_]+))'] = 'admin/$1/submit_sort_wildcard/$1/$3';
$route['admin/(:any)/(submit_([a-zA-Z_]+))'] = 'admin/$1/submit_wildcard/$1/$3';
$route['admin/(:any)/(sort_([a-zA-Z_]+))'] = 'admin/$1/sort_wildcard/$1/$3';
$route['admin/(:any)/(dt_([a-zA-Z_]+))'] = 'admin/$1/dt_wildcard/$1/$3';
$route['admin/(:any)/(export_([a-zA-Z_]+))'] = 'admin/$1/export_wildcard/$1/$3';

$route['(:any)'] = 'home/$1';
