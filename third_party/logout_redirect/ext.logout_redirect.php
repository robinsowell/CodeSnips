<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Logout_redirect_ext {

	var $name = 'Logout_redirect';
	var $version = '1.0';
	var $settings_exist = 'n';
	var $docs_url = '';

	/**
	 * Extension Constructor
	 */
	function __construct()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Activate Extension
	 */
	function activate_extension()
	{
		ee()->db->insert('extensions', array(
			'class'    => $this->name.'_ext',
			'method'   => 'member_logout_redirect',
			'hook'     => 'member_member_logout',
			'settings' => '',
			'priority' => 10,
			'version'  => $this->version,
			'enabled'  => 'y'
		));

		ee()->db->insert('extensions', array(
			'class'    => $this->name.'_ext',
			'method'   => 'cache_group_id',
			'hook'     => 'sessions_end',
			'settings' => '',
			'priority' => 10,
			'version'  => $this->version,
			'enabled'  => 'y'
		));

	}

	/**
	 * Update Extension
	 */
	function update_extension($current = FALSE)
	{
		if (! $current || $current == $this->version)
		{
			return FALSE;
		}
	}
	

	

	/**
	 * Disable Extension
	 */
	function disable_extension()
	{
		ee()->db->delete('extensions', array('class' => $this->name.'_ext'));
	}


	// --------------------------------------------------------------------

	function cache_group_id(&$sess)
	{
		if (isset($sess->userdata['group_id']))
		{
			$sess->set_cache('member_lr', 'group_id', $sess->userdata['group_id']);
		}
	}

	// --------------------------------------------------------------------


	function member_logout_redirect()
	{
		$group_id = ee()->session->cache('member_lr', 'group_id');
	
		
		// Only modify forum logout
		if (ee()->input->get_post('FROM') != 'forum')
		{
			return;
		}

		ee()->extensions->end_script;
		

		if ($group_id == 1)
		{
			$url = 'http://127.0.0.1:8888/g20/';
		}
		else
		{
			$url = 'http://127.0.0.1:8888/g20/index.php/forums';
		}

		// Build success message

		$name	= stripslashes(ee()->config->item('site_name'));

		$data = array(
			'title' 	=> lang('mbr_login'),
			'heading'	=> lang('thank_you'),
			'content'	=> lang('mbr_you_are_logged_out'),
			'redirect'	=> $url,
			'link'		=> array($url, $name)
		);

		ee()->output->show_message($data);

	}

}
