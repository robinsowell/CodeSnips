<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Slashed_ext {

	public $name = 'Slashed';
	public $version = '1.0';
	public $settings_exist = 'n';
	public $docs_url = '';
	public $settings        = array();

	private $EE;
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */

	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
	}

	public function slash_it($data)
	{
		if (isset($data['action']))
		{
			$data['action'] = ($data['action'] == '') ? ltrim($this->EE->functions->fetch_site_index(), '/').'/' : ltrim($data['action'], '/').'/';
		}

		if (isset($data['hidden_fields']['RET']))
		{
			$data['hidden_fields']['RET'] = ltrim($data['hidden_fields']['RET'], '/').'/';
		}
	}


	function activate_extension()
	{
		// Checks if cookies are allowed before setting them
		$this->EE->db->insert('extensions', array(
			'class'    => __CLASS__,
			'hook'     => 'form_declaration_modify_data',
			'method'   => 'slash_it',
			'settings' => $this->settings,
			'priority' => 1,
			'version'  => $this->version,
			'enabled'  => 'y'
		));
	}
	
	function disable_extension()
	{
    	$this->EE->db->where('class', __CLASS__);
    	$this->EE->db->delete('extensions');
	}
	
}
