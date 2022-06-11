<?php

defined( 'ABSPATH' ) || exit;

class WoocommerceIR_SMS_Gateways {

	private static $_instance;
	public $mobile = [];
	public $message = '';
	public $senderNumber = '';
	private $username = '';
	private $password = '';

	public function __construct() {
		$this->username     = PWooSMS()->Options( 'sms_gateway_username' );
		$this->password     = PWooSMS()->Options( 'sms_gateway_password' );
		$this->senderNumber = PWooSMS()->Options( 'sms_gateway_sender' );
	}

	public static function init() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function get_sms_gateway() {

		$gateway = [
			'sunwaysms'          => 'SunwaySMS.com',
			'parandsms'          => 'ParandSMS.ir',
			'gamapayamak'        => 'GAMAPayamak.com',
			'limoosms'           => 'LimooSMS.com',
			'smsfa'              => 'SMSFa.ir',
			'aradsms'            => 'Arad-SMS.ir',
			'farapayamak'        => 'FaraPayamak.ir',
			'payamafraz'         => 'PayamAfraz.com',
			'niazpardaz'         => 'SMS.NiazPardaz.com',
			'niazpardaz_'        => 'Login.NiazPardaz.ir',
			'yektasms'           => 'Yektatech.ir',
			'smsbefrest'         => 'SmsBefrest.ir',
			'relax'              => 'Relax.ir',
			'paaz'               => 'Paaz.ir',
			'postgah'            => 'Postgah.info',
			'idehpayam'          => 'IdehPayam.com',
			'azaranpayamak'      => 'Azaranpayamak.ir',
			'smsir'              => 'SMS.ir',
			'manirani'           => 'Manirani.ir',
			'tjp'                => 'TJP.ir',
			'websms'             => 'S1.Websms.ir',
			'payamresan'         => 'Payam-Resan.com',
			'bakhtarpanel'       => 'Bakhtar.xyz',
			'parsgreen'          => 'ParsGreen.com',
			'avalpayam'          => 'Avalpayam.com',
			'iransmsserver'      => 'IranSmsServer.com',
			'melipayamak'        => 'MeliPayamak.com',
			'melipayamakpattern' => 'MeliPayamak.com خدماتی',
			'loginpanel'         => 'LoginPanel.ir',
			'smshooshmand'       => 'SmsHooshmand.com',
			'smsfor'             => 'SMSFor.ir',
			'chaparpanel'        => 'ChaparPanel.IR',
			'firstpayamak'       => 'FirstPayamak.ir',
			'netpaydar'          => 'SMS.Netpaydar.com',
			'smspishgaman'       => 'Panel.SmsPishgaman.com',
			'parsianpayam'       => 'ParsianPayam.ir',
			'hostiran'           => 'Hostiran.com',
			'iransms'            => 'IranSMS.co',
			'negins'             => 'Negins.com',
			'kavenegar'          => 'Kavenegar.com',
			'afe'                => 'Afe.ir',
			'aradpayamak'        => 'Aradpayamak.net',
			'isms'               => 'ISms.ir',
			'razpayamak'         => 'RazPayamak.com',
			'_0098'              => '0098SMS.com',
			'sefidsms'           => 'SefidSMS.ir',
			'chapargah'          => 'Chapargah.com',
			'hafezpayam'         => 'HafezPayam.com',
			'mehrpanel'          => 'MehrPanel.ir',
			'kianartpanel'       => 'KianArtPanel.ir',
			'farstech'           => 'Sms.FarsTech.ir',
			'berandet'           => 'Berandet.ir',
			'nicsms'             => 'NikSms.com',
			'asanak'             => 'Asanak.ir',
			'ssmss'              => 'SSMSS.ir',
			'hiro_sms'           => 'Hiro-Sms.com',
			'sabanovin'          => 'SabaNovin.com',
			'trez'               => 'SmsPanel.Trez.ir',
			'raygansms'          => 'RayganSms.com',
			'sepahansms'         => 'SepahanSms.com (SepahanGostar.com)',
			'_3300'              => 'Sms.3300.ir',
			'smsnegar'           => 'Sms.SmsNegar.com',
			'behsadade'          => 'Sms.BehsaDade.com',
			'flashsms'           => 'FlashSms.ir (AdminPayamak.ir)',
			'payamsms'           => 'PayamSms.com',
			'hadafwp'            => 'sms.hadafwp.Com',
			'mehrafraz'          => 'mehrafraz.com',
			'irpayamak'          => 'IRPayamak.Com',
			'gamasystems'        => 'Gama.systems',
			'smsmelli'           => 'SMSMelli.com',
			'smsmeli'            => 'SMS-Meli.com',
			'kavenegar_lookUp'   => 'Kavenegar.com(lookup)',
			'atlaspayamak'       => 'Atlaspayamak.ir',
			'parsiansms'         => 'Parsian-SMS.ir',
			'panelsms20'         => 'panelsms20.ir',
			'newsms'             => 'NewSMS.ir',
			'parsiantd'          => 'sms.parsiantd.com',
			'sahandsms'          => 'sahandsms.com',
			'aryana'             => 'PayamKotah.com',
			'npsms'              => 'npsms.com',
			'sornasms'           => 'sornasms.net',
			'TSMS'               => 'tsms.ir',
			'pardissms'          => 'pardis.ssmss.ir',
			'karenkart'          => 'karenkart.com',
			'nh1ir'          => 'nh1.ir',
		];

		return apply_filters( 'pwoosms_sms_gateways', $gateway );
	}

