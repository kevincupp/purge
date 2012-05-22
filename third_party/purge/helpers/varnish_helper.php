<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('send_purge_request'))
{
	/**
	 * Sends purge request to Varnish through CURL
	 */
	function send_purge_request($site_url, $site_port=null)
	{
    if ( ! $site_url) {
      $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
      $purge_url = $protocol . $_SERVER['HTTP_HOST'] . '/';
      $port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : $site_port;
    }
    else {
      $parsed_url = parse_url($site_url);
      $url_path = array_key_exists("path", $parsed_url) ? $parsed_url["path"] : '/';
      $purge_url = $parsed_url["scheme"] . "://" . $parsed_url["host"] . $url_path;
      $port = (!array_key_exists("port", $parsed_url) || is_null($parsed_url["port"])) ? 80 : $parsed_url["port"];
    }

    if (is_null($port))
      $port = 80;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $site_url);
    curl_setopt($ch, CURLOPT_PORT , (int)$port);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'EE_PURGE');
    curl_exec($ch);
	}
}

?>
