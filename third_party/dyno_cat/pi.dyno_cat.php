<?php


$plugin_info = array(
  'pi_name' => 'DynoCat',
  'pi_version' => '2.0',
  'pi_author' => 'Rob1',
  'pi_author_url' => 'http://media-cow.net/',
  'pi_description' => 'A dynamic version of the {exp:channel:categories} tag',
  'pi_usage' => Dyno_cat::usage()
  );

class Dyno_cat {
// User definable variables- specify how to enclose the category count number

var $before_number = '(';
var $after_number = ')';
var $zero_count = '';


	    // Used to grab the current cat

	var $uri				= '';
	var $reserved_cat_segment 	= '';
	var $use_category_names		= FALSE;
	var $is_cat_page			= FALSE;
	var $c_current_cat 			= '';
	    // These are used with the nested category trees
   
    var $category_list  		= array();
	var $cat_full_array			= array();
	var $cat_array				= array();
	var $temp_array				= array();    
	var $c_array				= array();
	
	

    // ----------------------------------------
    //  Constructor
    // ----------------------------------------


    public function __construct()
    {
		$this->EE =& get_instance();   
		
		if ($this->EE->config->item("use_category_name") == 'y' AND $this->EE->config->item("reserved_category_word") != '')
		{
			$this->use_category_names	= $this->EE->config->item("use_category_name");
			$this->reserved_cat_segment	= $this->EE->config->item("reserved_category_word");
		}
    }
    // END
	
	
	
    // ----------------------------------------
    //  channel Categories
    // ----------------------------------------

