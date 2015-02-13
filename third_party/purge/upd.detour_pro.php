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
 * Detour Pro Module Install/Update File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Mike Hughes - City Zen
 * @link		http://cityzen.com
 */

class Detour_pro_upd {
	
	public $version = '1.5';
	
	private $EE;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Installation Method
	 *
	 * @return 	boolean 	TRUE
	 */
	public function install()
	{	
		$mod_data = array(
			'module_name'			=> 'Detour_pro',
			'module_version'		=> $this->version,
			'has_cp_backend'		=> "y",
			'has_publish_fields'	=> 'n'
		);
		
		$this->EE->functions->clear_caching('db');
		$this->EE->db->insert('modules', $mod_data);
		
		$this->EE->load->dbforge();

		/* Check to see if detour table exists */
		$sql = 'SHOW TABLES LIKE \'%detour%\'';
		$result = $this->EE->db->query($sql);
		
		$prev_install = ($result->num_rows) ? TRUE : FALSE;
		
		if($prev_install)
		{
	       	// Detour ext installed, update table to include MSM
	       	
	       	$query = $this->EE->db->get('detours', 1)->row_array();
	       	
	       	// Double check to see if site_id already exists
	       	if(!array_key_exists('site_id', $query))
	       	{
		        $fields = array(
	            	'site_id' => array('type' => 'int', 'constraint' => '4', 'unsigned' => TRUE), 
	            	'start_date' => array('type' => 'date', 'null' => TRUE), 
					'end_date' => array('type' => 'date', 'null' => TRUE), 
				);
				$this->EE->dbforge->add_column('detours', $fields);	
				
				// Apply site id of 1 to all existing detours
				$this->EE->db->update('detours', array('site_id' => 1), 'detour_id > 0');       	
	       	}
		}
		else
		{
			// Create detour tables and keys
			$fields = array
			(
				'detour_id'	=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'original_url'	=> array('type' => 'varchar', 'constraint' => '250'),
				'new_url'	=> array('type' => 'varchar', 'constraint' => '250', 'null' => TRUE, 'default' => NULL), 
				'start_date' => array('type' => 'date', 'null' => TRUE), 
				'end_date' => array('type' => 'date', 'null' => TRUE), 
				'detour_method' => array('type' => 'int', 'constraint' => '3', 'unsigned' => TRUE, 'default' => '301'), 
				'site_id' => array('type' => 'int', 'constraint' => '4', 'unsigned' => TRUE)
			);
	
			$this->EE->dbforge->add_field($fields);
			$this->EE->dbforge->add_key('detour_id', TRUE);
			$this->EE->dbforge->create_table('detours');
		}
			
		unset($fields);
		
		// Create hits table
		
		
		$sql = 'SHOW TABLES LIKE \'%detours_hits%\'';
		$result = $this->EE->db->query($sql);
		
		$prev_install = ($result->num_rows) ? TRUE : FALSE;
		
		if(!$prev_install)
		{		
			$this->_create_table_hits(); 
		}
		
		// Enable the extension to prevent redirect erros while installing.
		$this->EE->db->where('class', 'Detour_pro_ext');
		$this->EE->db->update('extensions', array('enabled'=>'y'));
		
		return TRUE;
	}

	// ----------------------------------------------------------------
	
	/**
	 * Uninstall
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function uninstall()
	{
		$mod_id = $this->EE->db->select('module_id')
								->get_where('modules', array(
									'module_name'	=> 'Detour_pro'
								))->row('module_id');
		
		$this->EE->db->where('module_id', $mod_id)
					 ->delete('module_member_groups');
		
		$this->EE->db->where('module_name', 'Detour_pro')
					 ->delete('modules');
		
		$this->EE->load->dbforge();
		$this->EE->dbforge->drop_table('detours');
		$this->EE->dbforge->drop_table('detours_hits');
		
		return TRUE;
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Module Updater
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function update($current = '')
	{
		// If you have updates, drop 'em in here.
		return TRUE;
	}
	
	
	/* Private Functions */
	
	function _create_table_hits()
	{
			$fields = array(
				'hit_id' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE), 
				'detour_id'	=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
				'hit_date'	=> array('type' => 'datetime')
			);
	
			$this->EE->dbforge->add_field($fields);
			$this->EE->dbforge->add_key('hit_id', TRUE);
		
			return ($this->EE->dbforge->create_table('detours_hits')) ? TRUE : FALSE;
	}
	
}
/* End of file upd.detour_pro.php */
/* Location: /system/expressionengine/third_party/detour_pro/upd.detour_pro.php */