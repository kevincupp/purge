<?php

namespace KevinCupp\Addons\Purge\Service;

/**
 * Varnish Service
 */
class Varnish {

	/**
	 * Sends a PURGE request to the specified URL
	 *
	 * @param string $purge_url Full URL to send the Purge request to
	 * @return string Response text from request
	 */
	public function purge($purge_url)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $purge_url);
		curl_setopt($ch, CURLOPT_PORT , $this->getPort());
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Host: '.$_SERVER['SERVER_NAME']]);
		curl_setopt($ch, CURLOPT_ENCODING , "gzip");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PURGE');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 4);
		$resp = curl_exec($ch);

		if (curl_errno($ch) > 1) $resp = curl_error($ch);

		curl_close($ch);

		// Extract message from Varnish response
		preg_match("/<p>([^<]*)<\/p>/", $resp, $parsed);

		return isset($parsed[1]) ? $parsed[1] : 'Unexpected response: '. $resp;
	}

	/**
	 * Gets the port on which Varnish is listening; if it's not specified in the
	 * config, we'll try to infer it
	 *
	 * @return int Varnish port number
	 */
	public function getPort()
	{
		if ( ! $port = ee()->config->item('varnish_port'))
		{
			$port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
		}

		return (int) $port;
	}
}

// EOF
