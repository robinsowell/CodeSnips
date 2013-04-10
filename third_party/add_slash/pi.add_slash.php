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
						'pi_name'			=> 'Add Slash',
						'pi_version'		=> '1.0',
						'pi_author'			=> 'ExpressionEngine Dev Team',
						'pi_author_url'		=> 'http://expressionengine.com/',
						'pi_description'	=> 'Adds a slash to the end of a string',
						'pi_usage'			=> Add_slash::usage()
					);
					
class Add_slash
{

    public $return_data = "";

    public function __construct()
    {
		$this->EE =& get_instance();	
		$this->return_data = $this->_slashed();
    }

	
    public function _slashed()
    {
		return rtrim($this->EE->TMPL->fetch_param('url'), '/').'/';
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
		
		{exp:add_slash url="http://site.com"}

		

		<?php
		$buffer = ob_get_contents();

		ob_end_clean(); 

		return $buffer;
	}

	// --------------------------------------------------------------------
	
}