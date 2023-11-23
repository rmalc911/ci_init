<?php
defined('BASEPATH') or exit('No direct script access allowed');

function html_string_add_new_lines($html) {
	$tags = array('(</p>)', '(</li>)', '(<br />)', '(<br>)', '(<hr />)', '(<hr>)', '(</h1>)', '(</h2>)', '(</h3>)', '(</h4>)', '(</h5>)', '(</h6>)', '(</div>)');
	$text = preg_replace($tags, "$0\n", $html);
	$text = preg_replace('/(&nbsp;(<br\s*\/?>\s*)|(<br\s*\/?>\s*))+/im', "\n", $text);
	$text = preg_replace("/[\r\n]{2,}/", "\n", $text);
	return $text;
}

function wysiwyg_to_preview_text($html, $length) {
	if (!function_exists('ellipsize')) {
		$ci = &get_instance();
		$ci->load->library('text_helper');
	}
	return ellipsize(html_string_add_new_lines($html), $length);
}

function single_db_image($images, $key) {
	return explode(IMG_SPLIT, $images)[$key] ?? '';
}

function date_format_c($time_string) {
	if (!$time_string) {
		return '';
	}
	return date(date_format, strtotime($time_string));
}

function time_format($time_string) {
	if (!$time_string) {
		return '';
	}
	return date(time_format, strtotime($time_string));
}

function date_time_format($time_string) {
	if (!$time_string) {
		return '';
	}
	return date(date_time_format, strtotime($time_string));
}

function user_date($time_string) {
	if (!$time_string) {
		return '';
	}
	return date(user_date, strtotime($time_string));
}

function user_time($time_string) {
	if (!$time_string) {
		return '';
	}
	return date(user_time, strtotime($time_string));
}

function user_date_time($time_string) {
	if (!$time_string) {
		return '';
	}
	return date(user_date_time, strtotime($time_string));
}

function db_user_time($time_string) {
	if (!$time_string) {
		return '';
	}
	return date(db_user_time, strtotime($time_string));
}
