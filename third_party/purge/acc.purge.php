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
	public $version			= '1.0';
	public $description		= 'Provides a place to manually send a purge request to Varnish.';
	public $sections		= array();
	
	/**
	 * Set Sections
	 */
	public function set_sections()
	{
		$EE =& get_instance();
		
		// Tried to use BASE and AMP constants but was causing "Disallowed Key Characters" error
		$data['request_url'] = 'index.php?D=cp&C=addons_accessories&M=process_request&accessory=purge&method=process_purge_request';
		
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
			$site_url = $EE->config->item('varnish_site_url');
  		$port = $EE->config->item('varnish_port');
  		
			send_purge_request($site_url, $port);
		}
	}
}
 
/* End of file acc.purge.php */
/* Location: /system/expressionengine/third_party/purge/acc.purge.php */