	public function tjp() {

		$username = $this->username;
		$password = $this->password;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		//$from     = $this->senderNumber;
		$to      = $this->mobile;
		$massage = $this->message;

		try {

			$client = new SoapClient( 'http://sms-login.tjp.ir/webservice/?WSDL', [
				'login'    => $username,
				'password' => $password,
			] );

			$client->sendToMany( $to, $massage );

		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}

		if ( empty( $sms_response ) ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function hostiran() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client       = new SoapClient( "http://37.228.138.118/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function mehrpanel() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client = new SoapClient( "http://87.107.121.52/post/send.asmx?wsdl" );

			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function hadafwp() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client = new SoapClient( "http://sms.hadafwp.com/Post/Send.asmx?wsdl" );

			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function parandsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client = new SoapClient( "http://87.107.121.52/post/send.asmx?wsdl" );

			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function aradsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client = new SoapClient( "http://arad-sms.ir/post/send.asmx?wsdl" );

			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function smsbefrest() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client = new SoapClient( "http://87.107.121.52/post/send.asmx?wsdl" );

			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function relax() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://onlinepanel.ir/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function farstech() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://sms.farstech.ir/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function kianartpanel() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://onlinepanel.ir/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function smspishgaman() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;
		$to       = $this->mobile;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$i = sizeOf( $to );
		while ( $i -- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '98' . $ret;
		}

		PWooSMS()->nusoap();

		try {
			$client                   = new nusoap_client( 'http://82.99.216.45/services/?wsdl', true );
			$client->soap_defencoding = 'UTF-8';
			$client->decode_utf8      = false;

			$results = $client->call( 'Send', [
				'username'  => $username,
				'password'  => $password,
				'srcNumber' => $from,
				'body'      => $massage,
				'destNo'    => $to,
				'flash'     => '0',
			] );

			$error = [];
			foreach ( $results as $result ) {
				if ( ! isset( $result['Mobile'] ) || stripos( $result['ID'], 'e' ) !== false ) {
					$error[] = $result;
				}
			}

			if ( empty( $error ) ) {
				return true; // Success
			}
		} catch ( Exception $e ) {
			$response = $e->getMessage();
		}

		return $response;
	}

	public function paaz() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://sms.paaz.ir/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function farapayamak() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client       = new SoapClient( "http://37.228.138.118/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function smsmeli() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client       = new SoapClient( "http://37.228.138.118/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function parsianpayam() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client     = new SoapClient( "http://onepayam.ir/API/Send.asmx?wsdl" );
			$encoding   = "UTF-8";
			$parameters = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'flash'    => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];

			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 0 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function iransmsserver() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://sms.iransmsserver.ir/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function niazpardaz() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://37.228.138.118/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function niazpardaz_() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://185.13.231.178/SendService.svc?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'userName'       => $username,
				'password'       => $password,
				'fromNumber'     => $from,
				'toNumbers'      => $to,
				'messageContent' => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'        => false,
				'udh'            => "",
				'recId'          => [ 0 ],
				'status'         => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSMSResult;

		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( strval( $sms_response ) == '0' ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function payamafraz() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://payamafraz.ir/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function yektasms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://37.228.138.118/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function gamapayamak() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://37.228.138.118/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function limoosms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://37.228.138.118/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function smsfa() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://smsfa.net/API/Send.asmx?WSDL' );
			$sms_response = $client->SendSms(
				[
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'flash'    => false,
					'udh'      => '',
				]
			)->SendSmsResult;
		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}
		if ( intval( $sms_response ) > 0 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function postgah() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://postgah.net/API/Send.asmx?WSDL' );
			$sms_response = $client->SendSms(
				[
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'flash'    => false,
					'udh'      => '',
				]
			)->SendSmsResult;
		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}
		if ( strval( $sms_response ) == '0' ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function azaranpayamak() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://azaranpayamak.ir/API/Send.asmx?WSDL' );
			$sms_response = $client->SendSms(
				[
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'flash'    => false,
					'udh'      => '',
				]
			)->SendSmsResult;
		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}
		if ( strval( $sms_response ) == '0' ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function manirani() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( 'http://sms.manirani.ir/API/Send.asmx?WSDL' );
			$sms_response = $client->SendSms(
				[
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'flash'    => false,
					'udh'      => '',
				]
			)->SendSmsResult;
		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}

		if ( intval( $sms_response ) > 0 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function smsir() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$content = 'user=' . rawurlencode( $username ) .
		           '&pass=' . rawurlencode( $password ) .
		           '&to=' . rawurlencode( $to ) .
		           '&lineNo=' . rawurlencode( $from ) .
		           '&text=' . $massage;

		$remote = wp_remote_get( 'https://ip.sms.ir/SendMessage.ashx?' . $content );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == 'ok' || stripos( $response, 'ارسال با موفقیت انجام شد' ) !== false ) {
			return true; // Success
		}

		return $response;
	}

	public function parsiansms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$content = 'uname=' . rawurlencode( $username ) .
		           '&pass=' . rawurlencode( $password ) .
		           '&to=' . rawurlencode( $to ) .
		           '&from=' . rawurlencode( $from ) .
		           '&msg=' . $massage ;

