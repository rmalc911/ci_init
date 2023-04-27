<?php

class MY_Exceptions extends CI_Exceptions {

	/**
	 * 404 Page Not Found Handler
	 *
	 * @access  private
	 * @param   string
	 * @return  string
	 */
	function show_404($page = '', $log_error = TRUE) {
		$heading = "404 Page Not Found";
		$message = "The page you requested was not found.";

		// By default we log this, but allow a dev to skip it
		if (
			!in_array($page, [
				"Assets/uploads",
				"Assets/admin",
				"Assets/plugins",
			])
			&&
			$log_error
		) {
			$msg = '';
			if (isset($_SERVER['HTTP_REFERER'])) {
				$msg .= 'Referer was ' . $_SERVER['HTTP_REFERER'];
			} else {
				$msg .= 'Referer was not set or empty';
			}
			if (isset($_SERVER['REMOTE_ADDR'])) {
				$msg .= ' IP address was ' . $_SERVER['REMOTE_ADDR'];
			} else {
				$msg .= ' Unable to track IP';
			}
			log_message('error', '404 Page Not Found --> ' . $page . ' - ' . $msg);
		}

		echo $this->show_error($heading, $message, 'error_404', 404);
		exit;
	}
}
