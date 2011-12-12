<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('send_purge_request'))
{
	/**
	 * Sends purge request to Varnish through CURL
	 */
	function send_purge_request($site_url, $port)
	{
    if ( ! $site_url) {
      $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
      $site_url = $protocol . $_SERVER['HTTP_HOST'] . '/';
    }
    if ( ! $port) {
      $port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;       
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $site_url);
    curl_setopt($ch, CURLOPT_PORT , (int)$port);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'EE_PURGE');
    curl_exec($ch);
	}
}

?>