		$remote = wp_remote_get( 'http://185.4.31.182/class/sms/webservice/send_url.php?' . $content );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == 'ok' || stripos( $response, 'done' ) !== false ) {
			return true; // Success
		}

		return $response;
	}

	public function smsmelli() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$content = 'uname=' . rawurlencode( $username ) .
		           '&pass=' . rawurlencode( $password ) .
		           '&to=' . rawurlencode( $to ) .
		           '&from=' . rawurlencode( $from ) .
		           '&msg=' . $massage;

		$remote = wp_remote_get( 'http://185.4.31.182/class/sms/webservice/send_url.php?' . $content );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == 'ok' || stripos( $response, 'done' ) !== false ) {
			return true; // Success
		}

		return $response;
	}

	public function netpaydar() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$content = 'user=' . rawurlencode( $username ) .
		           '&pass=' . rawurlencode( $password ) .
		           '&to=' . rawurlencode( $to ) .
		           '&lineNo=' . rawurlencode( $from ) .
		           '&text=' . $massage ;

		$remote = wp_remote_get( 'http://sms.netpaydar.com/SendMessage.ashx?' . $content );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == '1' || stripos( $response, 'ارسال با موفقیت انجام شد' ) !== false ) {
			return true; // Success
		}

		return $response;
	}

	public function afe() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = [];

		foreach ( $this->mobile as $mobile ) {

			$remote = wp_remote_get( 'http://www.afe.ir/Url/SendSMS?username=' . $username . '&Password=' . $password . '&Number=' . $from . '&mobile=' . $mobile . '&sms=' . $massage );

			$response = wp_remote_retrieve_body( $remote );

			if ( empty( $response ) || stripos( $response, 'success' ) === false ) {
				$errors[] = $response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		}

		return $errors;
	}

	public function iransms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = [];

		foreach ( $this->mobile as $mobile ) {

			$remote = wp_remote_get( 'http://www.iransms.co/URLSend.aspx?Username=' . $username . '&Password=' . $password . '&PortalCode=' . $from . '&Mobile=' . $mobile . '&Message=' .$massage . '&Flash=0' );

			$response = wp_remote_retrieve_body( $remote );

			if ( abs( $response ) < 30 ) {
				$errors[] = $response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		} else {
			$response = $errors;
		}

		return $response;
	}

	public function negins() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = [];

		foreach ( $this->mobile as $mobile ) {

			$remote = wp_remote_get( 'http://negins.com/URLSend.aspx?Username=' . $username . '&Password=' . $password . '&PortalCode=' . $from . '&Mobile=' . $mobile . '&Message=' . urlencode( $massage ) . '&Flash=0' );

			$response = wp_remote_retrieve_body( $remote );

			if ( abs( $response ) < 30 ) {
				$errors[] = $response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		} else {
			$response = $errors;
		}

		return $response;
	}

	public function hafezpayam() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = [];

		foreach ( $this->mobile as $mobile ) {

			$remote = wp_remote_get( 'http://hafezpayam.com/URLSend.aspx?Username=' . $username . '&Password=' . $password . '&PortalCode=' . $from . '&Mobile=' . $mobile . '&Message=' . urlencode( $massage ) . '&Flash=0' );

			$response = wp_remote_retrieve_body( $remote );

			if ( abs( $response ) < 30 ) {
				$errors[] = $response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		} else {
			$response = $errors;
		}

		return $response;
	}

	public function smshooshmand() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = $this->mobile;

		PWooSMS()->nusoap();

		$client = new nusoap_client( "http://185.4.28.100/class/sms/webservice/server.php?wsdl" );

		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8      = true;
		$client->setCredentials( $username, $password, "basic" );

		$i = sizeOf( $to );
		while ( $i -- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '+98' . $ret;
		}

		$parameters = [
			'from'       => $from,
			'rcpt_array' => $to,
			'msg'        => $massage,
			'type'       => 'normal',
		];

		$result = $client->call( "enqueue", $parameters );
		if ( ( isset( $result['state'] ) && $result['state'] == 'done' ) && ( isset( $result['errnum'] ) && ( $result['errnum'] == '100' || $result['errnum'] == 100 ) ) ) {
			return true; // Success
		} else {
			$response = $result;
		}

		return $response;
	}

	public function smsfor() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}
		$to = $this->mobile;

		PWooSMS()->nusoap();

		$i = sizeOf( $to );
		while ( $i -- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '0' . $ret;
		}

		$client                   = new nusoap_client( 'http://www.smsfor.ir/webservice/soap/smsService.php?wsdl', 'wsdl' );
		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8      = false;

		$params = [
			'username'         => $username,
			'password'         => $password,
			'sender_number'    => [ $from ],
			'receiver_number'  => $to,
			'note'             => [ $massage ],
			'date'             => [],
			'request_uniqueid' => [],
			'flash'            => false,
			'onlysend'         => 'ok',
		];
		$md_res = $client->call( "send_sms", $params );

		if ( empty( $md_res['getMessage()'] ) && empty( $md_res['getMessage()'] ) && is_numeric( str_ireplace( ',', '', $md_res[0] ) ) ) {
			return true; // Success
		} else {
			$response = $md_res;
		}

		return $response;
	}

	public function idehpayam() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$soap = new SoapClient( "http://panel.idehpayam.com/webservice/send.php?wsdl" );

			$soap->Username = $username;
			$soap->Password = $password;
			$soap->fromNum  = $from;
			$soap->toNum    = $to;
			$soap->Content  = $massage;
			$soap->Type     = '0';

			$result = $soap->SendSMS( $soap->fromNum, $soap->toNum, $soap->Content, $soap->Type, $soap->Username, $soap->Password );

			if ( ! empty( $result[0] ) && $result[0] > 100 ) {
				return true; // Success
			} else {
				$response = $result;
			}

			return $response;

		} catch ( SoapFault $e ) {
			return $e->getMessage();
		}
	}

	public function websms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$content = 'cusername=' . rawurlencode( $username ) .
		           '&cpassword=' . rawurlencode( $password ) .
		           '&cmobileno=' . rawurlencode( $to ) .
		           '&csender=' . rawurlencode( $from ) .
		           '&cbody=' . $massage;

		$remote = wp_remote_get( 'http://s1.websms.ir/wservice.php?' . $content );

		$sms_response = wp_remote_retrieve_body( $remote );

		if ( strlen( $sms_response ) > 8 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function bakhtarpanel() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		PWooSMS()->nusoap();

		$client = new nusoap_client( 'http://login.bakhtar.xyz/webservice/server.asmx?wsdl' );

		$status = explode( ',', ( $client->call( 'Sendsms', [
			'4',
			$from,
			$username,
			$password,
			'98',
			$massage,
			$to,
			false,
		] ) ) );

		if ( count( $status ) > 1 && $status[0] == 1 ) {
			return true; // Success
		} else {
			$response = $status;
		}

		return $response;
	}

	public function melipayamakpattern() {
		$response = false;
		//$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}


		$textarray = explode( '##', $massage );
		$key       = array_pop( $textarray );
		if ( trim( $key ) == 'shared' ) {
			try {

				$client = new SoapClient( "https://api.payamak-panel.com/post/send.asmx?wsdl", [ 'encoding' => 'UTF-8' ] );
				//$encoding     = "UTF-8";
				$parameters   = [
					'username' => $username,
					'password' => $password,
					'to'       => implode( ",", $to ),
					'text'     => reset( $textarray ),

				];
				$sms_response = $client->SendByBaseNumber3( $parameters )->SendByBaseNumber3Result;
			} catch ( SoapFault $ex ) {
				$sms_response = $ex->getMessage();
			}
			if ( $sms_response > 20 ) {
				return true; // Success
			} else {
				$response = $sms_response;
			}
		} else {

			try {
				$client       = new SoapClient( "http://api.payamak-panel.com/post/send.asmx?wsdl" );
				$encoding     = "UTF-8";
				$parameters   = [
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'isflash'  => false,
					'udh'      => "",
					'recId'    => [ 0 ],
					'status'   => 0,
				];
				$sms_response = $client->SendSms( $parameters )->SendSmsResult;
			} catch ( SoapFault $ex ) {
				$sms_response = $ex->getMessage();
			}

			if ( $sms_response == 1 ) {
				return true; // Success
			} else {
				$response = $sms_response;
			}

		}

		return $response;
	}

	public function melipayamak() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://37.228.138.118/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function payamresan() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$url = 'http://www.payam-resan.com/APISend.aspx?UserName=' . rawurlencode( $username ) .
		       '&Password=' . rawurlencode( $password ) .
		       '&To=' . rawurlencode( $to ) .
		       '&From=' . rawurlencode( $from ) .
		       '&Text=' .  $massage;

		$remote = wp_remote_get( $url );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == '1' || $response == 1 ) {
			return true; // Success
		}

		return $response;

	}

	public function newsms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ',', $this->mobile );

		$url = 'http://newsms.ir/api/?action=SMS_SEND&username=' . rawurlencode( $username ) .
		       '&password=' . rawurlencode( $password ) .
		       '&API_CHANGE_ALLOW=true&to=' . rawurlencode( $to ) .
		       '&api=1&from=' . rawurlencode( $from ) .
		       '&FLASH=0&text=' . $massage;

		$remote = wp_remote_get( $url );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) == '1' || $response == 1 ) {
			return true; // Success
		}

		return $response;
	}

	public function kavenegar() {

		$username = $this->username;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) ) {
			return false;
		}

		$messages = urlencode( $massage );
		$to       = implode( ',', $this->mobile );

		$url = "https://api.kavenegar.com/v1/$username/sms/send.json?sender=$from&receptor=$to&message=$messages";

		$remote = wp_remote_get( $url );

		$response = wp_remote_retrieve_body( $remote );

		if ( false !== $response ) {
			$json_response = json_decode( $response );
			if ( ! empty( $json_response->return->status ) && $json_response->return->status == 200 ) {
				return true; // Success
			}
		}

		return $response;
	}

	public function parsgreen() {

		$username = $this->username;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) ) {
			return false;
		}

		$to = $this->mobile;


		$body = array(
            'SmsBody'    => $massage,
            'Mobiles'   => $to,
        );


		$args = array(
            'body'        => json_encode($body),
            'timeout'     => '45',
            'headers'     => array(
                "Content-Type"  => "application/json; charset=utf-8",
                "Accept"        => "application/json",
                "Authorization" => "basic apikey:" . $username,
            ),
            'data_format' => 'body',
        );

		try {

			$remote = wp_remote_post( 'http://sms.parsgreen.ir/Apiv2/Message/SendSms',$args );

			$response = json_decode( wp_remote_retrieve_body( $remote ) );
			

		} catch ( Exception $ex ) {
			return $response = "error";
		}

		if ( $response->R_Success ) {
			return $response = true;
		} else {
			$response = $response->R_Message;
		}

		return $response;
	}

	public function kavenegar_lookUp() {

		$response = false;
		$username = $this->username;
		//    $password = $this->password;
		$from    = $this->senderNumber;
		$massage = $this->message;
		if ( empty( $username ) ) {
			return $response;
		}


		$regex_template = '/(?<=template=)(.*?)(?=token\d*=|$)/is';

		$regex_tokens = '/(token=|token\d=|token\d\d=)/is';

		$regex_variables = '/(?<=token=|token\d=|token\d\d=)(.*?)(?=token\d*=|$|template)/is';

		preg_match_all( $regex_template, $massage, $template_matches, PREG_PATTERN_ORDER, 0 );
		preg_match_all( $regex_tokens, $massage, $tokens_matches, PREG_PATTERN_ORDER, 0 );
		preg_match_all( $regex_variables, $massage, $variables_matches, PREG_PATTERN_ORDER, 0 );

		$to = implode( ',', $this->mobile );


		$templateName = $template_matches[0][0];
		$tokensParam  = "";


		for ( $i = 0; $i <= count( $tokens_matches[0] ) - 1; $i ++ ) {
			$tokenName = $tokens_matches[0][ $i ];
			$lookupval = html_entity_decode( $variables_matches[0][ $i ] );

			if ( ( strcasecmp( $tokenName, 'token10=' ) != 0 ) && ( strcasecmp( $tokenName, 'token20=' ) != 0 ) ) {
				$lookupval = str_replace( ' ', '-', $lookupval );
			}

			$tokensParam .= "&" . $tokenName . rawurlencode( html_entity_decode( $lookupval, ENT_QUOTES, 'UTF-8' ) );

		}


		$templateName = trim( $templateName );

		$url = "http://api.kavenegar.com/v1/$username/verify/lookup.json?receptor=$to&template=$templateName" . $tokensParam;

		$remote = wp_remote_get( $url );

		$sms_response = wp_remote_retrieve_body( $remote );

		if ( false !== $sms_response ) {
			$json_response = json_decode( $sms_response );
			if ( ! empty( $json_response->return->status ) && $json_response->return->status == 200 ) {
				return true; // Success
			}
		}

		if ( $response !== true ) {
			$response = $sms_response;
		}

		return $response;

	}

	public function avalpayam() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}
		$to = $this->mobile;


		PWooSMS()->nusoap();

		$client = new nusoap_client( "http://www.avalpayam.com/class/sms/webservice/server.php?wsdl" );

		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8      = true;
		$client->setCredentials( $username, $password, "basic" );

		$i = sizeOf( $to );
		while ( $i -- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '+98' . $ret;
		}

		$parameters = [
			'from'       => $from,
			'rcpt_array' => $to,
			'msg'        => $massage,
			'type'       => 'normal',
		];

		$result = $client->call( "enqueue", $parameters );
		if ( ( isset( $result['state'] ) && $result['state'] == 'done' ) && ( isset( $result['errnum'] ) && ( $result['errnum'] == '100' || $result['errnum'] == 100 ) ) ) {
			return true; // Success
		} else {
			$response = $result;
		}

		return $response;
	}

	public function loginpanel() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://87.107.121.52/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function atlaspayamak() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://4646.ir/post/send.php?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function chaparpanel() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://87.107.121.52/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function firstpayamak() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client = new SoapClient( "http://ui.firstpayamak.ir/webservice/v2.asmx?WSDL" );
			$params = [
				'username'         => $username,
				'password'         => $password,
				'recipientNumbers' => $to,
				'senderNumbers'    => [ $from ],
				'messageBodies'    => [ $massage ],
			];

			$sms_response = $client->SendSMS( $params );
			$sms_response = (array) $sms_response->SendSMSResult->long;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( is_array( $sms_response ) ) {
			foreach ( array_filter( $sms_response ) as $send ) {
				if ( $send > 1000 ) {
					return true; // Success
				}
			}
		}

		if ( $response !== true ) {
			$response = $sms_response;
		}

		return $response;
	}

	public function aradpayamak() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;

		$massage = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( ';', $this->mobile );

		try {

			$client             = new SoapClient( "http://aradpayamak.net/APPs/SMS/WebService.php?wsdl" );
			$sendsms_parameters = [
				'domain'   => 'aradpayamak.net',
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => $massage,
				'isflash'  => 0,
			];

			$sms_response = call_user_func_array( [ $client, 'sendSMS' ], $sendsms_parameters );

			if ( ! empty( $sms_response ) ) {
				return true; // Success
			}

		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $response !== true ) {
			$response = $sms_response;
		}

		return $response;
	}

	public function isms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;

		$massage = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$data = [
			'username' => $username,
			'password' => $password,
			'mobiles'  => $this->mobile,
			'body'     => $massage,
			'sender'   => $from,
		];

		$remote = wp_remote_get( 'http://ws3584.isms.ir/sendWS?'. http_build_query( $data ));

		$response = wp_remote_retrieve_body( $remote );

		$result = json_decode( $response, true );

		if ( ! empty( $result["code"] ) && ! empty( $result["message"] ) ) {
			$response = $result;
		} else {
			return true; // Success
		}

		return $response;
	}
	
	public function nh1ir() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;

		$massage = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$data = [
			'Username' => $username,
			'Password' => $password,
			'To'  => $this->mobile,
			'Text'     => $massage,
			'From'   => $from,
		];

		$remote = wp_remote_get( 'http://ws.nh1.ir/Api/SMS/Send?'. http_build_query( $data ));

		$response = wp_remote_retrieve_body( $remote );

		$result = json_decode( $response, true );

		if ( ! empty( $result["code"] ) && ! empty( $result["message"] ) ) {
			return true;
		} else {
			return true; // Success
		}

		return $response;
	}

	public function razpayamak() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client       = new SoapClient( "http://37.228.138.118/post/send.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function _0098() {

		$username  = $this->username;
		$password  = $this->password;
		$from      = $this->senderNumber;
		$recievers = $this->mobile;
		$massage   = $this->message;
		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = [];

		foreach ( (array) $recievers as $to ) {

			$url = 'http://www.0098sms.com/sendsmslink.aspx?DOMAIN=0098' .
			       '&USERNAME=' . rawurlencode( $username ) .
			       '&PASSWORD=' . rawurlencode( $password ) .
			       '&FROM=' . rawurlencode( $from ) .
			       '&TO=' . rawurlencode( $to ) .
			       '&TEXT=' .  $massage ;

			$remote = wp_remote_get( $url );

			$sms_response = intval( wp_remote_retrieve_body( $remote ) );

			if ( $sms_response !== 0 ) {
				$errors[ $to ] = $sms_response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		} else {
			$response = $errors;
		}

		return $response;

	}

	public function parsiantd() {

		$username  = $this->username;
		$password  = $this->password;
		$from      = $this->senderNumber;
		$recievers = $this->mobile;
		$massage   = $this->message;
		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = [];

		foreach ( (array) $recievers as $to ) {

			$content = 'http://sms.parsiantd.com/Api-Services/sms_sender_url.php?' .
			           '&username=' . rawurlencode( $username ) .
			           '&password=' . rawurlencode( $password ) .
			           '&from=' . rawurlencode( $from ) .
			           '&to=' . rawurlencode( $to ) .
			           '&text=' . $massage ;

			$remote = wp_remote_get( $content );

			$sms_response = intval( wp_remote_retrieve_body( $remote ) );

			if ( $sms_response < 12 ) {
				$errors[ $to ] = $sms_response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		} else {
			$response = $errors;
		}

		return $response;

	}

	public function sefidsms() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			$client     = new SoapClient( "http://api.sefidsms.ir/post/send.asmx?wsdl" );
			$encoding   = "UTF-8";
			$parameters = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];

			$sms_response = $client->SendSms( $parameters )->SendSmsResult;

		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function chapargah() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$massage = iconv( 'UTF-8', 'UTF-8//TRANSLIT', $massage );

		try {
			$client       = new SoapClient( 'http://chapargah.com/API/Send.asmx?WSDL' );
			$sms_response = $client->SendSms(
				[
					'username' => $username,
					'password' => $password,
					'from'     => $from,
					'to'       => $to,
					'text'     => $massage,
					'flash'    => false,
					'recId'    => [ 0 ],
					'status'   => 0,
				]
			)->SendSmsResult;
		} catch ( SoapFault $sf ) {
			$sms_response = $sf->getMessage();
		}
		if ( strval( $sms_response ) == '0' ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function berandet() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}


		PWooSMS()->nusoap();

		$i = sizeOf( $to );
		while ( $i -- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '+98' . $ret;
		}

		$timeout                  = 1800;
		$response_timeout         = 180;
		$client                   = new nusoap_client( "http://berandet.ir/Modules/DevelopmentTools/Groups/Messaging/MessagingWbs.php?wsdl", true, false, false, false, false, $timeout, $response_timeout, '' );
		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8      = false;
		$client->response_timeout = $response_timeout;
		$client->timeout          = $timeout;

		$parameter = [
			'request' => [
				'username'   => $username,
				'password'   => $password,
				'fromNumber' => $from,
				'message'    => $massage,
				'recieptor'  => $to,
			],
		];

		$result = $client->call( 'sendMessageOneToMany', $parameter );
		$result = json_decode( $result, true );

		if ( ( isset( $result['errCode'] ) && $result['errCode'] < 0 ) || ! empty( $result['err'] ) ) {
			$response = $result;
		} else {
			return true; // Success
		}

		return $response;
	}

	public function nicsms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$param = [
			'username'     => $username,
			'password'     => $password,
			'message'      => $massage,
			'numbers'      => implode( ',', $to ),
			'senderNumber' => $from,
			'sendOn'       => date( 'yyyy/MM/dd-hh:mm' ),
			'sendType'     => 1,
		];

		$remote = wp_remote_post( "http://niksms.com/fa/PublicApi/GroupSms", [
			'body' => $param,
		] );

		$_response = wp_remote_retrieve_body( $remote );

		$_response = json_decode( $_response );
		$_response = ! empty( $_response->Status ) ? $_response->Status : 2;

		if ( $_response === 1 || strtolower( $_response ) == 'successful' ) {
			return true; // Success
		} else {
			$response = $_response;
		}

		return $response;
	}

	public function irpayamak() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $to );
		$to = str_ireplace( '+98', '0', $to );

		$url = 'http://irpayamak.com/API/SendSms.ashx?username=' . $username . '&password=' . $password . '&from=' . $from . '&to=' . $to . '&message=' . urlencode( trim( $massage ) );

		$remote = wp_remote_get( $url );

		$response = wp_remote_retrieve_body( $remote );

		if ( preg_match( '/\[.*\]/is', (string) $response ) ) {
			return true; // Success
		}

		return $response;
	}
	
	public function karenkart() {

        $username = $this->username;
        $password = $this->password;
        $from     = $this->senderNumber;
        $massage  = $this->message;

        if ( empty( $username ) || empty( $password ) ) {
            return false;
        }

        $to = implode( ',', $this->mobile );


        $remote = wp_remote_get( 'http://www.karenkart.com/Home/send_via_get?note='. $massage .'&username='. $username .'&password='. $password .'&receiver_number='. $to .'&sender_number='. $from.'');

        $response = wp_remote_retrieve_body( $remote );

        if ( ! empty( $response ) && $response >= 1 ) {
            return true; // Success
        }

        return $response;
    }

	public function sahandsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $to );
		$to = str_ireplace( '+98', '0', $to );

		$url = 'http://webservice.sahandsms.com/NewSMSWebService.asmx/SendFromUrl?username=' . $username . '&password=' . $password . '&fromNumber=' . $from . '&toNumber=' . $to . '&message=' . urlencode( trim( $massage ) );

		wp_remote_get( $url );

		return true;
	}

	public function aryana() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = [
			'user'    => rawurlencode( $username ),
			'pass'    => rawurlencode( $password ),
			'mobile'  => rawurlencode( $to ),
			'line'    => rawurlencode( $from ),
			'message' => $massage,
			'flash'   => 1,
		];

		$remote = wp_remote_get( 'http://www.payamkotah.ir/FastSendSMS.ashx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 2000 ) {
			return true; // Success
		}

		return $response;
	}

	public function npsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = [
			'userName'      => rawurlencode( $username ),
			'password'      => rawurlencode( $password ),
			'reciverNumber' => rawurlencode( $to ),
			'senderNumber'  => rawurlencode( $from ),
			'smsText'       => $massage,
			'domainName'    => 'npsms',
		];

		$remote = wp_remote_get( 'https://npsms.com/sendSmsViaURL.aspx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 1 ) {
			return true; // Success
		}

		return $response;
	}

	public function sornasms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = [
			'username'     => $username,
			'pass'         => $password,
			'mobile'       => $to,
			'senderNumber' => $from,
			'message'      => $massage,
			'code'         => 10260,
		];

		$remote = wp_remote_get( 'https://sornasms.net/getCustomer.aspx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 1 ) {
			return true; // Success
		}

		return $response;
	}

	public function asanak() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $to );
		$to = str_ireplace( '+98', '0', $to );

		$data = [
			'username'    => $username,
			'password'    => $password,
			'destination' => $to,
			'source'      => $from,
			'message'     => $massage,
		];

		$remote = wp_remote_get( 'http://panel.asanak.ir/webservice/v1rest/sendsms?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( preg_match( '/\[.*\]/is', (string) $response ) ) {
			return true; // Success
		}

		return $response;
	}

	public function ssmss() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to      = implode( ",", $to );
		$massage = urlencode( $massage );

		try {

			$param = "login_username=$username&login_password=$password&receiver_number=$to&note_arr=$massage&sender_number=$from";

			$remote = wp_remote_get( "http://ssmss.ir/webservice/rest/sms_send?{$param}" );

			$response = json_decode( wp_remote_retrieve_body( $remote ) );

			if ( isset( $results->error ) ) {
				$response = $results->error;
			} elseif ( ! empty( $results->result ) && $results->result && ! empty( $results->list ) ) {
				return true; // Success
			}

			return $response;

		} catch ( Exception $ex ) {
			return $ex->getMessage();
		}
	}

	public function hiro_sms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$soap = new SoapClient( "http://my.hiro-sms.com/wbs/send.php?wsdl" );

			$soap->Username = $username;
			$soap->Password = $password;
			$soap->fromNum  = $from;
			$soap->toNum    = $to;
			$soap->Content  = $massage;
			$soap->Type     = '0';

			$result = $soap->SendSMS( $soap->fromNum, $soap->toNum, $soap->Content, $soap->Type, $soap->Username, $soap->Password );

			if ( ! empty( $result[0] ) && $result[0] > 100 ) {
				return true; // Success
			} else {
				$response = $result;
			}

			return $response;

		} catch ( SoapFault $e ) {
			return $e->getMessage();
		}
	}

	public function sabanovin() {

		$api_key = $this->username;
		$from    = $this->senderNumber;
		$to      = $this->mobile;
		$massage = $this->message;

		if ( empty( $api_key ) ) {
			return false;
		}

		$data = [
			'gateway' => $from,
			'to'      => implode( ",", $to ),
			'text'    => urlencode( $massage ),
		];

		$remote = wp_remote_get( "https://api.sabanovin.com/v1/{$api_key}/sms/send.json?" . http_build_query( $data ) );

		$response = json_decode( wp_remote_retrieve_body( $remote ) );

		if ( ! empty( $response->status->code ) && $response->status->code == 200 ) {
			return true; // Success
		} else if ( ! empty( $response->status->message ) ) {
			$response = $response->status->code . ":" . $response->status->message;
		}

		return $response;
	}

	public function trez() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = [
			'Smsclass'    => 1,
			'Username'    => rawurlencode( $username ),
			'Password'    => rawurlencode( $password ),
			'RecNumber'   => rawurlencode( $to ),
			'PhoneNumber' => rawurlencode( $from ),
			'MessageBody' => $massage,
		];

		$remote = wp_remote_get( 'http://smspanel.trez.ir/SendGroupMessageWithUrl.ashx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 2000 ) {
			return true; // Success
		}

		return $response;
	}

	public function raygansms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = [
			'Smsclass'    => 1,
			'Username'    => rawurlencode( $username ),
			'Password'    => rawurlencode( $password ),
			'RecNumber'   => rawurlencode( $to ),
			'PhoneNumber' => rawurlencode( $from ),
			'MessageBody' => $massage,
		];

		$remote = wp_remote_get( 'http://smspanel.trez.ir/SendGroupMessageWithUrl.ashx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 2000 ) {
			return true; // Success
		}

		return $response;
	}

	public function sepahansms() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client = new SoapClient( "http://www.sepahansms.com/smsSendWebServiceforphp.asmx?wsdl" );

			$sms_response = $client->SendSms( [
				'UserName'     => $username,
				'Pass'         => $password,
				'Domain'       => 'sepahansms',
				'SmsText'      => [ $massage ],
				'MobileNumber' => $to,
				'SenderNumber' => $from,
				'sendType'     => 'StaticText',
				'smsMode'      => 'SaveInPhone',
			] )->SendSmsResult->long;

			if ( is_array( $sms_response ) || $sms_response > 1000 ) {
				return true; // Success
			}

		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $response !== true ) {
			$response = $sms_response;
		}

		return $response;
	}

	public function _3300() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;


		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = $this->mobile;
		PWooSMS()->nusoap();

		try {
			$client                   = new nusoap_client( 'http://sms.3300.ir/almassms.asmx?wsdl', 'wsdl', '', '', '', '' );
			$client->soap_defencoding = 'UTF-8';
			$client->decode_utf8      = true;

			$param  = [
				'pUsername' => $username,
				'pPassword' => $password,
				'line'      => $from,
				'messages'  => [ 'string' => ( $massage ) ],
				'mobiles'   => [ 'string' => $to ],
				'Encodings' => [ 'int' => 2 ],
				'mclass'    => [ 'int' => 1 ],
			];
			$result = $client->call( "Send", $param );
			$result = isset( $result['SendResult'] ) ? $result['SendResult'] : 0;

			if ( $result < 0 ) {
				return true; // Success
			} else {
				$response = $result;
			}

		} catch ( Exception $ex ) {
			$response = $ex->getMessage();
		}

		return $response;
	}

	public function pardissms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = [
			'username'        => rawurlencode( $username ),
			'password'        => rawurlencode( $password ),
			'receiver_number' => rawurlencode( $to ),
			'sender_number'   => rawurlencode( $from ),
			'note'            => $massage,
		];

		$remote = wp_remote_get( 'http://pardis.ssmss.ir/send_via_get/send_sms.php?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 8 ) {
			return true; // Success
		}

		return $response;
	}

	public function gamasystems() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to = implode( '-', $this->mobile );

		$data = [
			'username' => rawurlencode( $username ),
			'password' => rawurlencode( $password ),
			'to'       => rawurlencode( $to ),
			'from'     => rawurlencode( $from ),
			'text'     => $massage,
		];

		$remote = wp_remote_get( 'http://sms.gama.systems/url/post/SendSMS.ashx?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( ! empty( $response ) && $response >= 11 ) {
			return true; // Success
		}

		return $response;
	}

	public function smsnegar() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}


		$domain = 'yazd';//todo: smspanel.eshare.ir

		try {

			$client = new SoapClient( "http://sms.smsnegar.com/webservice/Service.asmx?wsdl" );

			$result = $client->SendSms( [
				"cUserName"     => $username,
				"cPassword"     => $password,
				"cDomainname"   => $domain,
				"cBody"         => $massage,
				"cSmsnumber"    => $to,
				"cGetid"        => "0",
				"nCMessage"     => "1",
				"nTypeSent"     => "1",
				"m_SchedulDate" => "",
				"nSpeedsms"     => "0",
				"nPeriodmin"    => "0",
				"cstarttime"    => "",
				"cEndTime"      => "",
			] );

			if ( ! empty( $result->SendSmsResult ) ) {

				$result  = $result->SendSmsResult;
				$results = explode( ',', $result );
				unset( $result );

				foreach ( $results as $result ) {
					if ( intval( $result ) < 1000 ) {
						$result       = $client->ShowError( [ "cErrorCode" => $result, "cLanShow" => "FA" ] );
						$sms_response = ! empty( $result->ShowErrorResult ) ? $result->ShowErrorResult : $results;
						break;
					}
				}
			} else {
				$sms_response = 'unknown';
			}
		} catch ( Exception $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( empty( $sms_response ) ) {
			return true; // Success
		}

		return $sms_response;
	}

	public function mehrafraz() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}


		try {

			$client = new SoapClient( "http://www.mehrafraz.com/webservice/service.asmx?wsdl" );

			$result = $client->SendSms( [
				"cUserName"     => $username,
				"cPassword"     => $password,
				"cDomainname"   => $from,
				"cBody"         => $massage,
				"cSmsnumber"    => implode( ',', $to ),
				"cGetid"        => "0",
				"nCMessage"     => "1",
				"nTypeSent"     => "1",
				"m_SchedulDate" => "",
				"nSpeedsms"     => "0",
				"nPeriodmin"    => "0",
				"cstarttime"    => "",
				"cEndTime"      => "",
			] );

			if ( ! empty( $result->SendSmsResult ) ) {

				$result  = $result->SendSmsResult;
				$results = explode( ',', $result );
				unset( $result );

				foreach ( $results as $result ) {
					if ( intval( $result ) < 1000 ) {
						$result       = $client->ShowError( [ "cErrorCode" => $result, "cLanShow" => "FA" ] );
						$sms_response = ! empty( $result->ShowErrorResult ) ? $result->ShowErrorResult : $results;
						break;
					}
				}
			} else {
				$sms_response = 'unknown';
			}
		} catch ( Exception $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( empty( $sms_response ) ) {
			return true; // Success
		}

		return $sms_response;
	}

	public function behsadade() {

		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;


		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$to       = implode( ",", $to );
		$massage  = urlencode( $massage );
		$username = urlencode( $username );
		$password = urlencode( $password );
		$from     = urlencode( $from );

		try {

			$data = [
				'login_username'  => $username,
				'login_password'  => $password,
				'receiver_number' => $to,
				'note_arr'        => $massage,
				'sender_number'   => $from,
			];

			$remote = wp_remote_get( 'http://sms.behsadade.com/webservice/rest/sms_send?' . http_build_query( $data ) );

			$response = json_decode( wp_remote_retrieve_body( $remote ) );

			if ( isset( $results->error ) ) {
				$response = $results->error;
			} elseif ( ! empty( $results->result ) && $results->result && ! empty( $results->list ) ) {
				return true; // Success
			}

			return $response;

		} catch ( Exception $ex ) {
			return $ex->getMessage();
		}
	}

	public function panelsms20() {
		$response = false;
		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {
			foreach ( $to as $key => $value ) {
				// $arr[3] will be updated with each value from $arr...

				$param    = [
					'userName'    => $username,
					'password'    => $password,
					'msg'         => $massage,
					'from'        => $from,
					'to'          => $value,
					'isFlashSend' =>
						false,
				];
				$client   = new SoapClient( "http://panelsms20.ir/services/SMSServices.asmx?WSDL" );
				$response = $client->Send( $param );
			}


			$sms_response = $response;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function flashsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$to       = $this->mobile;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		try {

			$client       = new SoapClient( "http://flashsms.ir/smssendwebserviceforcms.asmx?wsdl" );
			$encoding     = "UTF-8";
			$parameters   = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $to,
				'text'     => iconv( $encoding, 'UTF-8//TRANSLIT', $massage ),
				'isflash'  => false,
				'udh'      => "",
				'recId'    => [ 0 ],
				'status'   => 0,
			];
			$sms_response = $client->SendSms( $parameters )->SendSmsResult;
		} catch ( SoapFault $ex ) {
			$sms_response = $ex->getMessage();
		}

		if ( $sms_response == 1 ) {
			return true; // Success
		} else {
			$response = $sms_response;
		}

		return $response;
	}

	public function payamsms() {

		$response   = false;
		$username   = $this->username;
		$password   = $this->password;
		$from       = $this->senderNumber;
		$massage    = $this->message;
		$to         = $this->mobile;
		$orginpayam = 'sazmansht';

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$i = sizeOf( $to );
		while ( $i -- ) {
			$uNumber = trim( $to[ $i ] );
			$ret     = &$uNumber;
			if ( substr( $uNumber, 0, 3 ) == '%2B' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '%2b' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 4 ) == '0098' ) {
				$ret = substr( $uNumber, 4 );
			}
			if ( substr( $uNumber, 0, 3 ) == '098' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 3 ) == '+98' ) {
				$ret = substr( $uNumber, 3 );
			}
			if ( substr( $uNumber, 0, 2 ) == '98' ) {
				$ret = substr( $uNumber, 2 );
			}
			if ( substr( $uNumber, 0, 1 ) == '0' ) {
				$ret = substr( $uNumber, 1 );
			}
			$to[ $i ] = '98' . $ret;
		}

		PWooSMS()->nusoap();

		try {
			$client                   = new nusoap_client( 'https://new.payamsms.com/services/v2/?wsdl', true );
			$client->soap_defencoding = 'UTF-8';
			$client->decode_utf8      = false;

			$results = $client->call( 'Send', [
				'organization' => $orginpayam,
				'username'     => $username,
				'password'     => $password,
				'srcNumber'    => $from,
				'body'         => $massage,
				'destNo'       => $to,
				'flash'        => '0',
			] );

			$error = [];
			foreach ( $results as $result ) {
				if ( ! isset( $result['Mobile'] ) || stripos( $result['ID'], 'e' ) !== false ) {
					$error[] = $result;
				}
			}

			if ( empty( $error ) ) {
				return true; // Success
			} else {
				foreach ( $results as $value ) {

					print_r( $value );
				}
			}

		} catch ( Exception $e ) {
			$response = $e->getMessage();
		}

		return $response;
	}

	public function tsms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		

		$data = [
			'from'     => rawurlencode( $from ),
			'to'       => rawurlencode( $this->mobile ),
			'username' => rawurlencode( $username ),
			'password' => rawurlencode( $password ),
			'message'  => $massage,
		];

		$remote = wp_remote_get( 'http://tsms.ir/url/tsmshttp.php?' . http_build_query( $data ) );

		$response = wp_remote_retrieve_body( $remote );

		if ( strtolower( $response ) > '20' ) {
			return true; // Success
		}

		return $response;
	}

	public function sunwaysms() {

		$username = $this->username;
		$password = $this->password;
		$from     = $this->senderNumber;
		$massage  = $this->message;

		if ( empty( $username ) || empty( $password ) ) {
			return false;
		}

		$errors = [];

		foreach ( $this->mobile as $mobile ) {

			$data = [
				'username' => $username,
				'password' => $password,
				'from'     => $from,
				'to'       => $mobile,
				'message'  => urlencode( $massage ),
			];

			$remote = wp_remote_get( 'http://sms.sunwaysms.com/SMSWS/HttpService.ashx?' . http_build_query( $data ) );

			$response = wp_remote_retrieve_body( $remote );

			if ( empty( $response ) || $response < 10000 ) {
				$errors[] = $response;
			}
		}

		if ( empty( $errors ) ) {
			return true; // Success
		}

		return $errors;
	}
}