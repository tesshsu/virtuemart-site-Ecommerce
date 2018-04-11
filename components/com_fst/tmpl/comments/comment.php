<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<?php 

if (!$this->parser)
{
	$this->parser = new FSTParser();
	$this->parser->Load($this->template,$this->template_type);
}

$this->parser->Clear();

$modcolor = "";
if ($this->_permissions['mod_kb'])
{
	if ($this->comment['published'] == 0)
		$modcolor = "style='background-color: #eeeeff'";
	if ($this->comment['published'] == 2)
		$modcolor = "style='background-color: #ffeeee'";
}

$moderation = "";
if ($this->_permissions['mod_kb'] && array_key_exists('id',$this->comment)) {
	$moderation .= '<div class="fst_kb_mod_this">';
	$show_tick = ""; 
	$show_cross = ""; 
	$show_delete = "";
	$show_edit = "";
	if ($this->comment['published'] == 1) 
	{
		$show_tick = "style='display: none'";
		$show_delete = "style='display: none'";
	} else if ($this->comment['published'] == 2) 
	{
		$show_cross = "style='display: none'";
	} else if ($this->comment['published'] == 0) 
	{
		$show_delete = "style='display: none'";
	}	
	if (!$this->opt_no_edit)	
		$moderation .= "<img id='fst_comment_{$this->uid}_{$this->comment['id']}_edit' {$show_edit} src='". JURI::root( true )."/components/com_fst/assets/images/edit_16.png' width='16' height='16' onclick='fst_edit_comment({$this->uid}, {$this->comment['id']})' style='cursor:pointer' title='".JText::_('EDIT_COMMENT')."'>";
	
	$moderation .= "<img id='fst_comment_{$this->uid}_{$this->comment['id']}_tick' {$show_tick} src='". JURI::root( true )."/components/com_fst/assets/images/save_16.png' width='16' height='16' onclick='fst_approve_comment({$this->uid}, {$this->comment['id']})' style='cursor:pointer' title='".JText::_('APPROVE_COMMENT')."' >";
	$moderation .= "<img id='fst_comment_{$this->uid}_{$this->comment['id']}_cross' {$show_cross} src='". JURI::root( true )."/components/com_fst/assets/images/cancel_16.png' width='16' height='16' onclick='fst_remove_comment({$this->uid}, {$this->comment['id']})' style='cursor:pointer' title='".JText::_('REMOVE_COMMENT')."'>";
	$moderation .= "<img id='fst_comment_{$this->uid}_{$this->comment['id']}_delete' {$show_delete} src='". JURI::root( true )."/components/com_fst/assets/images/delete_16.png' width='16' height='16' onclick='fst_delete_comment({$this->uid}, {$this->comment['id']})' style='cursor:pointer' title='".JText::_('DELETE_COMMENT')."'>";
	$moderation .= "</div>";
}

if (!$this->use_website)
	$this->comment['website'] = "";

$custom = array();
if ($this->customfields)
{
	foreach($this->customfields as &$field)
	{
		if (!is_array($field)) continue;
		if (!array_key_exists("id", $field)) continue;
		
		if (array_key_exists('custom_' . $field['id'],$this->comment))
		{
			$val = $this->comment['custom_' . $field['id']];
			$this->parser->SetVar('custom_' . $field['id'], trim($val));
			if (strlen(trim($val)) > 0)
			{
				$custom[] =	$val;
			}
		}
	}
}

if (!array_key_exists('id',$this->comment))
	$this->comment['id'] = '';

$this->parser->SetVar('divid', "fst_comment_{$this->uid}_{$this->comment['id']}");

if (count($custom) > 0)
{
	$this->parser->SetVar('custom', implode(", ", $custom));	
} else {
	$this->parser->SetVar('custom', "");	
}

if (!function_exists("truncate"))
{
	function truncate($text, $length, &$is_trimmed, $suffix = '&hellip;', $isHTML = true) {
		$i = 0;
		$simpleTags=array('br'=>true,'hr'=>true,'input'=>true,'image'=>true,'link'=>true,'meta'=>true);
		$tags = array();
		if($isHTML){
			preg_match_all('/<[^>]+>([^<]*)/', $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
			foreach($m as $o){
				if($o[0][1] - $i >= $length)
					break;
				$t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
				// test if the tag is unpaired, then we mustn't save them
				if($t[0] != '/' && (!isset($simpleTags[$t])))
					$tags[] = $t;
				elseif(end($tags) == substr($t, 1))
					array_pop($tags);
				$i += $o[1][1] - $o[0][1];
			}
		}

		// output without closing tags
		$output = substr($text, 0, $length = min(strlen($text),  $length + $i));
		// closing tags
		$output2 = (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');

		// Find last space or HTML tag (solving problem with last space in HTML tag eg. <span class="new">)
		$r = preg_split('/<.*>| /', $output, -1, PREG_SPLIT_OFFSET_CAPTURE);
		$r2 = end($r);
		$pos = (int)end($r2);
		// Append closing tags to output
		$output.=$output2;

		// Get everything until last space
		$one = substr($output, 0, $pos);
		// Get the rest
		$two = substr($output, $pos, (strlen($output) - $pos));
		// Extract all tags from the last bit
		preg_match_all('/<(.*?)>/s', $two, $tags);
		
		// Re-attach tags
		$output = $one . implode($tags[0]);
		
		// Add suffix if needed
		if (strlen($text) > $length) 
		{ 
			$output .= $suffix; 
			$is_trimmed = true; 
		}

		//added to remove  unnecessary closure
		$output = str_replace('</!-->','',$output); 

		return $output;
	}
?>

<script>
function expand_test(test_id)
{
	var div = jQuery('#test_full_' + test_id);
	var html = div.html();
	jQuery('#test_short_' + test_id).html(html);	
}
</script>
<?php
}

if ($this->opt_max_length > 0 && strlen($this->comment['body']) > $this->opt_max_length)
{
	$randno = mt_rand(100000,999999);
	$result = array();
	$is_trimmed = false;
	$result[] = "<div id='test_short_".$randno."'>";
	$result[] = truncate($this->comment['body'], $this->opt_max_length, $is_trimmed, '');
	
	if ($is_trimmed)
	{
		$result[] = "&hellip; <a href='#' onclick='expand_test(" . $randno . ");return false;'>" . JText::_("read more") . "</a><div id='test_full_".$randno."' style='display:none'>" . $this->comment['body'] . "</div>";
		$result[] = "</div>";
		$this->comment['body'] = trim(implode($result));
	}
}

$this->comment['body'] = str_replace("\n","<br />",$this->comment['body']);

$this->parser->SetVar('created_nice', FST_Helper::Date($this->comment['created'], FST_DATETIME_SHORT));
$this->parser->SetVar('date', FST_Helper::Date($this->comment['created'], FST_DATE_SHORT));
$this->parser->SetVar('modcolor',$modcolor);
$this->parser->SetVar('moderation',$moderation);
$this->parser->AddVars($this->comment);
	
//print_p($this->parser);

echo $this->parser->Parse();
