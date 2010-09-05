<?php
/**
* Stats file
*
* @package mpv_server
* @version $Id$
* @copyright (c) 2010 phpBB Group
* @license Internal use only
*
*/

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");

$phpEx = substr(strrchr(__FILE__, '.'), 1);
$root_path = './';

// Will define all needed things.
include($root_path . 'mpv_config.' . $phpEx);
include($root_path . 'includes/languages/' . MPV_LANG . '/lang.' . $phpEx);
include($root_path . 'includes/functions_mpv.' . $phpEx);
include($root_path . 'includes/mpv.' . $phpEx);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-gb" xml:lang="en-gb">
<head>
<style type="text/css">
table.data {
	border-width: 1px;
	border-spacing: 2px;
	border-style: outset;
	border-color: gray;
	border-collapse: separate;
	background-color: white;
	margin: 0 auto;
}
table.data th {
	border-width: 1px;
	padding: 1px;
	border-style: inset;
	border-color: gray;
	background-color: lightgray;
}
table.data td {
	border-width: 1px;
	padding: 5px;
	border-style: inset;
	border-color: gray;
	background-color: white;
}
</style>
<title><?php print $lang['TITLE_STATS']; ?></title>
</head>
<body>
<br /><br />

<?php
//Do we need to show some statistics?
if (sizeof($statistics))
{
	$mod_count = $property_count = $entries = $split_entry = $entries_values = array();
	
	foreach ($statistics as $tag => $properties)
	{
		$data_file = './store/data/' . $tag . '_data.txt';
		
		if (file_exists($data_file))
		{
			$contents = file_get_contents($data_file);
			$entries = explode("\r\n", $contents);
			
			foreach ($entries as $entry)
			{
				$mod_name = '';
				$split_entry = explode("||", $entry);
				
				foreach ($split_entry as $entry_value)
				{
					//Check for the MOD name and assign counts as needed
					if (empty($mod_name))
					{
						$mod_name = $entry_value;
						$mod_name_id = str_replace(' ', '_', $mod_name);
						if (isset($mod_count[$mod_name_id]))
						{
							$mod_count[$mod_name_id] += 1;
						}
						else
						{
							$mod_count = array_merge($mod_count, array($mod_name_id => 1));
						}
						$entries_values[] = $entry_value;
						continue;
					}
					$entries_values[] = $entry_value;
					$entry_value_id = str_replace(' ', '_', $entry_value);
					if (isset($property_count[$entry_value_id]))
					{
						$property_count[$entry_value_id] += 1;
					}
					else
					{
						$property_count = array_merge($property_count, array($entry_value_id => 1));
					}
				}
			}
		}
	}
	
	//Remove the duplicates
	$entries_values = array_unique($entries_values);
	sort($entries_values);
	$row = "		<tr><td>%s</td><td style=\"text-align:right;\">%s</td></tr>\n";
	
	print '<table id="tag_data" class="data">
	<tr>
		<th>' . $lang['TAG_DATA'] . '</th><th>' . $lang['DATA_COUNT'] . "</th>
	</tr>\n";
	
	//Print out each row for the properties
	foreach ($entries_values as $entry)
	{
		$entry_id = str_replace(' ', '_', $entry);
		
		if (isset($property_count[$entry_id]))
		{
			print sprintf($row, $entry, $property_count[$entry_id]);
		}
	}
	print '</table>
<br /><br />
<table id="mod_names" class="data">
	<tr>
		<th>' . $lang['MOD_NAME'] . '</th><th>' . $lang['SUBMIT_COUNT'] . "</th>
	</tr>\n";
	
	//Print out each row for the MOD names
	foreach ($entries_values as $entry)
	{
		if (!empty($entry))
		{
			$entry_id = str_replace(' ', '_', $entry);
			
			if (isset($mod_count[$entry_id]))
			{
				print sprintf($row, $entry, $mod_count[$entry_id]);
			}
		}
	}
	print '</table>';
}

?>
</body>
</html>
