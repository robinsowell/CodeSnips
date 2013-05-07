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
 * Registration Override Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		EE Dev
 * @link		
 */

class Reg_override_ext {
	
	public $settings 		= array();
	public $description		= 'Conditional processing after member registration.';
	public $docs_url		= '';
	public $name			= 'Registration Override';
	public $settings_exist	= 'n';
	public $version			= '1.0';
	
	private $EE;
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
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
		// Setup custom settings in this array.
		$this->settings = array();
		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'pro_check',
			'hook'		=> 'member_member_register',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		$this->EE->db->insert('extensions', $data);			
		
	}	

	// ----------------------------------------------------------------------
	
	/**
	 * pro_check
	 *
	 * @param 
	 * @return 
	 */
	public function pro_check($data, $member_id)
	{
		if ($this->EE->input->post('pro_signup') != 'yes')
		{
		  	return;
		}
		
		// It's a pro sign up, let's take over processing
		$this->EE->extensions->end_script = TRUE;
		
		$message = 'Thank you for signing up. You should receive your billing confirmation soon.';
		
		$site_name = ($this->EE->config->item('site_name') == '') ? lang('back') : stripslashes($this->EE->config->item('site_name'));
		$return = $this->EE->config->item('site_url');


		$data = array(
			'title' 	=> 'Member Registration is Complete',
			'heading'	=> 'Thank You',
			'content'	=> $message,
			'redirect'	=> '',
			'link'		=> array($return, $site_name)
		);

		$this->EE->output->show_message($data);
		
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

/* End of file ext.reg_override.php */
/* Location: /system/expressionengine/third_party/reg_override/ext.reg_override.php */