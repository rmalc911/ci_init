<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;

$route['admin'] = 'admin/home';
$route['admin/login'] = 'admin/login';
$route['admin/(:any)/(view_([a-zA-Z_]+))'] = 'admin/$1/view_wildcard/$1/$3';
$route['admin/(:any)/(add_([a-zA-Z_]+))'] = 'admin/$1/add_wildcard/$1/$3';
$route['admin/(:any)/(submit_sort_([a-zA-Z_]+))'] = 'admin/$1/submit_sort_wildcard/$1/$3';
$route['admin/(:any)/(submit_([a-zA-Z_]+))'] = 'admin/$1/submit_wildcard/$1/$3';
$route['admin/(:any)/(sort_([a-zA-Z_]+))'] = 'admin/$1/sort_wildcard/$1/$3';
$route['admin/(:any)/(dt_([a-zA-Z_]+))'] = 'admin/$1/dt_wildcard/$1/$3';
$route['admin/(:any)/(export_([a-zA-Z_]+))'] = 'admin/$1/export_wildcard/$1/$3';

$route['webhooks/(:any)'] = 'webhooks/$1';
$route['user/(:any)'] = 'home/user_$1';
$route['(:any)'] = 'home/$1';
$route['(:any)/(:any)'] = 'home/$1/$2';
