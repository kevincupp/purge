<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


 
class Purge_mcp {
	
	public $return_data;
	public $return_array = array();
	
	private $_base_url;
	private $_data = array();
	private $_module = 'purge';
	
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=purge';
		
		$this->EE->cp->set_right_nav(array(
			'module_home'	=> $this->_cp_url()
		));

		$this->EE->view->cp_page_title = "Purge: Channel URL Patterns for Varnish";
	}
	
	


	public function index()
	{
		
		$this->EE->load->library('table');
		
		$this->_data['action_url'] = $this->_base_url . '&method=save';
		//$this->_data['images'] = $this->get_alttags();
		
		$this->EE->load->model('channel_model');
		$channels_query = $this->EE->channel_model->get_channels()->result();
		foreach ($channels_query as $channel) 
			$this->_data['channels'][] = array('channel_title' => $channel->channel_title, 'channel_name' => $channel->channel_name, 'channel_id' => $channel->channel_id );
		
		$this->_data['rules'] = $this->get();
		
		return $this->EE->load->view('rules', $this->_data, TRUE);
		
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
		
		// Redirect back to Detour Pro landing page
		$this->EE->functions->redirect($this->_base_url);
	}

	function strposa($haystack, $needle, $offset=0) {
		if(!is_array($needle)) $needle = array($needle);
		foreach($needle as $query) {
			if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
		}
		return false;
	}
	
	
	
	private function _cp_url ($method = 'index', $variables = array()) {
		$url = BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=' . $this->_module . AMP . 'method=' . $method;
		
		foreach ($variables as $variable => $value) {
			$url .= AMP . $variable . '=' . $value;
		}
		
		return $url;
	}
	
	private function _form_url ($method = 'index', $variables = array()) {
		$url = 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=' . $this->_module . AMP . 'method=' . $method;
		
		foreach ($variables as $variable => $value) {
			$url .= AMP . $variable . '=' . $value;
		}
		
		return $url;
	}
	
	

	
}
