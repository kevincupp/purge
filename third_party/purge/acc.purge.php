<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------
 
/**
 * Purge Accessory
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Accessory
 * @author		Kevin Cupp
 * @link		http://kevincupp.com
 */
 
class Purge_acc
{	
	public $name			= 'Purge';
	public $id				= 'purge';
	public $version			= '1.0.1';
	public $description		= 'Provides a place to manually send a purge request to Varnish.';
	public $sections		= array();
	
	/**
	 * Set Sections
	 */
	public function set_sections()
	{
		$EE =& get_instance();
		
		$data['request_url'] = html_entity_decode(BASE.AMP.'C=addons_accessories'.AMP.'M=process_request'.AMP.'accessory=purge'.AMP.'method=process_purge_request');
		
		$this->sections['Purge Varnish'] = $EE->load->view('accessory_purge_varnish', $data, TRUE);
	}
	
	/**
	 * Handles AJAX request from control panel accessory to send purge request to Varnish
	 */
	public function process_purge_request()
	{
		if (AJAX_REQUEST)
		{
			$EE =& get_instance();
			$EE->load->helper('varnish');
			$urls = $EE->config->item('varnish_site_url');
  			$port = $EE->config->item('varnish_port');
  		
			if ( ! is_array($urls))
			{
				$urls = array($urls);
			}
			
			foreach ($urls as $url)
			{
				send_purge_request($url, $port);
			}
		}
	}
}
 
/* End of file acc.purge.php */
/* Location: /system/expressionengine/third_party/purge/acc.purge.php */
