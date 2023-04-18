<?php
defined('BASEPATH') or exit('No direct script access allowed');

class WebModel extends CI_Model {
	public function get_careers() {
		$careers = $this->db
			->where('career_status', '1')
			->get('careers')
			->result_array();
		return $careers;
	}

	public function get_career_application($id) {
		$application = $this->db
			->select('ca.*, c.career_name, c.career_desc, c.career_desc_preview')
			->from('career_applications ca')
			->where(['ca.id' => $id])
			->join('careers c', 'c.id = ca.career_id')
			->get()
			->row_array();
		return $application;
	}

	public function get_contact_enquiry($id) {
		$enquiry = $this->db
			->from('contact_us')
			->where(['id' => $id])
			->get()
			->row_array();
		return $enquiry;
	}

	public function save_career_application($data) {
		$resume = $this->save_file('applicant-resume', RESUME_UPLOAD_PATH, 'doc|docx|pdf');
		if (is_array($resume)) {
			return $resume;
		}
		$resume_url = base_url(RESUME_UPLOAD_PATH . $resume);
		$post_data = [
			'career_id' => $data['career-option'],
			'applicant_fname' => $data['applicant-fname'],
			'applicant_lname' => $data['applicant-lname'],
			'applicant_email' => $data['applicant-email'],
			'applicant_phone' => $data['applicant-phone'],
			'applicant_resume' => $resume_url,
			'applicant_about' => $data['applicant-about'],
			'date' => date(date_time_format),
		];

		$res = $this->db->insert('career_applications', $post_data);
		if ($res) {
			$id = $this->db->insert_id();
			$table = $this->career_mail_table($id);
			$this->send_enquiry_mail(CLIENT_NAME . " - New Job Application Received", $table, "Job Application", "New Job Application");
		}
		return $res;
	}

	public function save_contact_form($data) {
		$post_data = [
			'contact_name' => $data['contact_name'],
			'contact_email' => $data['contact_email'],
			'contact_phone' => $data['contact_phone'],
			'contact_subject' => $data['contact_subject'],
			'contact_message' => $data['contact_message'],
			'submit_page' => $data['submit_page'],
			'contact_ip' => $this->input->ip_address(),
			'contact_date' => date(date_time_format),
		];

		$res = $this->db->insert('contact_us', $post_data);
		if ($res) {
			$id = $this->db->insert_id();
			$table = $this->contact_mail_table($id);
			$this->send_enquiry_mail(CLIENT_NAME . " - New Contact Enquiry Received", $table, "Contact Enquiry", "New Contact Enquiry");
		}
		return $res;
	}

	public function save_file($field, $path = 'assets/uploads', $accept = '*', $max_size = '4096') {
		$this->load->library('upload');
		if (isset($_FILES[$field]) && $_FILES[$field]['name'] != '') {
			if (!file_exists($path)) {
				mkdir($path, 0777, true);
			}
			$config['upload_path'] = dirname($_SERVER["SCRIPT_FILENAME"]) . '/' . $path;
			$config['allowed_types'] = $accept;
			$config['max_size'] = $max_size;
			$config['encrypt_name'] = true;
			$this->upload->initialize($config);
			$upload_status = false;
			if (is_array($_FILES[$field]['name'])) {
				$upload_status = $this->upload->do_upload($field . '[]');
			} else {
				$upload_status = $this->upload->do_upload($field);
			}
			if ($upload_status) {
				$data = $this->upload->data();
				$image = $data['file_name'];
				return $image;
			}
		}
		return $this->upload->error_msg;
	}

	public function get_config($config_key = null) {
		if ($config_key) {
			$this->db->where_in('config_key', $config_key);
		}
		$config = array_column($this->db->get('admin_config')->result_array(), 'config_value', 'config_key');
		if ($config_key != null && !is_array($config_key)) {
			return $config[$config_key] ?? null;
		}
		return $config;
	}

	private function career_mail_table($id) {
		$ap = $this->get_career_application($id);
		$enquiry_table = [
			'First Name' => $ap['applicant_fname'],
			'Last Name' => $ap['applicant_lname'],
			'Email' => $ap['applicant_email'],
			'Phone' => $ap['applicant_phone'],
			'Resume' => anchor($ap['applicant_resume'], "File"),
			'About' => $ap['applicant_about'],
			'Career Name' => $ap['career_name'],
			'Date' => date(user_date_time, strtotime($ap['date']))
		];
		return $enquiry_table;
	}

	private function contact_mail_table($id) {
		$ap = $this->get_contact_enquiry($id);
		$contact_table = [
			'Name' => $ap['contact_name'],
			'Email' => $ap['contact_email'],
			'Phone' => $ap['contact_phone'],
			'Subject' => $ap['contact_subject'],
			'Message' => $ap['contact_message'],
			'Date' => date(user_date_time, strtotime($ap['contact_date']))
		];
		return $contact_table;
	}

	private function send_enquiry_mail($subject, $table, $type, $message = "") {
		if ($message == "") {
			$message = "Hi Admin! New $type received.";
		}
		$mail_view = $this->load->view('email/new_enquiry', ['table' => $table, 'message' => $message], true);
		$config = $this->get_config();
		file_put_contents('mail/' . $type . ".html", $mail_view);
		if ($config == null) {
			return false;
		}
		$this->load->library('Alerts');
		$to_user_id = $config['alert_to_email_id'];
		if (!$to_user_id) {
			$to_user_id = $config['alert_from_email_id'] ?? DEFAULT_EMAIL_ID;
			$to_user_name = $config['alert_from_name'] ?? DEFAULT_EMAIL_NAME;
			$email_recipients = [
				['name' => $to_user_name, 'email' => $to_user_id],
			];
		} else {
			$email_recipients = [];
			$to_user_list = explode("\n", $to_user_id);
			// Clean empty strings
			$to_user_list = array_filter($to_user_list);
			foreach ($to_user_list as $to_user_line) {
				$to_user_row = explode(":", trim($to_user_line));
				if ($to_user_row[0] == "") continue;
				if (count($to_user_row) > 1) {
					array_push($email_recipients, ['name' => $to_user_row[1], 'email' => $to_user_row[0]]);
				} else {
					array_push($email_recipients, ['name' => DEFAULT_EMAIL_NAME, 'email' => $to_user_row[0]]);
				}
			}
		}
		return $this->alerts->send_mail($subject, $mail_view, $email_recipients);
	}
}
