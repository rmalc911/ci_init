<?php
include_once FCPATH . "vendor/autoload.php";

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
		$CI->load->model('AdminModel');
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

	public function send_mail($subject, $message, $touser, $cc = [], $bcc = []) {
		if ($this->sendmail_mode == 'local') {
			return $this->send_mail_local($subject, $message, $touser, $cc, $bcc);
		} elseif ($this->sendmail_mode == 'sendinblue') {
			return $this->sendTransactionalEmail($subject, $message, $touser, $cc, $bcc);
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

	public function sendTransactionalEmail($subject, $message, $touser, $cc = [], $bcc) {

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

	function send_sms($mobile, $sms) {
		return true;
		$sms = urlencode($sms);

		$url = "";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$data = curl_exec($ch);

		curl_close($ch);

		return $data;
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
