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
 * TOS Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		CATs
 * @link		http://ellislab.com
 */

class Tosconf_ext {
	
	public $settings 		= array();
	public $description		= 'Quick one';
	public $docs_url		= '';
	public $name			= 'TOS Confirmation';
	public $settings_exist	= 'n';
	public $version			= '1.0';
	public $location 		= 'http://127.0.0.1:8888/g20/index.php/tos/accept';
	
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->settings = $settings;
	}
	
	// ----------------------------------------------------------------------
	
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
		// Setup custom settings in this array.
		$this->settings = array();
		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'cookie_check',
			'hook'		=> 'sessions_end',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		$this->EE->db->insert('extensions', $data);	
	}
	
	/**
	 * Cookie Check
	 *
	 * Checks for TOS cookie
	 *
	 */
	public function cookie_check($obj)
	{
		if (REQ != 'PAGE')
		{
			return;
		}


		
		// If the cookie is set- allow them through
		if (ee()->input->cookie('tos_confirmed') != FALSE)
		{
			return;
		}
		
		// If they are logged out, let them through
		// Use EE settings to control what non-members see
		if ($obj->userdata['member_id'] == 0)
		{
			return;
		}			
			
		// We check for member and forum urls only
		// If no cookie is set there, we send them back to the confirmation page
		if (ee()->uri->segment(1) == 'member')
		{
			$this->_do_check();
		}
		elseif (in_array(ee()->uri->segment(1), preg_split('/\|/', 'forums', -1, PREG_SPLIT_NO_EMPTY)))
		{
			$this->_do_check();			
		}
	}					


	/**
	 * Do Check
	 *
	 * Helper function- no cookie, redirects to location var
	 *
	 */
	private function _do_check()
	{
		if (ee()->input->cookie('TOSaccept') == FALSE)
		{
			$header = "Location: $this->location";
			header($header);
			exit;
		}
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

/* End of file ext.tosconf.php */
/* Location: /system/expressionengine/third_party/tosconf/ext.tosconf.php */