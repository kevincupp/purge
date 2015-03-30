<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('send_purge_request'))
{
	/**
	 * Sends purge request to Varnish through CURL
	 */
	function send_purge_request($site_url = NULL, $site_port = NULL)
	{
		$protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		
		if (empty($site_url))
		{
			$purge_url = $protocol . $_SERVER['HTTP_HOST'] . '/';
			$port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : $site_port;
		}
		else
		{
			$purge_url = $site_url;
			$port = $site_port;
		}
		
		if (empty($port))
		{
			$port = 80;
		}
	
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $purge_url);
		curl_setopt($ch, CURLOPT_PORT , (int)$port);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Host: '.$_SERVER['SERVER_NAME'] ) );
		curl_setopt($ch, CURLOPT_ENCODING , "gzip");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'PURGE');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,2); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 4);
		$resp = curl_exec($ch);
		
		if(curl_errno($ch) > 1) //should be 0 but for some reason my curl complains about error 1, protocol unrecognised but request still processes.
			$resp = curl_error($ch);
				
		curl_close ($ch);
		
		preg_match("/<p>([^<]*)<\/p>/",$resp,$resp); //extract the core message from typical varnish response. 
		
		return $resp[1];
	}
}

?>
