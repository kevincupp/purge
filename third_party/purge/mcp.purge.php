<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


 
class Purge_mcp {
	
	public $return_data;
	public $return_array = array();
	private $_base_url;

	
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=purge';
		
		$this->EE->cp->set_right_nav(array(
			'module_home'	=> $this->_base_url
		));

		$this->EE->view->cp_page_title = "Purge: Channel URL Patterns for Varnish";
	}
	
	


	public function index()
	{
		
		$_data = array();
		
		$_data['action_url'] = $this->_base_url . '&method=save';
				
		$this->EE->load->model('channel_model');
		$channels_query = $this->EE->channel_model->get_channels()->result();
		foreach ($channels_query as $channel) 
			$_data['channels'][] = array('channel_title' => $channel->channel_title, 'channel_name' => $channel->channel_name, 'channel_id' => $channel->channel_id );
		
		$_data['rules'] = $this->get();
		
		return $this->EE->load->view('rules', $_data, TRUE);
		
	}
	
	private function get()
	{
		$this->EE->db->select('*');
		return $this->EE->db->get_where('purge_rules')->result_array();
	}
	
	
	public function save()
	{	
	
		$rules = $_POST['rule'];
		$patterns = $_POST['pattern'];
		
		$this->EE->db->empty_table('purge_rules');
		
		foreach($rules as $key => $channel)
		{
			$this->EE->db->insert( 'purge_rules', array('channel_id' => $channel, 'pattern' => $patterns[$key]) );	
		}

		$this->EE->functions->redirect($this->_base_url);
	}


	
	

	
}
