<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
						'pi_name'			=> 'Selected',
						'pi_version'		=> '1',
						'pi_author'			=> 'EE Support',
						'pi_author_url'		=> 'http://ellislab.com/',
						'pi_description'	=> 'Returns an text if there is a match in $_GET or $_POST',
						'pi_usage'			=> Selected::usage()
					);


/**
 * Param_plus Class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			EllisLab Dev Team
 * @copyright		Copyright (c) 2004 - 2014, EllisLab, Inc.
 * @link			http://ellislab.com
 */


class Selected {

	var $return_data = '';

	/**
	 * Constructor
	 *
	 */
	function Selected($str = '')
	{
		// Fetch parameters
		$field = ee()->TMPL->fetch_param('field');
		$match = ee()->TMPL->fetch_param('match');
		$return = ee()->TMPL->fetch_param('return', 'selected');		
		
		if ( ! $field OR ! $match)
		{
			return;
		}
		
		if (ee()->input->get_post($field) == $match)
		{
			$this->return_data = $return;
		}
		elseif (isset($_POST[$field]) && is_array($_POST[$field]))
		{
			if (in_array($match, $_POST[$field]))
			{
				$this->return_data = $return;
			}
		}
		elseif (isset($_GET[$field]) && is_array($_GET[$field]))
		{
			if (in_array($match, $_GET[$field]))
			{
				$this->return_data = $return;
			}
		}
		
		return;
		
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
	public static function usage()
	{
		ob_start();
		?>
This plugin is for use in forms and intended to show whether a field value was selected when submitted.  It checks both $_GET and $_POST for the existence of the specified field name.  If the fieldname exists and the value matches the value in the match parameter, it returns the word 'selected'.  The return word can be altered using the 'output' parameter.  

			{exp:selected field="category" match="blue" output="checked"}
			
If you have a field named 'category' or 'category[]' and the value 'blue' is in the POST/GET, the above tag would be replaced by the word 'checked'.

		<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}

	// --------------------------------------------------------------------

}
// END CLASS