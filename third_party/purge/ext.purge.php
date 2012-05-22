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
 * Purge Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Kevin Cupp
 * @link		http://kevincupp.com
 */

class Purge_ext
{	
	public $description		= 'Sends purge header to Varnish after entry submission and deletion.';
	public $docs_url		= '';
	public $name			= 'Purge';
	public $settings_exist	= 'n';
	public $version			= '1.0.1';
	
	private $EE;
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->site_url = $this->EE->config->item('varnish_site_url');
        $this->port = $this->EE->config->item('varnish_port');
    
	}// ----------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		$hooks = array(
		  'entry_submission_end'	=> 'send_purge_request',
		  'delete_entries_end'		=>'send_purge_request'
		);
		
		foreach ($hooks as $hook => $method)
		{
			$data = array(
				'class'		=> __CLASS__,
				'method'	=> $method,
				'hook'		=> $hook,
				'settings'	=> '',
				'version'	=> $this->version,
				'enabled'	=> 'y'
			);
			
			$this->EE->db->insert('extensions', $data);
		}
	}	

	// ----------------------------------------------------------------------
	
	/**
	 * Sends purge request to Varnish when registered EE hooks are triggered
	 *
	 * @param 
	 * @return 
	 */
	public function send_purge_request()
	{
		$this->EE->load->helper('varnish');

        $urls = $this->site_url;
        if (!is_array($urls))
            $urls = array($urls);
		
        foreach ($urls as $url)
            send_purge_request($url, $this->port);
	}

	// ----------------------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}	
	
	// ----------------------------------------------------------------------
}

/* End of file ext.purge.php */
/* Location: /system/expressionengine/third_party/purge/ext.purge.php */
