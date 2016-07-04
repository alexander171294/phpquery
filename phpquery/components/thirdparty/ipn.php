<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../../index.html');
	die();
}

_::declare_component('property');

class ipn
{
	// usamos mi trait Property 
	use Property;
	
	// atributos privados
	private $notify_validate_request_content = null;
	private $notify_validate_request_header = null;
	
	// variables.
	private $item_name = null; // nombre del producto
	private $payment_status = null; // estado del pago
	private $payment_amount = null; // precio pagado
	private $payment_currency = null; // ?
	private $transaction_id = null; // id de transacción de paypal
	private $receiver_email = null; // nuestro mail
	private $client_email = null; // mail del que compró
	
	private $error = false;
	private $error_msg = null;
	
	
	public function __construct()
	{
		$this->vars_extract();
		$this->notify_validate_request_content = $this->request_content_generate();
		$this->notify_validate_request_header = $this->request_header_generate();
		$this->notify_validate_request();			
	}
	
	private function request_content_generate()
	{
		$req = 'cmd=_notify-validate';
		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}
		return $req;
	}
	
	private function request_header_generate()
	{
		$header = null;
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($this->notify_validate_request_content) . "\r\n\r\n";
		return $header;
	}
	
	private function notify_validate_request()
	{
		try 
		{
			$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
			if(!$fp) throw new exception('No es posible conectar con www.paypal.com');
			fputs ($fp, $this->notify_validate_request_header . $this->notify_validate_request_content);
			while (!feof($fp)) 
			{
				$res = fgets ($fp, 1024);
				if (strcmp ($res, "VERIFIED") == 0)
				{
					return true;
				}
				else
				{
					throw new exception('Ocurrió un error con la transaccion');
				}
			}
		} catch(Exception $e)
		{
			$this->error = true;
			$this->error_msg = $e->getMessage();
			return false;
		}		
	}
	
	private function vars_extract()
	{
		$this->item_name = $_REQUEST['item_name'];
		$this->payment_status = $_REQUEST['payment_status'];
		$this->payment_amount = $_REQUEST['mc_gross'];
		$this->payment_currency = $_REQUEST['mc_currency'];
		$this->transaction_id = $_REQUEST['txn_id'];
		$this->receiver_email =  $_REQUEST['receiver_email'];
		$this->client_email = $_REQUEST['payer_email'];
	}
	
	public function get_item_name() { return $this->item_name; }
	public function set_item_name($value) { ; }
	
	public function get_payment_status() { return $this->payment_status; }
	public function set_payment_status($value) { ; }
	
	public function get_payment_amount() { return $this->payment_amount; }
	public function set_payment_amount($value) { ; }
	
	public function get_payment_currency() { return $this->payment_currency; }
	public function set_payment_currency($value) { ; }
	
	public function get_transaction_id() { return $this->transaction_id; }
	public function set_transaction_id($value) { ; }
	
	public function get_receiver_email() { return $this->receiver_email; }
	public function set_receiver_email($value) { ; }
	
	public function get_client_email() { return $this->client_email; }
	public function set_client_email($value) { ; }
}