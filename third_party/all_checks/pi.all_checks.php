<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
Copyright (C) 2007 - 2011 EllisLab, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
ELLISLAB, INC. BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Except as contained in this notice, the name of EllisLab, Inc. shall not be
used in advertising or otherwise to promote the sale, use or other dealings
in this Software without prior written authorization from EllisLab, Inc.
*/

$plugin_info = array(
						'pi_name'			=> 'All Checkboxes',
						'pi_version'		=> '1.0',
						'pi_author'			=> 'ExpressionEngine Dev Team',
						'pi_author_url'		=> 'http://expressionengine.com/',
						'pi_description'	=> 'Displays all the checkboxes for a given field',
						'pi_usage'			=> All_checks::usage()
					);
					
class All_checks
{

    public function __construct()
    {
        $this->EE =& get_instance();
        $this->return_data = $this->get_options();
    }
	
    public function get_options()
    {
 		$field_id = $this->EE->TMPL->fetch_param('field_id');

		$selected = $this->EE->TMPL->fetch_param('data');
		$selected_array = array();

		if ($selected != FALSE)
		{
			foreach(explode(',', $selected) as $v)
			{
				$selected_array[trim($v)] = '';
			}
		}

		$this->EE->db->select('*');
		$this->EE->db->where('field_id', $field_id); 
		$this->EE->db->from('channel_fields');		

		$query = $this->EE->db->get();

		if ($query->num_rows() == 0)
		{
			return 'out';
		}
		
		$out = array();

		foreach ($query->result_array() as $id => $row)
		{
			$options = explode("\n", $row['field_list_items']);
			
			foreach ($options as $v)
			{
				$v = trim($v);
				$checked = (isset($selected_array[$v])) ? 'checked' : FALSE;
				
				$out[] = array('option' => $v, 'checked' => $checked);
			}
		}
		
		$output = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $out);

       return $output;
    }

 
	// --------------------------------------------------------------------
	
	/**
	 * Usage
	 *
	 * Plugin Usage
	 *
	 * @access	public
	 * @return	string
	 */
	function usage()
	{
		ob_start(); 
		?>
		------------------
		EXAMPLE USAGE:
		------------------
		
{exp:channel:entries entry_id="16"}

{exp:all_checks field_id="9" data="{checks}"}
		
{option} {checked}<br>
		
{/exp:all_checks}

{/exp:channel:entries}

Where data is the checkbox field.

		

		<?php
		$buffer = ob_get_contents();

		ob_end_clean(); 

		return $buffer;
	}

	// --------------------------------------------------------------------
	
}
// END Class
