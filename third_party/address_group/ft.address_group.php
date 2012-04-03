<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Address_group_ft extends EE_Fieldtype {
	
	var $info = array(
		'name'		=> 'Address Group',
		'version'	=> '1.0'
	);
	
	var $address_fields = array('street_address', 'city', 'state', 'zip');
	var $has_array_data = TRUE;
	
	
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
		// Load our language file as we have field labels to show
		$this->EE->lang->loadfile('address_group');
		
		// Check for post data first
		// may exist if validation failed and we do not want to overwrite it
		
		if ( ! empty($_POST))
		{
			foreach($this->address_fields as $key)
			{
				$$key = (string) $this->EE->input->post($key.'_field_id_'.$this->field_id);
			}
		}
		elseif ($data)
		{
			list($street_address, $city, $state, $zip) = explode('|', $data);
		}
		else
		{
			// Note the use of our settings array to grab default settings if they exist
			foreach($this->address_fields as $key)
			{
				$$key = (isset($this->settings[$key])) ? $this->settings[$key] : '';
			}
		}

		// Placeholder data in order to skip core required check
		// This is necessary or fields set to 'required' will never get past validation
		$form = form_hidden('field_id_'.$this->field_id, 'address_group_placeholder').'<table>';
		
		foreach($this->address_fields as $key)
		{
			$form .= '<tr><td>'.lang($key, $key.'_field_id_'.$this->field_id).'</td><td>'.form_input($key.'_field_id_'.$this->field_id, $$key).'</td></tr>';
		}

		$form .= '</table>';

		return $form;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Display Global Settings
	 *
	 * @access	public
	 * @return	form contents
	 *
	 */
	function display_global_settings()
	{
		// We really don't need global settings
		// But here they are as an example!

		$this->EE->lang->loadfile('address_group');
		$data = array_merge($this->settings, $_POST);
		
		$form = '<table>';
		
		foreach($this->address_fields as $key)
		{
			$form .= '<tr><td>'.lang($key, $key).'</td><td>'.form_input($key, $data[$key]).'</td></tr>';
		}

		$form .= '</table>';		
		
		return $form;
	}


	// --------------------------------------------------------------------
	
	/**
	 * Display Settings Screen
	 *
	 * @access	public
	 * @return	default global settings
	 *
	 */
	function display_settings($data)
	{
		$this->EE->lang->loadfile('address_group');
	
		foreach($this->address_fields as $key)
		{
			$data[$key] = (isset($data[$key])) ? $data[$key] : ''; 
			
			$this->EE->table->add_row(
				lang($key, $key), form_input($key, $data[$key])
				);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Install Fieldtype
	 *
	 * @access	public
	 * @return	default global settings
	 *
	 */
	function install()
	{
		foreach($this->address_fields as $key)
		{
			$data[$key] = '';
		}		
		
		return $data;
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
		// If there is no data?  We'll just return and variable or pair
		// replaced with empty string
		if (empty($data))
		{
			return;
		}
		
		// We have data!
		list($street_address, $city, $state, $zip) = explode('|', $data);
		
		// Do we have a tag pair?
		if ($tagdata !== FALSE)
		{
			// Let's prefix our variables to reduce likelihood of collisions
			$address_array = array(
				'ag_street_address' => $street_address, 
				'ag_city' => $city, 
				'ag_state' => $state, 
				'ag_zipcode' => $zip												
			);

			$tagdata = $this->EE->functions->prep_conditionals($tagdata, $address_array);
			$tagdata = $this->EE->functions->var_swap($tagdata, $address_array);
		}		
		else
		{
			// We could, and probably should, run this through the parser
			/*
				$this->EE->typography->parse_type(
				$this->EE->functions->encode_ee_tags($data),
				array(
					'text_format'	=> $this->row['field_ft_'.$this->field_id],
					'html_format'	=> $this->row['channel_html_formatting'],
					'auto_links'	=> $this->row['channel_auto_link_urls'],
					'allow_img_url' => $this->row['channel_allow_img_urls']
					)
				);			
			*/
			
			$tagdata = $street_address."\n".$city.', '.$state.' '.$zip;

			$tagdata = 	$this->EE->typography->parse_type(
				$this->EE->functions->encode_ee_tags($tagdata),
				array(
					'text_format'	=> $this->row['field_ft_'.$this->field_id],
					'html_format'	=> $this->row['channel_html_formatting'],
					'auto_links'	=> 'n',
					'allow_img_url' => 'n'
					)
				);
		}
		
		return $tagdata;
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
		$address = array();
		
		foreach($this->address_fields as $key)
		{
			$address[] = $this->EE->input->post($key.'_field_id_'.$this->field_id);
		}
		
		// We could do something nicer, such as json array here
		return implode('|', $address);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Save Global Settings
	 *
	 * @access	public
	 * @return	global settings
	 *
	 */
	function save_global_settings()
	{
		return array_merge($this->settings, $_POST);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Settings
	 *
	 * @access	public
	 * @return	field settings
	 *
	 */
	function save_settings($data)
	{
		return array(
			'street_address'	=> $this->EE->input->post('street_address'),
			'city'				=> $this->EE->input->post('city'),
			'state'				=> $this->EE->input->post('state'),
			'zip'				=> $this->EE->input->post('zip')
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Validate field input
	 *
	 * @access	public
	 * @param	submitted field data
	 * @return	TRUE or an error message
	 *
	 */
	function validate($data)
	{
		// Validation is a bit tricky with this one.  Note that typically, an empty 'required' field
		// will be cause by the API before getting to this point.  Which is a problem, here.
		// The field data is actually in our city/stat/zip fields, not field_id_x.  So- we fudge 
		// some data in there and do the actual required check here!
		
		$this->EE->lang->loadfile('address_group');
		$total = count($this->address_fields);
		$valid = 0;

		foreach($this->address_fields as $key)
		{
			$post_data = $this->EE->input->post($key.'_field_id_'.$this->field_id);
			$valid += ( ! empty($post_data)) ? 1 : 0;
		}

		// Is the field required?
		if ($this->settings['field_required'] == 'y')
		{
			if ($valid != $total)
			{
				return lang('all_address_fields_required');
			}		
		}

		//  Done with standard 'required check, we do something a bit more custom
		// If any field has content?  All address fields must be filled out
		if ($valid == 0 OR $valid == $total)
		{
			return TRUE;
		}

		return lang('no_partial_addresses');
	}
	
}

/* End of file ft.address_group.php */
/* Location: ./system/expressionengine/third_party/address_group/ft.address_group.php */