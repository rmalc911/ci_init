<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajax extends MY_Controller {
	public function __construct() {
		parent::__construct();
		header('Content-Type: application/json');
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
			'products' => [
				'product_size_map' => 'product_id',
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
				$album_post[] = ['image_url' => $file['image'], 'album_id' => $album_id];
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

		header('Content-Type: image/png');
		// Render image
		imagepng($image);
		imagedestroy($image);
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
