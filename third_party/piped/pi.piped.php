<?php


$plugin_info = array(
  'pi_name' => 'Piped',
  'pi_version' => '.1',
  'pi_author' => 'EE',
  'pi_author_url' => 'http://ellislab.com/',
  'pi_description' => 'Give it a query, it gives you a piped list of results',
  'pi_usage' => Piped::usage()
  );

class Piped {

    public function __construct()
    {
		$this->EE =& get_instance();   
		$this->return_data = $this->_do_query();
   }

    function _do_query()
    {		
		
		if (($sql = $this->EE->TMPL->fetch_param('sql')) === FALSE)
		{
			return FALSE;			
		}
		
		if (substr(strtolower(trim($sql)), 0, 6) != 'select')
		{
			return FALSE;			
		}

		$query = $this->EE->db->query($sql);
		
		if ($query->num_rows() == 0)
		{
			return $this->return_data = $this->EE->TMPL->no_results();
		}

		$out = '';
		
		foreach ($query->result_array() as $row)
		{
			$out .= implode('', $row).'|';
		}
		
		return $out;
	}


// ----------------------------------------
  //  Plugin Usage
  // ----------------------------------------

  // This function describes how the plugin is used.
  //  Make sure and use output buffering

  function usage()
  {
  ob_start(); 
  ?>

{exp:piper sql="Select member_id FROM exp_members WHERE group_id = 7"}

Will return a pipe delimited list of the results row.  So:

1|2|3|6

Note- if you pull back more than one field?  It just mushes the results together and is pretty useless.

  <?php
  $buffer = ob_get_contents();
	
  ob_end_clean(); 

  return $buffer;
  }
  // END

} //end class