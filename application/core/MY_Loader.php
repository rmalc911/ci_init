<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Loader extends CI_Loader {
	public function template($template_name, $vars = array(), $return = FALSE) {
		$view = $this->view(ADMIN_VIEWS_PATH . 'includes/header', $vars, true);

		if (is_array($template_name)) {
			foreach ($template_name as $file_to_load) {
				$view .= $this->view(ADMIN_VIEWS_PATH . $file_to_load, $vars, true);
			}
		} else {
			$view .= $this->view(ADMIN_VIEWS_PATH . $template_name, $vars, true);
		}

		$view .= $this->view(ADMIN_VIEWS_PATH . 'includes/footer', $vars, true);
		if ($return) {
			return $view;
		} else {
			echo $view;
		}
	}
}
