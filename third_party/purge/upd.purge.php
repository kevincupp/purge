<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Purge_upd {
	
	public $version = '1.1.1';
	
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
			'module_name'			=> 'Purge',
			'module_version'		=> $this->version,
			'has_cp_backend'		=> "y",
			'has_publish_fields'	=> 'n'
		);
		
		// add the rules table
		$sql[] = "
		CREATE TABLE `{$this->EE->db->dbprefix}purge_rules` (
			`id` int(11) unsigned NOT NULL auto_increment,
			`channel_id` int(4) unsigned NOT NULL,
			`site_id` int(4) unsigned NOT NULL,
	 		`pattern` varchar(255) default NULL,
			PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";

		// run the queries one by one
		foreach ($sql as $query)
		{
			$this->EE->db->query($query);
		}	
		
		$this->EE->functions->clear_caching('db');
		$this->EE->db->insert('modules', $mod_data);
		
		$this->EE->load->dbforge();

			
		// Enable the extension to prevent redirect erros while installing.
		$this->EE->db->where('class', 'Purge_ext');
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
									'module_name'	=> 'Purge'
								))->row('module_id');
		
		$this->EE->db->where('module_id', $mod_id)
					 ->delete('module_member_groups');
		
		$this->EE->db->where('module_name', 'Purge')
					 ->delete('modules');
		
		$this->EE->load->dbforge();
		$this->EE->dbforge->drop_table('purge_rules');

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
		return FALSE;
	}
	

	
}
