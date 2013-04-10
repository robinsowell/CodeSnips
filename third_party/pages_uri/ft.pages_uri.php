<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages_uri_ft extends EE_Fieldtype {
	
	var $info = array(
		'name'		=> 'Pages URI',
		'version'	=> '1.0'
	);

	//var $has_array_data = TRUE;  // Required if you want tag pairs!
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Display Field on Publish
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	function display_field($data)
	{
		// We really don't care- we're gonna rewrite it before save anyway
		
		$this->EE->javascript->set_global('publish.hidden_fields', array($this->field_id => $this->field_name));
		return form_hidden($this->field_name, $data);

		return $form;
	}


	// --------------------------------------------------------------------
		
	/**
	 * Replace tag
	 *
	 * @access	public
	 * @param	field data
	 * @param	field parameters
	 * @param	data between tag pairs
	 * @return	replacement text
	 *
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		return $this->EE->functions->encode_ee_tags($data);
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Save Data
	 *
	 * @access	public
	 * @param	submitted field data
	 * @return	string to save
	 *
	 */
	function save($data)
	{
		// Let's see- we want the pages uri: pages__pages_uri
		$val = $this->EE->input->post('pages__pages_uri');
		
		$val = (empty($val) OR $val == '/example/pages/uri/') ? '' : $val;
		return $val;
	}
	
}

/* End of file ft.pages_uri.php */
/* Location: ./system/expressionengine/third_party/address_group/ft.pages_uri.php */