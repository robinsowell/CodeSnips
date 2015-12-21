<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class J_array_ft extends EE_Fieldtype {
	var $info = array(
		'name'		=> 'Jason Array Field',
		'version'	=> '1.0'
	);
	var $has_array_data = FALSE;

	// --------------------------------------------------------------------
	function validate($data)
	{
		return TRUE;
	}

	// --------------------------------------------------------------------
	function display_field($data)
	{

		return form_textarea(array(
			'name'	=> $this->name(),
			'value'	=> $data,
			'rows'	=> $this->settings['field_ta_rows'],
			'dir'	=> $this->settings['field_text_direction'],
			'class' => ''
		));
	}
	// --------------------------------------------------------------------
	function replace_tag($data, $params = '', $tagdata = '')
	{
		$data =  ee()->functions->encode_ee_tags($data);
		$my_array = explode("\n", trim($data));
		
		foreach ($my_array as $k => $v)
		{
			$my_array[$k] = trim($v);
		}

		return json_encode($my_array);

	}
	// --------------------------------------------------------------------
	/**
	 * Accept all content types.
	 *
	 * @param string  The name of the content type
	 * @param bool    Accepts all content types
	 */
	public function accepts_content_type($name)
	{
		return TRUE;
	}
	// --------------------------------------------------------------------
	function display_settings($data)
	{
		$prefix = 'j_array';
		$field_rows	= ($data['field_ta_rows'] == '') ? 6 : $data['field_ta_rows'];
		ee()->table->add_row(
			lang('textarea_rows', 'field_ta_rows'),
			form_input(array('id'=>'field_ta_rows','name'=>'field_ta_rows', 'size'=>4,'value'=>set_value('field_ta_rows', $field_rows)))
		);
		$this->text_direction_row($data, $prefix);
	}

}

// EOF

