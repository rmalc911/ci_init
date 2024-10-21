<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajax extends MY_Controller {
	public function __construct() {
		parent::__construct();
		header('Content-Type: application/json');
	}

	public function reset_user_password() {
		$user_id = $this->input->post('value');
		echo json_encode([
			'data' => '',
			'title' => 'Reset Login Password',
			'content' => $this->load->view(ADMIN_VIEWS_PATH . 'users/reset_user_password', ['user_id' => $user_id], true)
		]);
	}

	public function view_career_about() {
		$application_id = $this->input->post('value');
		$this->data['application'] = $this->TemplateModel->get_edit_row('career_applications', $application_id);
		$this->data['title'] = $this->data['application']['applicant_fname'];
		$this->data['content'] = nl2br($this->data['application']['applicant_about']);
		echo json_encode($this->data);
	}

	public function view_contact_message() {
		$form_id = $this->input->post('value');
		$this->data['form'] = $this->TemplateModel->get_edit_row('contact_us', $form_id);
		$this->data['title'] = $this->data['form']['contact_name'];
		$this->data['content'] = $this->data['form']['contact_message'];
		echo json_encode($this->data);
	}

	public $table_pages = [
		'tax_vat' => 'vat',
		'tax_gst' => 'gst',
		'uom' => 'uoms',
	];

	public $skip_access_check = [
		// 'media_albums', 'careers', 'infrastructure_gallery', 'web_banners', 'media_images'
	];

	public $delete_images = [
		'web_banners' => [
			'banner_img' => BANNER_UPLOAD_PATH,
		],
	];

	public function delete_record() {
		$data = explode(',', $this->input->post('id'));
		$row = $data[0];
		$table = $data[1];
		$page_name = $this->table_pages[$table] ?? $table;
		if (in_array($page_name, $this->skip_access_check)) {
			$delete_access = true;
		} else {
			$delete_access = $this->TemplateModel->verify_access($page_name, 'delete_data', false);
		}
		if (!$delete_access) {
			echo json_encode(['success' => false, 'error' => '', 'error_message' => 'You are not allowed to delete this data.']);
			return;
		}
		$key = 'id';
		if (isset($data[2])) {
			$key = $data[2];
		}
		$delete_fk = [
			'users' => [
				'user_access_map' => 'user',
			],
		];
		if (isset($delete_fk[$table])) {
			foreach ($delete_fk[$table] as $di => $dtable) {
				$this->db->delete($di, [$dtable => $row]);
			}
		}
		$row_data = $this->db->get_where($table, [$key => $row])->row_array();
		$res = $this->db->delete($table, [$key => $row]);
		if ($res) {
			foreach ($this->delete_images[$table] ?? [] as $img_field => $img_path) {
				$file_deleted = @unlink($img_path . $row_data[$img_field]);
			}
		}
		echo json_encode(['success' => $res, 'error' => $this->db->error(), 'error_message' => '']);
	}

	public function status_update_record() {
		$data = explode(',', $this->input->post('id'));
		$id = $data[0];
		$table = $data[1];
		$key = $data[2];
		$old_status = $data[3];
		$status_column_name = $data[4];
		$page_name = $this->table_pages[$table] ?? $table;
		if (in_array($page_name, $this->skip_access_check)) {
			$block_access = true;
		} else {
			$block_access = $this->TemplateModel->verify_access($page_name, 'block_data', false);
		}
		if (!$block_access) {
			echo json_encode(['success' => false, 'error' => '', 'error_message' => 'You are not allowed to change status of this data.']);
			return;
		}
		$new_status = $old_status == '1' ? '0' : '1';
		$res = $this->db->update($table, [$status_column_name => $new_status], [$key => $id]);
		echo json_encode(['success' => $res, 'error' => $this->db->error(), 'error_message' => '']);
	}

	public function upload_gallery_files() {
		$album_id = $this->input->post('album_id');
		$files = $this->TemplateModel->save_files('file', GALLERY_UPLOAD_PATH);
		$errors = [];
		if (count($files) > 0) {
			$album_post = [];
			foreach ($files as $fi => $file) {
				if ($file['status'] == false) {
					$errors[] = $file['errors'];
					continue;
				}
				$album_post[] = [
					'image_url' => $file['image'],
					'album_id' => $album_id,
					'media_type' => 'i',
					'created_date' => date_time_format('now'),
				];
			}
			if (count($album_post) > 0) {
				$this->db->insert_batch('media_images', $album_post);
			}
		}
		$this->data['errors'] = $errors;
		$this->data['images'] = $this->TemplateModel->get_edit_map('media_images', 'album_id', $album_id);
		$this->data['html'] = $this->load->view(ADMIN_VIEWS_PATH . 'website/album-images', $this->data, true);
		echo json_encode($this->data);
	}
	
	public function save_media_link() {
		$url = $this->input->post('url');
		$album_id = $this->input->post('album_id');
		$album_post = [
			'image_url' => $url,
			'album_id' => $album_id,
			'media_type' => 'v',
			'created_date' => date_time_format('now'),
		];
		$res = $this->db->insert('media_images', $album_post);
		$this->data['images'] = $this->TemplateModel->get_edit_map('media_images', 'album_id', $album_id);
		$this->data['html'] = $this->load->view(ADMIN_VIEWS_PATH . 'website/album-images', $this->data, true);
		echo json_encode($this->data);
	}

	public function set_gallery_caption() {
		$image_id = $this->input->post('image_id');
		$caption = $this->input->post('image_caption');
		$res = false;
		if ($caption != '') {
			$res = $this->db->update('media_images', ['image_caption' => $caption], ['id' => $image_id]);
		}
		echo json_encode(['success' => $res]);
	}

	public function delete_gallery_image() {
		$file_path = GALLERY_UPLOAD_PATH;
		$image_id = $this->input->post('image_id');
		$image = $this->TemplateModel->get_edit_row('media_images', $image_id);
		if (!$image) {
			echo json_encode(['success' => false]);
			return;
		}
		if (!file_exists($file_path . $image['image_url'])) {
			echo json_encode(['success' => false]);
			return;
		}
		$db_deleted = $this->db->delete('media_images', ['id' => $image_id], 1);
		$file_deleted = false;
		if ($db_deleted) {
			$file_deleted = unlink($file_path . $image['image_url']);
		}
		echo json_encode(['success' => $file_deleted]);
	}

	public function view_gallery() {
		$album_id = $this->input->post('album_id');
		$this->data['images'] = $this->TemplateModel->get_edit_map('media_images', 'album_id', $album_id);
		$this->data['html'] = $this->load->view(ADMIN_VIEWS_PATH . 'website/album-images', $this->data, true);
		echo json_encode($this->data);
	}

	public function get_add_form() {
		$master = $this->input->post('master');
		$form_template = null;
		$form = '';
		$form_title = '';
		$master_fields = $this->get_master_fields($master);
		$form_template = $master_fields['form_template'];
		$form = $master_fields['form'];
		$form_title = $master_fields['form_title'];
		if ($form_template) {
			echo json_encode([
				'success' => true,
				'content' => $form,
				'title' => $form_title,
				'template' => $form_template
			]);
		}
	}

	public function get_master_fields($config_name) {
		/** @var TemplateConfig */
		$options = $this->TemplateModel->{$config_name};
		$view_template = $this->TemplateModel->{$options->view_template}();
		$form_template = $this->TemplateModel->{$options->form_template}();
		$form_title = 'Add ' . singular($view_template['head']);
		$table = $options->table;
		$key = $options->id;
		$option_name = $options->display_name;
		$form = $this->load->view(ADMIN_VIEWS_PATH . 'templates/form_template', ['template' => $form_template, 'popup_mode' => true], true);

		return [
			'form_template' => $form_template,
			'form' => $form,
			'form_title' => $form_title,
			'table' => $table,
			'key' => $key,
			'option_name' => $option_name,
		];
	}

	public function get_row() {
		$option = $this->input->get('config');
		/** @var TemplateConfig */ $config = $this->TemplateModel->{"{$option}_config"};
		$edit = $this->input->get('edit');
		$row = $this->TemplateModel->get_edit_row($config->table, $edit, $config->id);
		echo json_encode([
			'success' => $row ? true : false,
			'row' => $row,
		]);
	}

	public function save_add_form() {
		$form = $this->input->post('form');
		$values = $this->input->post('values');
		$master_fields = $this->get_master_fields($form);
		$form_template = $master_fields['form_template'];
		$table = $master_fields['table'];
		$key = $master_fields['key'];
		$option_name = $master_fields['option_name'];
		$added = $this->TemplateModel->save_table_data($table, $values, $form_template, $key);
		if ($added) {
			echo json_encode([
				'success' => true,
				'options' => $this->TemplateModel->select_options($table, $key, $option_name),
			]);
		}
	}

	public function get_config_options($config_name) {
		$filter = $this->input->get();
		/** @var TemplateConfig */ $config = $this->TemplateModel->{"{$config_name}_config"};
		echo json_encode($config->get_options($filter));
	}

	public function placeholder_img() {
		// Dimensions
		$getsize    = $this->input->get('size') ?? '100x100';
		$height = $this->input->get('height') ?? 150;
		$dimensions = explode('x', $getsize);
		if (empty($dimensions[1])) {
			$dimensions[1] = $dimensions[0];
		}
		$dim_y = $height;
		if (!empty($dimensions[1])) {
			$dim_y = min($dimensions[1], $height);
		}
		$dim_x = min($dim_y * ($dimensions[0] / $dimensions[1]), ($height * 2));

		// Create image
		$image      = imagecreate($dim_x, $dim_y);

		// Colours
		$bg         = isset($_GET['bg']) ? $_GET['bg'] : 'ccc';
		$bg         = hex2rgb($bg);
		$setbg      = imagecolorallocate($image, $bg['r'], $bg['g'], $bg['b']);

		$fg         = isset($_GET['fg']) ? $_GET['fg'] : '555';
		$fg         = hex2rgb($fg);
		$setfg      = imagecolorallocate($image, $fg['r'], $fg['g'], $fg['b']);

		// Text
		$text       = isset($_GET['text']) ? strip_tags($_GET['text']) : $getsize;
		$text       = str_replace('+', ' ', $text);

		// Text positioning
		$fontsize   = 4;
		$fontwidth  = imagefontwidth($fontsize);    // width of a character
		$fontheight = imagefontheight($fontsize);   // height of a character
		$length     = strlen($text);                // number of characters
		$textwidth  = $length * $fontwidth;         // text width
		$xpos       = (imagesx($image) - $textwidth) / 2;
		$ypos       = (imagesy($image) - $fontheight) / 2;

		// Generate text
		imagestring($image, $fontsize, $xpos, $ypos, $text, $setfg);

		// cache images
		$seconds_to_cache = 60 * 60 * 24 * 30; // s*m*h*d
		$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
		header("Expires: $ts");
		header("Pragma: cache");
		header("Cache-Control: max-age=$seconds_to_cache");

		header('Content-Type: image/png');
		// Render image
		imagepng($image);
		imagedestroy($image);
	}

	public function generate_barcode($code, $filename = "") {
		if ($filename != "") $filename = 'assets/uploads/barcode/' . $filename;
		$this->barcode($filename, $code, 40, 'horizontal', 'code128', true);
	}

	private function barcode($filepath = "", $text = "0", $size = "20", $orientation = "horizontal", $code_type = "code128", $print = false, $SizeFactor = 1) {
		$code_string = "";
		// Translate the $text into barcode the correct $code_type
		if (in_array(strtolower($code_type), array("code128", "code128b"))) {
			$chksum = 104;
			// Must not change order of array elements as the checksum depends on the array's key to validate final code
			$code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "\`" => "111422", "a" => "121124", "b" => "121421", "c" => "141122", "d" => "141221", "e" => "112214", "f" => "112412", "g" => "122114", "h" => "122411", "i" => "142112", "j" => "142211", "k" => "241211", "l" => "221114", "m" => "413111", "n" => "241112", "o" => "134111", "p" => "111242", "q" => "121142", "r" => "121241", "s" => "114212", "t" => "124112", "u" => "124211", "v" => "411212", "w" => "421112", "x" => "421211", "y" => "212141", "z" => "214121", "{" => "412121", "|" => "111143", "}" => "111341", "~" => "131141", "DEL" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "FNC 4" => "114131", "CODE A" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
			$code_keys = array_keys($code_array);
			$code_values = array_flip($code_keys);
			for ($X = 1; $X <= strlen($text); $X++) {
				$activeKey = substr($text, ($X - 1), 1);
				$code_string .= $code_array[$activeKey];
				$chksum = ($chksum + ($code_values[$activeKey] * $X));
			}
			$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

			$code_string = "211214" . $code_string . "2331112";
		} elseif (strtolower($code_type) == "code128a") {
			$chksum = 103;
			$text = strtoupper($text); // Code 128A doesn't support lower case
			// Must not change order of array elements as the checksum depends on the array's key to validate final code
			$code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "NUL" => "111422", "SOH" => "121124", "STX" => "121421", "ETX" => "141122", "EOT" => "141221", "ENQ" => "112214", "ACK" => "112412", "BEL" => "122114", "BS" => "122411", "HT" => "142112", "LF" => "142211", "VT" => "241211", "FF" => "221114", "CR" => "413111", "SO" => "241112", "SI" => "134111", "DLE" => "111242", "DC1" => "121142", "DC2" => "121241", "DC3" => "114212", "DC4" => "124112", "NAK" => "124211", "SYN" => "411212", "ETB" => "421112", "CAN" => "421211", "EM" => "212141", "SUB" => "214121", "ESC" => "412121", "FS" => "111143", "GS" => "111341", "RS" => "131141", "US" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "CODE B" => "114131", "FNC 4" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
			$code_keys = array_keys($code_array);
			$code_values = array_flip($code_keys);
			for ($X = 1; $X <= strlen($text); $X++) {
				$activeKey = substr($text, ($X - 1), 1);
				$code_string .= $code_array[$activeKey];
				$chksum = ($chksum + ($code_values[$activeKey] * $X));
			}
			$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

			$code_string = "211412" . $code_string . "2331112";
		} elseif (strtolower($code_type) == "code39") {
			$code_array = array("0" => "111221211", "1" => "211211112", "2" => "112211112", "3" => "212211111", "4" => "111221112", "5" => "211221111", "6" => "112221111", "7" => "111211212", "8" => "211211211", "9" => "112211211", "A" => "211112112", "B" => "112112112", "C" => "212112111", "D" => "111122112", "E" => "211122111", "F" => "112122111", "G" => "111112212", "H" => "211112211", "I" => "112112211", "J" => "111122211", "K" => "211111122", "L" => "112111122", "M" => "212111121", "N" => "111121122", "O" => "211121121", "P" => "112121121", "Q" => "111111222", "R" => "211111221", "S" => "112111221", "T" => "111121221", "U" => "221111112", "V" => "122111112", "W" => "222111111", "X" => "121121112", "Y" => "221121111", "Z" => "122121111", "-" => "121111212", "." => "221111211", " " => "122111211", "$" => "121212111", "/" => "121211121", "+" => "121112121", "%" => "111212121", "*" => "121121211");

			// Convert to uppercase
			$upper_text = strtoupper($text);

			for ($X = 1; $X <= strlen($upper_text); $X++) {
				$code_string .= $code_array[substr($upper_text, ($X - 1), 1)] . "1";
			}

			$code_string = "1211212111" . $code_string . "121121211";
		} elseif (strtolower($code_type) == "code25") {
			$code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
			$code_array2 = array("3-1-1-1-3", "1-3-1-1-3", "3-3-1-1-1", "1-1-3-1-3", "3-1-3-1-1", "1-3-3-1-1", "1-1-1-3-3", "3-1-1-3-1", "1-3-1-3-1", "1-1-3-3-1");

			for ($X = 1; $X <= strlen($text); $X++) {
				for ($Y = 0; $Y < count($code_array1); $Y++) {
					if (substr($text, ($X - 1), 1) == $code_array1[$Y])
						$temp[$X] = $code_array2[$Y];
				}
			}

			for ($X = 1; $X <= strlen($text); $X += 2) {
				if (isset($temp[$X]) && isset($temp[($X + 1)])) {
					$temp1 = explode("-", $temp[$X]);
					$temp2 = explode("-", $temp[($X + 1)]);
					for ($Y = 0; $Y < count($temp1); $Y++)
						$code_string .= $temp1[$Y] . $temp2[$Y];
				}
			}

			$code_string = "1111" . $code_string . "311";
		} elseif (strtolower($code_type) == "codabar") {
			$code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "$", ":", "/", ".", "+", "A", "B", "C", "D");
			$code_array2 = array("1111221", "1112112", "2211111", "1121121", "2111121", "1211112", "1211211", "1221111", "2112111", "1111122", "1112211", "1122111", "2111212", "2121112", "2121211", "1121212", "1122121", "1212112", "1112122", "1112221");

			// Convert to uppercase
			$upper_text = strtoupper($text);

			for ($X = 1; $X <= strlen($upper_text); $X++) {
				for ($Y = 0; $Y < count($code_array1); $Y++) {
					if (substr($upper_text, ($X - 1), 1) == $code_array1[$Y])
						$code_string .= $code_array2[$Y] . "1";
				}
			}
			$code_string = "11221211" . $code_string . "1122121";
		}

		// Pad the edges of the barcode
		$code_length = 20;
		if ($print) {
			$text_height = 20;
		} else {
			$text_height = 0;
		}

		for ($i = 1; $i <= strlen($code_string); $i++) {
			$code_length = $code_length + (int)(substr($code_string, ($i - 1), 1));
		}

		if (strtolower($orientation) == "horizontal") {
			$img_width = $code_length * $SizeFactor;
			$img_height = $size;
		} else {
			$img_width = $size;
			$img_height = $code_length * $SizeFactor;
		}

		$image = imagecreate($img_width, $img_height + $text_height);
		$black = imagecolorallocate($image, 0, 0, 0);
		$white = imagecolorallocate($image, 255, 255, 255);

		imagefill($image, 0, 0, $white);
		if ($print) {
			imagestring($image, 5, 10, $img_height + 2, $text, $black);
		}

		$location = 10;
		for ($position = 1; $position <= strlen($code_string); $position++) {
			$cur_size = $location + (substr($code_string, ($position - 1), 1));
			if (strtolower($orientation) == "horizontal")
				imagefilledrectangle($image, $location * $SizeFactor, 0, $cur_size * $SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black));
			else
				imagefilledrectangle($image, 0, $location * $SizeFactor, $img_width, $cur_size * $SizeFactor, ($position % 2 == 0 ? $white : $black));
			$location = $cur_size;
		}

		// Draw barcode to the screen or save in a file
		if ($filepath == "") {
			header('Content-type: image/png');
			imagepng($image);
			imagedestroy($image);
		} else {
			imagepng($image, $filepath);
			imagedestroy($image);
		}
	}
}

// Convert hex to rgb (modified from csstricks.com)
function hex2rgb($colour) {
	$colour = preg_replace("/[^abcdef0-9]/i", "", $colour);
	if (strlen($colour) == 6) {
		list($r, $g, $b) = str_split($colour, 2);
		return array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
	} elseif (strlen($colour) == 3) {
		list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
		return array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
	}
	return false;
}
