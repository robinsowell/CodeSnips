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
 * Member Export Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		EL Support
 * @link		
 */

class Member_export_mcp {
	
	public $return_data;
	
	private $_base_url;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=member_export';
		
		$this->EE->cp->set_right_nav(array(
			'module_home'	=> $this->_base_url,
			// Add more right nav items here.
		));
	}
	
	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return 	void
	 */
	public function index()
	{
		 $this->EE->view->cp_page_title = lang('member_export_module_name');
		
		// Store by type with | between values
		ee()->db->select("group_id, email");
		ee()->db->order_by("email", 'asc');
		$query = ee()->db->get("members");

		if ($query->num_rows() != 0)
		{
			foreach($query->result_array() as $row)
			{
				$member_data[] = $row['group_id'].', '.$row['email'];
			}
		}

		$vars['list_item'] = (implode($member_data, "\n"));

		return ee()->load->view('index', $vars, TRUE);
	
	}

	/**
	 * Start on your custom code here...
	 */
	
}
/* End of file mcp.member_export.php */
/* Location: /system/expressionengine/third_party/member_export/mcp.member_export.php */