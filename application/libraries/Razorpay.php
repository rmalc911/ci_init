<?php

class Razorpay {

    private $url = 'https://api.razorpay.com/v1/';

    private $key_id = '';
    private $key_secret = '';

    private $mode = "test";

    /**
     * @param string $key_id Key ID
     * @param string $key_secret Key Secret
     */
    public function set_keys($key_id, $key_secret) {
        $this->key_id = $key_id;
        $this->key_secret = $key_secret;
    }

    public function get_keyid() {
        return $this->key_id;
    }

    public function capture($id, $amount) {
        $post = array('amount' => $amount * 100);
        $this->writeLog('Capture Request>>');
        return $this->send_request("payments/{$id}/capture", $post, "POST");
    }

    public function get_data($param) {
        $this->writeLog('Get Payments List>>');
        return $this->send_request("payments", $param);
    }

    public function fetch_data($razorpayId) {
        $this->writeLog('Get Payment Details>>');
        return $this->send_request("payments/" . $razorpayId);
    }

    public function create_order($order_data) {
        $post = [
            "amount" => $order_data['amount'],
            "currency" => "INR",
            "receipt" => $order_data['order_id'],
            "notes" => $order_data['notes'],
            "payment" => [
                "capture" => "automatic",
                "capture_options" => [
                    "automatic_expiry_period" => 12,
                    "manual_expiry_period" => 7200,
                    "refund_speed" => "optimum"
                ]
            ]
        ];
        $this->writeLog('Create Order>>');
        return $this->send_request("orders", $post, "POST");
    }

    public function create_payment_link($details) {
        return $this->send_request("payment_links", $details, "POST");
    }

    public function get_payment_link_details($link_id) {
        return $this->send_request("payment_links/" . $link_id);
    }

    public function refund_payment($pay_id, $amount = null, $notes = [], $receipt = "") {
        $post['amount'] = $amount;
        $post['notes'] = $notes;
        $post['receipt'] = $receipt;
        $this->writeLog('Refund Payment>>');
        return $this->send_request("payments/$pay_id/refund", $post, "POST");
    }

    public function manageContact($post, $updateId = "") {
        $this->writeLog('Manage Contact>>');
        return $this->send_request("contacts/" . $updateId, $post, $updateId == "" ? "POST" : "PATCH");
    }

    public function manageFundAccount($post) {
        $this->writeLog('Manage Fund Account>>');
        return $this->send_request("fund_accounts", $post, "POST");
    }

    public function generatePayOut($post) {
        $this->writeLog('Generate Payout>>');
        return $this->send_request("payouts", $post, "POST");
    }

    public function getPayOut($transactionId) {
        $this->writeLog('Get Payout>>');
        return $this->send_request("payouts/" . $transactionId);
    }

    /**
     * send_request
     *
     * @param string $path
     * @param array|null $data
     * @param string $http
     *
     * @return array|null
     */
    private function send_request(string $path, array $data = null, string $http = "GET"): ?array {
        $url = $this->url . $path;
        $auth = base64_encode($this->key_id . ':' . $this->key_secret);
        $data_string = json_encode($data);
        $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Basic ' . $auth, 'Content-Length: ' . strlen($data_string));
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $jsonObj = curl_exec($ch);
        curl_close($ch);
        $this->writeLog('Request :' . $url . ' Method: ' . $http . ' Data: ' . $data_string);
        $this->writeLog('Response:' . $jsonObj);
        if ($this->isJson($jsonObj) == 1 && $jsonObj != '' && $jsonObj != 'null') {
            $dataArr = json_decode($jsonObj, true);
            return $dataArr;
        }
        return null;
    }

    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    function writeLog($data) {
        $fileName = "Razorpay-" . date("Y-m-d") . ".txt";
        $fp = fopen(FCPATH . "/logs/" . $fileName, 'a+');
        $data = date("Y-m-d H:i:s") . " - " . $data;
        fwrite($fp, $data);
        fwrite($fp, "\n");
        fclose($fp);
    }
}
