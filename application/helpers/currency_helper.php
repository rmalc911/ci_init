<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function inr_rate($n) {
	$suffix = "";
	// if ($n >= 1000) {
	// 	$n = round(($n / 1000), 2);
	// 	$suffix = " Thousand";
	// } else {
	// 	return "&#8377;$n";
	// }
	if ($n < 100000) {
		return "&#8377;" . number_format($n, 2);
	}
	if ($n >= 100000) {
		$n = round(($n / 100000), 2);
		$suffix = " Lakh";
	}
	if ($n >= 100) {
		$n = round(($n / 100), 2);
		$suffix = " Crore";
	}
	// $n = number_format($n, 1);
	return "&#8377;$n$suffix";
}

function getIndianCurrency(float $number) {
	$decimal = round($number - ($no = floor($number)), 2) * 100;
	$hundred = null;
	$digits_length = strlen($no);
	$i = 0;
	$str = array();
	$words = array(
		0 => '',
		1 => 'one',
		2 => 'two',
		3 => 'three',
		4 => 'four',
		5 => 'five',
		6 => 'six',
		7 => 'seven',
		8 => 'eight',
		9 => 'nine',
		10 => 'ten',
		11 => 'eleven',
		12 => 'twelve',
		13 => 'thirteen',
		14 => 'fourteen',
		15 => 'fifteen',
		16 => 'sixteen',
		17 => 'seventeen',
		18 => 'eighteen',
		19 => 'nineteen',
		20 => 'twenty',
		30 => 'thirty',
		40 => 'forty',
		50 => 'fifty',
		60 => 'sixty',
		70 => 'seventy',
		80 => 'eighty',
		90 => 'ninety'
	);
	$digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
	while ($i < $digits_length) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += $divider == 10 ? 1 : 2;
		if ($number) {
			$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
		} else $str[] = null;
	}
	$Rupees = trim(ucfirst(implode('', array_reverse($str))));
	$paise = ($decimal > 0) ? ". " . ucfirst($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
	return ($Rupees ? ($Rupees . $paise) . ' only' : '');
	//  . $paise;
}
