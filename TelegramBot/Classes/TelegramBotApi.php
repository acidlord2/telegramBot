<?php
/**
 *
 * @class TelegramBotApi
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class TelegramBotApi
{
	
	static private $token;
	static private $url;
	static private $log;
	
    static public function postData($method, $postdata)
	{
	    if (!isset(self::$log))
	    {
	        self::$log = new Log('TelegramBot - Classes - TelegramBotApi.log');
	    }
	    
	    if (!isset(self::$token))
		{
			// Fetch parameter ms_user
		    self::$token = Settings::get('telegramToken')[0]['value'];
		}
		self::$log->write(__LINE__ . ' token - ' . self::$token);
		
		if (!isset(self::$url))
		{
		    // Fetch parameter ms_user
		    self::$url = Settings::get('telegramUrl')[0]['value'];
		}
		self::$log->write(__LINE__ . ' url - ' . self::$url);
		
		$serviceUrl = self::$url . self::$token . '/' . $method;
		self::$log->write(__LINE__ . ' serviceUrl - ' . $serviceUrl);
		self::$log->write(__LINE__ . ' postdata - ' . json_encode($postdata, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		
		$header = array (
		    'Content-type: application/json'
		);
		
		$curl = curl_init($serviceUrl);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postdata));
		//var_dump(json_encode($postdata, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		$jsonOut = curl_exec($curl);
		$arrayOut = json_decode ($jsonOut, true);
		curl_close($curl);
		
		self::$log->write(__LINE__ . ' arrayOut - ' . json_encode($arrayOut, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		
		return $arrayOut;
	}
}

?>