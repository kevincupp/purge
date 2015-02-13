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
			/*$parsed_url = parse_url($site_url);
			$url_path = isset($parsed_url['path']) ? $parsed_url['path'] : '/';
			$url_scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : $protocol;
			$purge_url = $url_scheme . $parsed_url['host'] . $url_path;
			$port = ( ! isset($parsed_url['port']) || empty($parsed_url['port'])) ? 80 : $parsed_url['port'];*/
		}
		
		if (empty($port))
		{
			$port = 80;
		}
		
		echo $purge_url; 
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $purge_url);
		curl_setopt($ch, CURLOPT_PORT , (int)$port);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Host: '.$_SERVER['SERVER_NAME'] ) );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'PURGE');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		if(curl_exec($ch) === false)
			die( curl_error ( $ch ) ); 
		curl_close ($ch);
	}
}

?>
