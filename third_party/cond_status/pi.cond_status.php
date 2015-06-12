<?php
$plugin_info = array(
'pi_name' => 'Conditional Status',
'pi_version' => '.1',
'pi_author' => 'EE',
'pi_author_url' => 'http://ellislab.com/',
'pi_description' => 'Conditionally outputs a status parameter values based on member group',
'pi_usage' => Cond_status::usage()
);

class Cond_status {

var $return_data;

	public function __construct()
	{
		$this->return_data = $this->check_conditionals();
	}

	function check_conditionals()
	{
		$param = ee()->TMPL->fetch_param('version');
		$member_group = ee()->session->userdata('group_id');
		
		if ( ! $param OR $param == 1)
		{
			if ($member_group == 1 || $member_group == 6)
			{
				return 'open|preview';  
			}
			
			return 'open';
		}

		return 'open';
	}


// ----------------------------------------
// Plugin Usage
// ----------------------------------------
// This function describes how the plugin is used.
// Make sure and use output buffering
function usage()
{
ob_start();
?>
To use as the parameter in another tag, use the parse parameter in the outer tag:

{exp:channel:entries parse="inward" status="{exp:cond_status}"}



<?php
$buffer = ob_get_contents();
ob_end_clean();
return $buffer;
}
// END
} //end class