    function categories()
    {		
		
		
// rob1 mod- ditches a query if group id set manually
			if ($this->EE->TMPL->fetch_param('group_id'))
			{
				$group_id = $this->EE->TMPL->fetch_param('group_id');
			}
// end mod- sorta- clean up the else stuff
			else
			{
		
            	$sql = "SELECT cat_group FROM exp_channels";
		
            	if ($channel = $this->EE->TMPL->fetch_param('channel'))
            	{
                	$sql .= " WHERE channel_name = '".$this->EE->db->escape_str($channel)."'";
		    	}
		    
		    	$query = $this->EE->db->query($sql);
		        
            	if ($query->num_rows() != 1)
            	{
                	return '';
            	}
            
            	$group_id = $query->row('cat_group');
			}
		
			
		$parent_only = ($this->EE->TMPL->fetch_param('parent_only') == 'yes') ? TRUE : FALSE;

		if ($this->EE->TMPL->fetch_param('disable') != 'count') 
		{ 
			$this->do_count($group_id); 
		}

		$this->get_current_cat($group_id);
		
		if ( $this->is_cat_page === FALSE && $this->EE->TMPL->fetch_param('default_parent_only') == 'yes') 
		 {  
			$parent_only = TRUE; 
		}                     		
// end mod



		$path = array();
		
		if (preg_match_all("#".LD."path(=.+?)".RD."#", $this->EE->TMPL->tagdata, $matches)) 
		{
			for ($i = 0; $i < count($matches['0']); $i++)
			{			
				if ( ! isset($path[$matches['0'][$i]]))
				{
					$path[$matches['0'][$i]] = $this->EE->functions->create_url($this->EE->functions->extract_path($matches['1'][$i]));
				}
			}
		}
		                
		$str = '';
		
		if ($this->EE->TMPL->fetch_param('style') == '' OR $this->EE->TMPL->fetch_param('style') == 'nested')
        {
			$this->category_tree(
									array(
											'group_id'		=> $group_id, 
											'template'		=> $this->EE->TMPL->tagdata, 
											'path'			=> $path, 
											'channel_array' 	=> '',
											'parent_only'	=> $parent_only,
											'show_empty'	=> $this->EE->TMPL->fetch_param('show_empty')
										  )
								);
				

			if (count($this->category_list) > 0)
			{

var_dump($this->cat_array);

				$i = 0;
				
				$id_name = ( ! $this->EE->TMPL->fetch_param('id')) ? 'nav_categories' : $this->EE->TMPL->fetch_param('id');
				
				$this->category_list['0'] = '<ul id="'.$id_name.'">'."\n";
			
				foreach ($this->category_list as $val)
				{
					$str .= $val;                    
				}
			}
		}
		else
		{
		
		$this->linear($group_id, $parent_only, $path);
		
		}



		return $str;
		
	}
		
		
		
	
	
		
		
function linear($group_id, $parent_only, $path)
{
			$str = '';	
		
			$show_empty = $this->EE->TMPL->fetch_param('show_empty');
		
			if ($show_empty == 'no')
			{	
				// First we'll grab all category ID numbers

				$query = $this->EE->db->query("SELECT cat_id, parent_id FROM exp_categories WHERE group_id ='$group_id' ORDER BY parent_id, cat_order");
				
				$all = array();
				
				// No categories exist?  Let's go home..
				if ($query->num_rows() == 0)
					return false;
			
			$all = array();
			$not_parents = array();
			
			$this->all_tops = array();			

				foreach($query->result_array() as $row2)
				{
				$all[$row2['cat_id']] = $row2['parent_id'];

				if ($row2['parent_id'] == 0) 
				{
				$this->all_tops[$row2['cat_id']][] = $row2['cat_id'];
				}
				else { $not_parents[$row2['cat_id']] = $row2['parent_id']; }

				} // ends query loop


while (count($not_parents) > 0) 
{
		foreach ($not_parents as $cat_id => $parent_id)
		{

        	foreach ($this->all_tops as $top_cat_k => $cat_val_a)
            {
			 	if ( in_array($parent_id, $cat_val_a))
				{
                 $this->all_tops[$top_cat_k][] = $cat_id;
                 unset($not_parents[$cat_id]);
		 		}
	         }
		}
}


			foreach ($this->all_tops as $top_cat_k => $cat_val_a)
			{
				if (in_array($this->c_current_cat, $cat_val_a))				
    			{ $this->dyn_cats = $cat_val_a; }	
			}
				
				// Next we'l grab only the assigned categories
			$sql = "SELECT DISTINCT(exp_categories.cat_id), parent_id FROM exp_categories, exp_channel_titles
					LEFT JOIN exp_category_posts ON exp_categories.cat_id = exp_category_posts.cat_id
					WHERE group_id ='$group_id' AND exp_category_posts.entry_id = exp_channel_titles.entry_id ";
					
			if ($this->EE->TMPL->fetch_param('status'))
			{
			$status = $this->EE->TMPL->fetch_param('status');
			$sql .= $this->EE->functions->sql_andor_string($status, 'exp_channel_titles.status').' ';
			}
			if ($this->EE->TMPL->fetch_param('channel_id'))
			{
			$channel_id = $this->EE->TMPL->fetch_param('channel_id');
			$sql .= $this->EE->functions->sql_andor_string($channel_id, 'exp_channel_titles.channel_id').' ';
			}			
			$sql .= "AND exp_category_posts.cat_id IS NOT NULL";

			
				if ($parent_only === TRUE)
				{
					$sql .= " AND parent_id = 0";
				}
				
				$sql .= " ORDER BY parent_id, cat_order";
				
				$query = $this->EE->db->query($sql);
				if ($query->num_rows() == 0)
					return false;
					
				// All the magic happens here, baby!!
				
				foreach($query->result_array() as $row)
				{
					if ($row['parent_id'] != 0)
					{
						$this->find_parent($row['parent_id'], $all);
					}	
					
					$this->cat_full_array[] = $row['cat_id'];
				}
					

//end			
				
			
				$this->cat_full_array = array_unique($this->cat_full_array);
					
				$sql = "SELECT cat_id, parent_id, cat_name, cat_image, cat_description FROM exp_categories WHERE cat_id IN (";
		
				foreach ($this->cat_full_array as $val)
				{
					$sql .= $val.',';
				}
			
				$sql = substr($sql, 0, -1).')';
				
				$sql .= " ORDER BY parent_id, cat_order";
				
				$query = $this->EE->db->query($sql);
					  
				if ($query->num_rows() == 0)
			
				{	return false; }
			      
			}
			else // if not limiting to cats with content
			{
				$sql = "SELECT exp_categories.cat_name, exp_categories.cat_image, exp_categories.cat_description, exp_categories.cat_id, exp_categories.parent_id FROM exp_categories WHERE group_id ='$group_id'";
						
				if ($parent_only === TRUE)

				{
					$sql .= " AND parent_id = 0";
				}
				
				$sql .= " GROUP BY exp_categories.cat_name ORDER BY parent_id, cat_order";
							
				$query = $this->EE->db->query($sql);
								  
				if ($query->num_rows() == 0)
				{
						return '';
				}
	
			}  
			
			// Here we check the show parameter to see if we have any 
			// categories we should be ignoring or only a certain group of 
			// categories that we should be showing.  By doing this here before
			// all of the nested processing we should keep out all but the 
			// request categories while also not having a problem with having a 
			// child but not a parent.  As we all know, categories are not asexual.
		
			if ($this->EE->TMPL->fetch_param('show') !== FALSE)
			{
				if (ereg("^not ", $this->EE->TMPL->fetch_param('show')))
				{
					$not_these = explode('|', trim(substr($this->EE->TMPL->fetch_param('show'), 3)));
				}
				else
				{
					$these = explode('|', trim($this->EE->TMPL->fetch_param('show')));
				}
			}
			
			foreach($query->result_array() as $row)
			{
			
 // rob1 mod - skips over the subcats if they aren't in the	 same array as the current cat		
		
			if (isset($this->dyn_cats) && $row['parent_id'] != 0 && !in_array($row['cat_id'], $this->dyn_cats))
			{
				continue;
			}
// end mod
				if (isset($not_these) && in_array($row['cat_id'], $not_these))
				{
					continue;
				}
				elseif(isset($these) && ! in_array($row['cat_id'], $these))
				{
					continue;
				}
			
				$this->temp_array[$row['cat_id']]  = array($row['cat_id'], $row['parent_id'], '1', $row['cat_name'], $row['cat_description'], $row['cat_image'], $row['cat_url_title'], $depth);
			}
															
			foreach($this->temp_array as $key => $val) 
			{				
				if (0 == $val['1'])
				{    
					$this->cat_array[] = $val;
					$this->process_subcategories($key);
				}
			}
			
			unset($this->temp_array);
							
			foreach ($this->cat_array as $key => $val)
			{
				$chunk = str_replace(LD.'category_name'.RD, $val['3'], $this->EE->TMPL->tagdata);
				$chunk = str_replace(LD.'category_description'.RD, $val['4'], $chunk);
				$chunk = str_replace(LD.'category_image'.RD, $val['5'], $chunk);
				$chunk = str_replace(LD.'category_id'.RD, $val['0'], $chunk);

// rob1 mods				
				$chunk = str_replace(LD.'parent_id'.RD, $val['1'], $chunk);
				$chunk = str_replace(LD.'category_url'.RD, $val['4'], $chunk);
				$chunk = str_replace(LD.'depth'.RD, $val['5'], $chunk);
				
if (isset($this->c_array[$val['0']])) {				
				$chunk = str_replace(LD.'category_count'.RD, $this->before_number.$this->c_array[$val['0']].$this->after_number, $chunk);
}
else
{
$chunk = str_replace(LD.'category_count'.RD, $this->zero_count, $chunk);
}

				
				if ($val['0'] != $this->c_current_cat)
				{
					$chunk = preg_replace("/".LD."if c_current_category\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "", $chunk);	
				}
				else
				{
					$chunk = preg_replace("/".LD."if c_current_category\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "\\1", $chunk);	
				}


// end rob1 mod
				
				if ($val['4']== '')
				{
					$chunk = preg_replace("/".LD."if category_description\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "", $chunk);	
				}
				else
				{
					$chunk = preg_replace("/".LD."if category_description\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "\\1", $chunk);	
				}
				
				foreach($path as $k => $v)
				{	
					if ($this->use_category_names == TRUE)
					{
						$chunk = str_replace($k, $this->EE->functions->remove_double_slashes($v.'/'.$this->reserved_cat_segment.'/'.$val['3'].'/'), $chunk); 
					}
					else
					{
						$chunk = str_replace($k, $this->EE->functions->remove_double_slashes($v.'/C'.$val['0'].'/'), $chunk); 
					}
				}	
				
				$str .= $chunk;
			}
		    
			if ($this->EE->TMPL->fetch_param('backspace'))
			{            
				$str = rtrim(str_replace("&#47;", "/", $str));
				$str = substr($str, 0, - $this->EE->TMPL->fetch_param('backspace'));
				$str = str_replace("/", "&#47;", $str);
			}

       return $str;
    }
    // END

		
	function get_current_cat($group_id = '')
	{
    // ----------------------------------------------
    //  Parse the URL query string - ripped from channel module
    // ----------------------------------------------
 
 	$qstring = $this->EE->uri->query_string;


		if ($qstring != '')
		{		
		// --------------------------------------
		//  Parse category indicator - also ripped from the channel module
		// --------------------------------------
				
		// Text version of the category

			if ($this->use_category_names == 'y')
			{
				$q_array = explode("/", $qstring);
				 
				if (in_array($this->reserved_cat_segment, $q_array))
				{
					$q_array_r = array_flip($q_array);
					$cat_key = $q_array_r[$this->reserved_cat_segment] + 1;
					
					$catn = (isset($q_array[$cat_key])) ? $q_array[$cat_key] : FALSE;
					
					if ($catn)
					{
						$this->c_current_cat = $catn;
						// rob1 mod
						$this->is_cat_page = TRUE;
						// end mod
						
						$g = ($group_id != '') ? "AND group_id=".$group_id : '';
						
						$result = $this->EE->db->query("SELECT cat_id FROM exp_categories WHERE cat_url_title='".$this->EE->db->escape_str($this->c_current_cat)."' $g LIMIT 1");
					
						if ($result->num_rows() == 1)
						{
							$this->c_current_cat = $result->row('cat_id');
						}
					}
				}
			}
			else
			{
				// Numeric version of the category
				if (preg_match("#^C(\d+)#", $qstring, $match))
				{		
				// rob1 mod
				$this->is_cat_page = TRUE;
				// end mod
				$this->c_current_cat = $match['1'];	
				}
			}
		}
	}
	



    function do_count($group_id)
	{

	
		if ($this->EE->TMPL->fetch_param('status') || $this->EE->TMPL->fetch_param('channel_id') )
		{
			$status = $this->EE->TMPL->fetch_param('status');
			$sql = "SELECT count(exp_category_posts.entry_id) AS count, exp_categories.cat_id FROM exp_categories, exp_category_posts, exp_channel_titles WHERE group_id ='$group_id' AND exp_category_posts.cat_id = exp_categories.cat_id AND   exp_category_posts.entry_id = exp_channel_titles.entry_id ";
			
			if ($this->EE->TMPL->fetch_param('status'))
			{
			$status = $this->EE->TMPL->fetch_param('status');
			$sql .= $this->EE->functions->sql_andor_string($status, 'exp_channel_titles.status').' ';
			}
			if ($this->EE->TMPL->fetch_param('channel_id'))
			{
			$channel_id = $this->EE->TMPL->fetch_param('channel_id');
			$sql .= $this->EE->functions->sql_andor_string($channel_id, 'exp_channel_titles.channel_id').' ';
			}			
			$sql .= "GROUP BY exp_categories.cat_id";
			$sql = $this->EE->db->query($sql);
			
		}    
		else
		{
			$sql = $this->EE->db->query("SELECT count(exp_category_posts.entry_id) AS count, exp_categories.cat_id FROM exp_categories, exp_category_posts WHERE group_id ='$group_id' AND exp_category_posts.cat_id = exp_categories.cat_id GROUP BY exp_categories.cat_id");
		}
		
			// No categories exist?  Let's go home..
			if ($sql->num_rows() == 0)
				return false;
			
			foreach($sql->result_array() as $row)
			{
				$this->c_array[$row['cat_id']] = $row['count'];
			}	

	}
	
	
	

    //--------------------------------
    // Category Tree
    //--------------------------------

    // This function and the next create a nested, hierarchical category tree

    function category_tree($cdata = array())
    {
        
        $default = array('group_id', 'path', 'template', 'depth', 'channel_array', 'parent_only', 'show_empty');
        
        foreach ($default as $val)
        {
        	$$val = ( ! isset($cdata[$val])) ? '' : $cdata[$val];
        }
        
        if ($group_id == '')
        {
            return false;
        }
        
		// -----------------------------------
		//  Are we showing empty categories
		// -----------------------------------
		
		// If we are only showing categories that have been assigned to entries
		// we need to run a couple queries and run a recursive function that
		// figures out whether any given category has a parent.
		// If we don't do this we will run into a problem in which parent categories
		// that are not assigned to a channel will be supressed, and therefore, any of its
		// children will be supressed also - even if they are assigned to entries.
		// So... we will first fetch all the category IDs, then only the ones that are assigned
		// to entries, and lastly we'll recursively run up the tree and fetch all parents.
		// Follow that?  No?  Me neither... 
           
		if ($show_empty == 'no')
		{	
			// First we'll grab all category ID numbers
		
			$query = $this->EE->db->query("SELECT cat_id, parent_id FROM exp_categories WHERE group_id ='$group_id' ORDER BY parent_id, cat_order");
			
			$all = array();
			$not_parents = array();
			
			$this->all_tops = array();			

				foreach($query->result_array() as $row2)
				{
				$all[$row2['cat_id']] = $row2['parent_id'];

				if ($row2['parent_id'] == 0) 
				{
				$this->all_tops[$row2['cat_id']][] = $row2['cat_id'];
				}
				else { $not_parents[$row2['cat_id']] = $row2['parent_id']; }

				} // ends query loop


while (count($not_parents) > 0) 
{
		foreach ($not_parents as $cat_id => $parent_id)
		{

        	foreach ($this->all_tops as $top_cat_k => $cat_val_a)
            {
			 	if ( in_array($parent_id, $cat_val_a))
				{
                 $this->all_tops[$top_cat_k][] = $cat_id;
                 unset($not_parents[$cat_id]);
		 		}
	         }
		}
}


			foreach ($this->all_tops as $top_cat_k => $cat_val_a)
			{
				if (in_array($this->c_current_cat, $cat_val_a))				
    			{ $this->dyn_cats = $cat_val_a; }	
			}

// end mod




			// Next we'l grab only the assigned categories

			$sql = "SELECT DISTINCT(exp_categories.cat_id), parent_id FROM exp_categories, exp_channel_titles
					LEFT JOIN exp_category_posts ON exp_categories.cat_id = exp_category_posts.cat_id
					WHERE group_id ='$group_id' AND exp_category_posts.entry_id = exp_channel_titles.entry_id ";
					
			if ($this->EE->TMPL->fetch_param('status'))
			{
			$status = $this->EE->TMPL->fetch_param('status');
			$sql .= $this->EE->functions->sql_andor_string($status, 'exp_channel_titles.status').' ';
			}
			if ($this->EE->TMPL->fetch_param('channel_id'))
			{
			$channel_id = $this->EE->TMPL->fetch_param('channel_id');
			$sql .= $this->EE->functions->sql_andor_string($channel_id, 'exp_channel_titles.channel_id').' ';
			}			
			$sql .= "AND exp_category_posts.cat_id IS NOT NULL ";


			if ($parent_only === TRUE)
			{
				$sql .= " AND parent_id = 0";
			}
			
			$sql .= " ORDER BY parent_id, cat_order";
						

			$query = $this->EE->db->query($sql);
			if ($query->num_rows() == 0)
				{ return false; }
				
			// All the magic happens here, baby!!
			
			foreach($query->result_array() as $row)
			{
			
				if ($row['parent_id'] != 0)
				{
					$this->find_parent($row['parent_id'], $all);
				}	
				
				$this->cat_full_array[] = $row['cat_id'];
			}
        
        	$this->cat_full_array = array_unique($this->cat_full_array);
        		
			$sql = "SELECT * FROM exp_categories WHERE cat_id IN (";
        
        	foreach ($this->cat_full_array as $val)
        	{
        		$sql .= $val.',';
        	}
        
			$sql = substr($sql, 0, -1).')';
			
			$sql .= " ORDER BY parent_id, cat_order";
			
			$query = $this->EE->db->query($sql);
				  
			if ($query->num_rows() == 0)
				{ return false; } 


// end mod
			       
        }
		else // if selecting empty cats
		{
        
			$sql = "SELECT * FROM exp_categories WHERE group_id ='$group_id' ";

			if ($parent_only === TRUE)
			{
				$sql .= " AND parent_id = 0";
			}
			
			$sql .= " ORDER BY parent_id, cat_order";
			
			$query = $this->EE->db->query($sql);

			if ($query->num_rows() == 0)
			{ return FALSE; }

			$all = array();
			$this->all_tops = array();
			$not_parents = array();	

				foreach($query->result_array() as $row2)
				{
				$all[$row2['cat_id']] = $row2['parent_id'];

				if ($row2['parent_id'] == 0) 
				{
				$this->all_tops[$row2['cat_id']][] = $row2['cat_id'];
				}
				else { $not_parents[$row2['cat_id']] = $row2['parent_id']; }

				} // ends query loop


while (count($not_parents) > 0) 
{
		foreach ($not_parents as $cat_id => $parent_id)
		{

        	foreach ($this->all_tops as $top_cat_k => $cat_val_a)
            {
			 	if ( in_array($parent_id, $cat_val_a))
				{
                 $this->all_tops[$top_cat_k][] = $cat_id;
                 unset($not_parents[$cat_id]);
		 		}
	         }
		}
}

					
			foreach ($this->all_tops as $top_cat_k => $cat_val_a)
			{
			if (in_array($this->c_current_cat, $cat_val_a))				
    		{ $this->dyn_cats = $cat_val_a; }	
			}


// end mod



		}		
		
		// Here we check the show parameter to see if we have any 
		// categories we should be ignoring or only a certain group of 
		// categories that we should be showing.  By doing this here before
		// all of the nested processing we should keep out all but the 
		// request categories while also not having a problem with having a 
		// child but not a parent.  As we all know, categories are not asexual
		
		if ($this->EE->TMPL->fetch_param('show') !== FALSE)
		{
			if (ereg("^not ", $this->EE->TMPL->fetch_param('show')))
			{
				$not_these = explode('|', trim(substr($this->EE->TMPL->fetch_param('show'), 3)));
			}
			else
			{
				$these = explode('|', trim($this->EE->TMPL->fetch_param('show')));
			}
		}
		
	
		foreach($query->result_array() as $row)
		{
// rob1 mod - skips over the subcats if they aren't in the same array as the current cat		
			
		
			if (isset($this->dyn_cats) && $this->is_cat_page == TRUE && $row['parent_id'] != 0 && !in_array($row['cat_id'], $this->dyn_cats))
			{
				continue;
			}
// end mod
		
			if (isset($not_these) && in_array($row['cat_id'], $not_these))
			{
				continue;
			}
			elseif(isset($these) && ! in_array($row['cat_id'], $these))
			{
				continue;
			}
		
			$this->cat_array[$row['cat_id']]  = array($row['parent_id'], $row['cat_name'], $row['cat_image'], $row['cat_description'], $row['cat_url_title'], 0);
		}
 
    	$this->temp_array = $this->cat_array;
    	
    	$open = 0;
    	
        foreach($this->cat_array as $key => $val) 
        { 
            if (0 == $val['0'])
            {
				if ($open == 0)
				{
					$open = 1;
					
					$this->category_list[] = "<ul>\n";
				}
				
				$chunk = str_replace(LD.'category_id'.RD, $key, $template);
				$chunk = str_replace(LD.'category_name'.RD, $val['1'], $chunk);
				$chunk = str_replace(LD.'category_image'.RD, $val['2'], $chunk);
				$chunk = str_replace(LD.'category_description'.RD, $val['3'], $chunk);
// rob1 mods				
				$chunk = str_replace(LD.'category_url'.RD, $val['4'], $chunk);
				$chunk = str_replace(LD.'depth'.RD, 0, $chunk);
				$chunk = str_replace(LD.'parent_id'.RD, $val['0'], $chunk);

				if (isset($this->c_array[$key])) {				
				$chunk = str_replace(LD.'category_count'.RD, $this->before_number.$this->c_array[$key].$this->after_number, $chunk);
				}
				else
				{
				$chunk = str_replace(LD.'category_count'.RD, $this->zero_count, $chunk);
				}
				
				if ($key != $this->c_current_cat)
				{
					$chunk = preg_replace("/".LD."if c_current_category\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "", $chunk);	
				}
				else
				{
					$chunk = preg_replace("/".LD."if c_current_category\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "\\1", $chunk);	
				}

// end rob1 mod

				
				if ($val['3'] == '')
				{
					$chunk = preg_replace("/".LD."if category_description\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "", $chunk);	
				}
				else
				{
					$chunk = preg_replace("/".LD."if category_description\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "\\1", $chunk);	
				}
            					
				foreach($path as $pkey => $pval)
				{
					if ($this->use_category_names == TRUE)
					{
						$chunk = str_replace($pkey, $this->EE->functions->remove_double_slashes($pval.'/'.$this->reserved_cat_segment.'/'.$val['4'].'/'), $chunk); 
					}
					else
					{
						$chunk = str_replace($pkey, $this->EE->functions->remove_double_slashes($pval.'/C'.$key.'/'), $chunk); 
					}
				}	
            	   
				$this->category_list[] = "\t<li>".$chunk;            	
				
				if (is_array($channel_array))
				{
					$fillable_entries = 'n';
					
					foreach($channel_array as $k => $v)
					{
						$k = substr($k, strpos($k, '_') + 1);
					
						if ($key == $k)
						{
							if ($fillable_entries == 'n')
							{
								$this->category_list[] = "\n\t\t<ul>\n";
								$fillable_entries = 'y';
							}
														
							$this->category_list[] = "\t\t\t$v";
						}
					}
				}
				
				if (isset($fillable_entries) && $fillable_entries == 'y')
				{
					$this->category_list[] = "\t\t</ul>\n";
				}
								
				$this->category_subtree(
											array(
													'parent_id'		=> $key, 
													'path'			=> $path, 
													'template'		=> $template,
													'channel_array' 	=> $channel_array
												  )
									);
				$t = '';
				
				if (isset($fillable_entries) && $fillable_entries == 'y')
				{
					$t .= "\t";
				}
				
				$this->category_list[] = $t."</li>\n";
				
				unset($this->temp_array[$key]);
				
				$this->close_ul(0);
            }
        }        
    }
    // END  
    
    
    
    //--------------------------------
    // Category Sub-tree
    //--------------------------------
        
    function category_subtree($cdata = array())
    {
        
        $default = array('parent_id', 'path', 'template', 'depth', 'channel_array', 'show_empty');
        
        foreach ($default as $val)
        {
        		$$val = ( ! isset($cdata[$val])) ? '' : $cdata[$val];
        }
        
        $open = 0;
        
        if ($depth == '') 
        		$depth = 1;
                
		$tab = '';
		for ($i = 0; $i <= $depth; $i++)
			$tab .= "\t";
        
		$done = array();
		
		foreach($this->cat_array as $key => $val) 
        {
			$done[] = $key;
			
			if ($parent_id == $val['0'])
            {
  				//$skip = ($parent_id == 0 OR in_array($this->c_current_cat, $done)) ? FALSE : TRUE;

				//if ($skip) { continue; }

          		if ($open == 0)
				{
					$open = 1;            		
					$this->category_list[] = "\n".$tab."<ul>\n";
				}
				
				$chunk = str_replace(LD.'category_id'.RD, $key, $template);
				$chunk = str_replace(LD.'category_name'.RD, $val['1'], $chunk);
				$chunk = str_replace(LD.'category_image'.RD, $val['2'], $chunk);
				$chunk = str_replace(LD.'category_description'.RD, $val['3'], $chunk);
				$chunk = str_replace(LD.'category_url'.RD, $val['4'], $chunk);
				$chunk = str_replace(LD.'depth'.RD, $depth, $chunk);

// rob1 mods				
				$chunk = str_replace(LD.'parent_id'.RD, $val['0'], $chunk);

if (isset($this->c_array[$key])) {				
				$chunk = str_replace(LD.'category_count'.RD, $this->before_number.$this->c_array[$key].$this->after_number, $chunk);
}
else
{
$chunk = str_replace(LD.'category_count'.RD, $this->zero_count, $chunk);
}

				
				if ($key != $this->c_current_cat)
				{
					$chunk = preg_replace("/".LD."if c_current_category\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "", $chunk);	
				}
				else
				{
					$chunk = preg_replace("/".LD."if c_current_category\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "\\1", $chunk);	
				}



// end rob1 mod

				
				if ($val['3'] == '')
				{
					$chunk = preg_replace("/".LD."if category_description\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "", $chunk);	
				}
				else
				{
					$chunk = preg_replace("/".LD."if category_description\s*".RD."(.*?)".LD.SLASH."if".RD."/s", "\\1", $chunk);	
				}
		
				foreach($path as $pkey => $pval)
				{
					if ($this->use_category_names == TRUE)
					{
						$chunk = str_replace($pkey, $this->EE->functions->remove_double_slashes($pval.'/'.$this->reserved_cat_segment.'/'.$val['4'].'/'), $chunk); 
					}
					else
					{
						$chunk = str_replace($pkey, $this->EE->functions->remove_double_slashes($pval.'/C'.$key.'/'), $chunk); 
					}
				}	
				
				$this->category_list[] = $tab."\t<li>".$chunk;
				
				if (is_array($channel_array))
				{
					$fillable_entries = 'n';
					
					foreach($channel_array as $k => $v)
					{
						$k = substr($k, strpos($k, '_') + 1);
					
						if ($key == $k)
						{
							if ( ! isset($fillable_entries) || $fillable_entries == 'n')
							{
								$this->category_list[] = "\n{$tab}\t\t<ul>\n";
								$fillable_entries = 'y';
							}
							
							$this->category_list[] = "{$tab}\t\t\t$v";            			
						}
					}
				}
				 
				if (isset($fillable_entries) && $fillable_entries == 'y')
				{
					$this->category_list[] = "{$tab}\t\t</ul>\n";
				}
				 
				$t = '';
												
				if ($this->category_subtree(
											array(
													'parent_id'		=> $key, 
													'path'			=> $path, 
													'template'		=> $template,
													'depth' 			=> $depth + 2,
													'channel_array' 	=> $channel_array
												  )
									) != 0 );
			
			if (isset($fillable_entries) && $fillable_entries == 'y')
			{
				$t .= "$tab\t";
			}        
							
				$this->category_list[] = $t."</li>\n";
				
				unset($this->temp_array[$key]);
				
				$this->close_ul($parent_id, $depth + 1);
            }
        } 
        return $open; 
    }
    // END



    //--------------------------------
    // Close </ul> tags
    //--------------------------------

	// This is a helper function to the above
	
    function close_ul($parent_id, $depth = 0)
    {	
		$count = 0;
		
		$tab = "";
		for ($i = 0; $i < $depth; $i++)
		{
			$tab .= "\t";
		}
    	
        foreach ($this->temp_array as $val)
        {
         	if ($parent_id == $val['0']) 
         	
         	$count++;
        }
            
        if ($count == 0) 
        	$this->category_list[] = $tab."</ul>\n";
    }
	// END
	
	

    //--------------------------------
    // Locate category parent
    //--------------------------------
    // This little recursive gem will travel up the
    // category tree until it finds the category ID
    // number of any parents.  It's used by the function 
    // below

	function find_parent($parent, $all)
	{	
		foreach ($all as $cat_id => $parent_id)
		{
			if ($parent == $cat_id)
			{
				$this->cat_full_array[] = $cat_id;
				
				if ($parent_id != 0)
					$this->find_parent($parent_id, $all);				
			}
		}
	}
	// END






	   
    //--------------------------------
    // Process Subcategories
    //--------------------------------
        
    function process_subcategories($parent_id)
    {        
    		foreach($this->temp_array as $key => $val) 
        {
            if ($parent_id == $val['1'])
            {
				$this->cat_array[] = $val;
				$this->process_subcategories($key);
			}
        }
    }
    // END
	


// ----------------------------------------
  //  Plugin Usage
  // ----------------------------------------

  // This function describes how the plugin is used.
  //  Make sure and use output buffering

  function usage()
  {
  ob_start(); 
  ?>
The DynoCat plugin is a dynamic version of the {exp:channel:categories} tag with a few added tweaks.  Say you have a category structure like:

Blake's 7  
.....Gen  
Buffyverse  
.....Gen  
.....Het  
..........Buffy/Giles  
Harry Potter  
.....Gen 
	
When you are on a category page- let's say the Buffy/Giles category- your categories will display like:

Blake's 7  
Buffyverse  
.....Gen  
.....Het  
..........Buffy/Giles  
Harry Potter  

In other words, you only see the subcategories when they share the same top level category as the current category page.

Example code:

{exp:dyno_cat:categories channel="channel1" show_empty="no" status="open" default_parent_only="yes"}

{if c_current_category}<b>{/if}<a href="../../dyno_cat/final/%7Bpath=channel/test%7D">{category_name}</a> {category_count}
{if c_current_category}</b>{/if}
{/exp:dyno_cat:categories}

All the parameters and variables available in the {exp:channel:categories} are available, as well as a few additions.
Added parameters:

-default_parent_only - "yes" or "no" (default)
In order for anything 'dynamic' to happen, the parents_only parameter MUST be off (that's what it defaults to anyway).  However, I wanted my NON-category pages to show parents only.  This parameter determines whether subcats are shown on NON-category pages.

-status - accepts pipe deliniated lists of article status, used with the {count} variable AND in conjunction with the show_empty parameter.  Default is "all" statuses.

-group_id - if you manually specify the group id, it saves a query.  Otherwise it looks it up for you.

**Note- as with all EE tags, the cache="yes" refresh="60" parameters work with this tag, which is handy.

Added variables:
{category_count}
{parent_id}
{if c_current_category}{/if}

Configuration options:
If you open up this file in an editor, you can set three user defined variables at the top of the file:

var $before_number = '(';
var $after_number = ')';
var $zero_count = '';

These just specify enclosing code for the category count number (kinda useless) and $zero_count determines what is displayed for the {category_count} if the count is 0.



  <?php
  $buffer = ob_get_contents();
	
  ob_end_clean(); 

  return $buffer;
  }
  // END

} //end class