<?php
$plugin_info = array(
	'pi_name' => 'Date Parameter',
	'pi_version' => '.1',
	'pi_author' => 'EE',
	'pi_author_url' => 'http://ellislab.com/',
	'pi_description' => 'Give it an offset, it gives you a formated date',
	'pi_usage' => Date_param::usage()
);

class Date_param {

	public $return_data;
	
	public function __construct()
	{

		$this->return_data = $this->_get_date();

	}

	function _get_date()
	{	
		$offset = ee()->TMPL->fetch_param('offset');
		$date = ee()->TMPL->fetch_param('date');
		$format = ee()->TMPL->fetch_param('format');
		
		// Not localized yet
		if ( ! $date)
		{
			$date = ee()->localize->now;
		}
		
		if ($offset)
		{
			if (strpos($offset, '-') === 0)
			{
				$offset = str_replace('-', '', $offset);
				$date = $date - $offset;
			}
			else
			{
				$date = $date + $offset;
			}
		}

		// Localized formatted date
		if ($format)
		{
			$date = ee()->localize->format_date($format, $date);
		}
		
		return $date;
	}

	// ----------------------------------------
	// Plugin Usage
	// ----------------------------------------
	// This function describes how the plugin is used.
	// Make sure and use output buffering

	static function usage()
	{
ob_start();
?>
{exp:date_param offset="-86400" format="%Y-%m-%d %H:%i"}
Will return the current date minus 86400 seconds, i.e., 24 hours ago.

Designed to be used in the channel entry parameter as a start_on date.

{exp:channel:entries parse="inward" limit="5" start_on='{exp:date_param offset="-86400" format="%Y-%m-%d %H:%i"}'}
{title} - {entry_date format="%Y-%m-%d %H:%i"}<br>
{/exp:channel:entries}

Note the use of the parse parameter in the entry tag- that allows you to use a tag as a parameter.


There are three parameters you can set in the plugin:

- offset - number of seconds to offset the date.  Positive numbers set date in
  the future, negative numbers set date in the past.  No offset just returns
  untransformed date.
- date - date in utc.  Defaults to the current date.
- format - date format.

<?php
$buffer = ob_get_contents();
ob_end_clean();
return $buffer;
	}

	// END
} //end class
