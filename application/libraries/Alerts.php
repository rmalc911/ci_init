<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Alerts {
	public $protocol = 'sendmail';

	public $smtp_host = '';
	public $smtp_port = 465;
	public $smtp_user = "";
	public $smtp_pass = "";

	public $from_id = '';
	public $from_name = '';
	public $reply_mail = '';

	private $sendmail_mode = 'local';
	private $API_KEY = null;
	private $config = null;

	function __construct() {
		$config_items = [
			'sendmail_mode',
			'alert_from_email_id',
			'alert_from_name',
			'alert_to_email_id',
			'sendinblue_api_key',
		];
		$CI = &get_instance();
		$config = $CI->AdminModel->get_config($config_items);
		if ($config == null) {
			return;
		}
		if ($config['alert_from_name'] != '' && $config['alert_from_email_id'] != '') {
			$this->from_name = $config['alert_from_name'];
			$this->from_id = $config['alert_from_email_id'];
		}
		$this->reply_mail = $this->from_id;

		$this->sendmail_mode = $config['sendmail_mode'];
		if ($this->sendmail_mode == 'sendinblue') {
			$sendinblue_api_key = $config['sendinblue_api_key'];
			if ($sendinblue_api_key != '') {
				$this->API_KEY = $sendinblue_api_key;
				$this->config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->API_KEY);
			}
		}
	}

	public function send_mail($subject, $message, $touser, $cc = [], $bcc = [], $attachments = []) {
		$this->writeLog(json_encode(['subject' => $subject, 'touser' => $touser, ]), "SendMail");
		if (ENVIRONMENT == "development") {
			return true;
		}
		if ($this->sendmail_mode == 'local') {
			return $this->send_mail_local($subject, $message, $touser, $cc, $bcc);
		} elseif ($this->sendmail_mode == 'sendinblue') {
			return $this->sendTransactionalEmail($subject, $message, $touser, $cc, $bcc, $attachments);
		}
	}

	function send_mail_local($subject, $message, $touser, $cc = '', $bcc = '') {

		$CI = &get_instance();
		$CI->load->library('email');

		$config['protocol'] = $this->protocol;
		if ($this->protocol == 'sendmail') {
			$config['mailpath'] = '/usr/sbin/sendmail';
		}
		if ($this->protocol == 'mail') {
			$config['smtp_host'] = $this->smtp_host;
			$config['smtp_port'] = $this->smtp_port;
			$config['smtp_user'] = $this->smtp_user;
			$config['smtp_pass'] = $this->smtp_pass;
		}

		$config['mailtype']  = 'html';
		$config['charset'] = 'iso-8859-1';

		$CI->email->initialize($config);
		$CI->email->from($this->from_id, $this->from_name);

		if ($bcc != '') {
			$CI->email->bcc($bcc);
		}
		if ($cc != '') {
			$CI->email->cc($cc);
		}
		$CI->email->to($touser);

		$CI->email->subject($subject);

		$CI->email->message($message);

		return $CI->email->send();
	}

	public function sendTransactionalEmail($subject, $message, $touser, $cc = [], $bcc, $attachments = []) {

		$log = "SendinBlueApi sendTransactionalEmail called";
		$apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(
			new GuzzleHttp\Client(),
			$this->config
		);

		$sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail([
			'subject' => $subject,
			'sender' => ['name' => $this->from_name, 'email' => $this->from_id],
			'replyTo' => ['name' => $this->from_name, 'email' => $this->reply_mail],
			'htmlContent' => $message,
			// 'params' => []
		]);

		// $sendSmtpEmail['to'] = $touser;
		$sendSmtpEmail['messageVersions'] = array_map(function ($user) {
			$version = [
				'to' => [[
					'name' => $user['name'],
					'email' => $user['email'],
				]],
			];
			if (isset($user['params'])) {
				$version['params'] = $user['params'];
			}
			return $version;
		}, $touser);

		if ($cc != []) {
			$sendSmtpEmail['cc'] = $cc;
		}

		if ($bcc != []) {
			$sendSmtpEmail['bcc'] = $bcc;
		}

		if ($attachments != []) {
			$log .= "\n\tAttachments: " . print_r($attachments, true);
			$sendSmtpEmail['attachment'] = array_map(function ($attachment) {
				$content = file_get_contents($attachment);
				return new \SendinBlue\Client\Model\SendSmtpEmailAttachment([
					'content' => base64_encode($content),
					'name' => basename($attachment),
				]);
			}, $attachments);
		}

		try {
			$result = $apiInstance->sendTransacEmail($sendSmtpEmail);
			$log .= "\n\tResponse: " . print_r($result, true);
			$this->writeLog($log);
			return $result;
		} catch (Exception $e) {
			$log .= "\n\tException when calling TransactionalEmailsApi->sendTransacEmail: " . $e->getMessage();
			$this->writeLog($log);
			return false;
		}
	}

	function mail_config() {
		$config['protocol'] = $this->protocol;
		if ($this->protocol == 'sendmail') {
			$config['mailpath'] = '/usr/sbin/sendmail';
		}
		if ($this->protocol == 'mail') {
			$config['smtp_host'] = $this->smtp_host;
			$config['smtp_port'] = $this->smtp_port;
			$config['smtp_user'] = $this->smtp_user;
			$config['smtp_pass'] = $this->smtp_pass;
		}

		$config['from_id'] = $this->from_id;
		$config['from_name'] = $this->from_name;
		$config['API_KEY'] = $this->API_KEY;
		$config['sendmail_mode'] = $this->sendmail_mode;
		return $config;
	}

	public function getAccount() {

		$log = "SendinBlueApi getAccount called";
		$apiInstance = new SendinBlue\Client\Api\AccountApi(
			new GuzzleHttp\Client(),
			$this->config
		);

		try {
			$result = $apiInstance->getAccount();
			$log .= "\n\tResponse: " . print_r($result, true);
			$this->writeLog($log);
			return $result;
		} catch (Exception $e) {
			$log .= "\n\t:Exception when calling AccountApi->getAccount " . $e->getMessage();
			$this->writeLog($log);
			return false;
		}
	}

	private $sms_templates = [
		'welcome_default' => "",
		'new_order' => "",
		'otp_default' => "",
	];
	private $sms_api_key = "";
	private $sms_sender_id = "";
	private $sms_username = "";
	private $sms_api_url = "";

	function send_sms($mobile, $content, $template) {
		return true;
		if (ENVIRONMENT != "production") {
			return true;
		}
		$template_content = $this->sms_templates[$template] ?? null;
		if ($template_content == null) {
			return [
				'status' => false,
				'error' => "Invalid template",
			];
		}
		$api_key = $this->sms_api_key;
		$sender_id = $this->sms_sender_id;
		$username = $this->sms_username;

		$sms_content = $template_content;
		foreach ($content as $var => $value) {
			$sms_content = str_replace("{#var" . $var . "#}", $value, $sms_content);
		}

		$url = $this->sms_api_url;
		$params = http_build_query([
			'username' => $username,
			'apikey' => $api_key,
			'mobile' => $mobile,
			'senderid' => $sender_id,
			'message' => $sms_content,
			// ]);
		], '', '&', PHP_QUERY_RFC1738);

		// $url_full = $url . '?' . join("&", $params);
		$url_full = $url . '?' . $params;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_full);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$status = $status_code == 200;

		$log = "Request: " . $url_full . "\nResponse: " . $response;
		$this->writeLog($log, "SMSAPI");

		return [
			'status' => $status,
			'sms_content' => $sms_content,
			'response' => $response,
		];
	}

	private function writeLog($data, $file = "SendinBlueApi") {
		$fileName = "$file-" . date("Y-m-d") . ".txt";
		$fp = fopen(dirname($_SERVER["SCRIPT_FILENAME"]) . "/logs/" . $fileName, 'a+');
		$data = date("Y-m-d H:i:s") . " - " . $data;
		fwrite($fp, $data);
		fwrite($fp, "\n");
		fclose($fp);
	}
}
