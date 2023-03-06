<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Append url to base_url(assets/uploads)
 * @param string $url
 */
function u_base_url($url = '') {
	return as_base_url('uploads/' . $url);
}

/**
 * Append url to admin path
 * @param string $url
 */
function ad_base_url($url = '') {
	return base_url('admin/' . $url);
}

/**
 * Append url to base_url(assets)
 * @param string $url
 */
function as_base_url($url = '') {
	return base_url('assets/' . $url);
}

/**
 * Append url to admin/assets
 * @param string $url
 */
function aa_base_url($url = '') {
	return as_base_url('admin/' . $url);
}

/**
 * Redirect to base url
 * @param string $url
 */
function redirect_base($url = '') {
	redirect(base_url($url));
}
