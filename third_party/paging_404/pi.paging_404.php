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
 * Paging 404 Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		EE Dev
 * @link		
 */

$plugin_info = array(
	'pi_name'		=> 'Paging 404',
	'pi_version'	=> '1.0',
	'pi_author'		=> 'EE Dev',
	'pi_author_url'	=> '',
	'pi_description'=> '404 page redirect',
	'pi_usage'		=> Paging_404::usage()
);


class Paging_404 {

	public $return_data;
    
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$redirect	= ee()->TMPL->fetch_param('redirect');
		$require_pagination	= ee()->TMPL->fetch_param('require_pagination');
		$segment	= ee()->TMPL->fetch_param('pagination_segment');
		
		$url = ee()->functions->create_url($redirect);
		
		if ( ! $segment)
		{
			return;
		}

		$seg = ee()->uri->segment($segment);

		if ($seg == FALSE)
		{
			if ($require_pagination == 'yes')
			{
				ee()->functions->redirect($url, FALSE, 404);
			}
			
			return;
		}
		
		//if (preg_match("#^P(\d+)|/P(\d+)#", $seg, $match))		
		if (preg_match("#P(\d+)#", $seg, $match))
		{					
			if (ee()->uri->segment($segment+1))
			{
				ee()->functions->redirect($url, FALSE, 404);
			} 
		}		
		else
		{
			ee()->functions->redirect($url, FALSE, 404);
		}
			
		return;
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Plugin Usage
	 */
	public static function usage()
	{
		ob_start();
?>

Specify a segment for pagination- if there's a segment after that, it 404's.

Parameters

pagination_segment (required) Specifies the segment to look for pagination in.

require_pagination (default 'no')  If set to 'yes', the pagination MUST contain a Px segment.  If set to 'no', that segment may be empty.

redirect (required) The template_group/template to redirect to on invalid url.

Example:

{exp:paging_404 redirect="about/404" pagination_segment = "3"}

If segment 3 has anything in it OTHER than a valid page indicator (Px) OR if it has something in segment 4 AFTER a valid page indicator in segment 3- it will redirect to your about/404 template.


<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.paging_404.php */
/* Location: /system/expressionengine/third_party/paging_404/pi.paging_404.php */