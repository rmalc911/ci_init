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
		return null;
	}
	return date(date_format, strtotime($time_string));
}

function time_format($time_string) {
	if (!$time_string) {
		return null;
	}
	return date(time_format, strtotime($time_string));
}

function date_time_format($time_string) {
	if (!$time_string) {
		return null;
	}
	return date(date_time_format, strtotime($time_string));
}

function user_date($time_string) {
	if (!$time_string) {
		return null;
	}
	return date(user_date, strtotime($time_string));
}

function user_time($time_string) {
	if (!$time_string) {
		return null;
	}
	return date(user_time, strtotime($time_string));
}

function user_date_time($time_string) {
	if (!$time_string) {
		return null;
	}
	return date(user_date_time, strtotime($time_string));
}

function db_user_time($time_string) {
	if (!$time_string) {
		return null;
	}
	return date(db_user_time, strtotime($time_string));
}

function input_date($time_string) {
	if (!$time_string) {
		return null;
	}
	return date(input_date, strtotime($time_string));
}

function input_time($time_string) {
	if (!$time_string) {
		return null;
	}
	return date(input_time, strtotime($time_string));
}

function input_date_time($time_string) {
	if (!$time_string) {
		return null;
	}
	return date(input_date_time, strtotime($time_string));
}

function relative_time(?string $time_string) {
	if (!$time_string) {
		return '';
	}
	$now = new DateTime();
	$dateObj = new DateTime("$time_string");
	$diff = $now->diff($dateObj);

	if ($diff->y > 0) {
		return $diff->y . " years ago";
	} elseif ($diff->m > 0) {
		return $diff->m . " months ago";
	} elseif ($diff->d > 0) {
		return $diff->d . " days ago";
	} elseif ($diff->h > 0) {
		return $diff->h . " hours ago";
	} elseif ($diff->i > 0) {
		return $diff->i . " minutes ago";
	} else {
		return "Just now";
	}
}

function remaining_days($time_string) {
	if (!$time_string) {
		return '';
	}
	$now = new DateTime();
	$now->setTime(0, 0, 0);
	$dateObj = new DateTime($time_string);
	$diff = $now->diff($dateObj);
	return $diff->days;
}

function getMaximumFileUploadSize() {
	$max_size = min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));
	$hard_limit = convertPHPSizeToBytes('8M');
	// $hard_limit = true; // use server limit
	return convertBytesToPHPSize(min($max_size, $hard_limit)) . 'B';
}

/**
 * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
 *
 * @param string $sSize
 * @return integer The value in bytes
 */
function convertPHPSizeToBytes($sSize) {
	//
	$sSuffix = strtoupper(substr($sSize, -1));
	if (!in_array($sSuffix, array('P', 'T', 'G', 'M', 'K'))) {
		return (int)$sSize;
	}
	$iValue = (int)substr($sSize, 0, -1);
	switch ($sSuffix) {
		case 'P':
			$iValue *= 1024;
			// Fallthrough intended
		case 'T':
			$iValue *= 1024;
			// Fallthrough intended
		case 'G':
			$iValue *= 1024;
			// Fallthrough intended
		case 'M':
			$iValue *= 1024;
			// Fallthrough intended
		case 'K':
			$iValue *= 1024;
			break;
	}
	return $iValue;
}

function convertBytesToPHPSize($iBytes) {
	$aSize = array('B', 'K', 'M', 'G', 'T', 'P');
	$i = 0;

	while ($iBytes >= 1024 && $i < (count($aSize) - 1)) {
		$iBytes /= 1024;
		$i++;
	}

	return round($iBytes) . $aSize[$i];
}
