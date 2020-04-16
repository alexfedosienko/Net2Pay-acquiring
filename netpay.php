<?php
/**
 * Платежный шлюз Net2Pay
 *
 * @link   https://github.com/alexfedosienko/Net2Pay-acquiring
 * @version 1.0
 * @author Alexander Fedosienko <alexfedosienko@gmail.com>
 */

namespace AFedosienko;
class NetPay
{
	private $api_key;
	private $auth;
	private $payment_expire_days;
	private $url;
	private $successUrl;
	private $failUrl;

	public function __construct($api_key, $auth, $successUrl, $failUrl, $expire_days = 1, $dev = false)
	{
		if ($dev) {
			$this->url = 'https://demo.net2pay.ru/billingService/paypage/';
			$this->api_key = 'js4cucpn4kkc6jl1p95np054g2';
			$this->auth = 1;
		} else {
			$this->url = 'https://my.net2pay.ru/billingService/paypage/';
			$this->api_key = $api_key;
			$this->auth = $auth;	
		}
		$this->successUrl = $successUrl;
		$this->failUrl = $failUrl;
		$this->payment_expire_days = date("Y-m-dVH:i:s", strtotime('+'.$expire_days.' day'));
	}

	public function getPaymentData($amount, $order_id, $description, $phone = "", $email = "")
	{
		$params = array(
			'amount' => $amount,
			'orderID' => $order_id,
			'currency' => 'RUB',
			'description' => $description,
			'orderNumber' => $order_id,
			'successUrl' => $this->successUrl,
			'failUrl' => $this->failUrl,
			'phone' => $phone,
			'email' => $email,
		);
		$md5_api_key = base64_encode(md5($this->api_key, true));
		$crypto_key = substr(base64_encode(md5($md5_api_key.$this->payment_expire_days, true)), 0, 16);
		$data = array();
		foreach ($params as $key => $value) {
			$item = $key."=".$value;
			$cipher = "AES-128-ECB";
			if (in_array($cipher, openssl_get_cipher_methods())) {
				$ivlen = openssl_cipher_iv_length($cipher);
				$iv = openssl_random_pseudo_bytes($ivlen);
				$data[] = openssl_encrypt($item, $cipher, $crypto_key, $options=0, $iv, $tag);
			}
		}
		$data = implode("&", $data);
		// return array('auth' => $this->auth, 'data' => urlencode($data), 'expire' => urlencode($this->payment_expire_days), 'url' => $this->url);
		return array('auth' => $this->auth, 'data' => $data, 'expire' => $this->payment_expire_days, 'url' => $this->url);
	}

	public function getForm($amount, $order_id, $description, $phone = "", $email = "")
	{
		$data = $this->getPaymentData($amount, $order_id, $description, $phone, $email);
		$form = '<form action="'.$data['url'].'" method="POST">';
		$form .= '	<input type="hidden" name="data" value="'.urlencode($data['data']).'">';
		$form .= '	<input type="hidden" name="auth" value="'.$data['auth'].'">';
		$form .= '	<input type="hidden" name="expire" value="'.urlencode($data['expire']).'">';
		$form .= '	<input type="submit">';
		$form .= '</form>';
		return $form;
	}
}