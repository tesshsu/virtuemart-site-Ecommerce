<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<h1><?php echo JText::_("CATEGORIES_ASSIGN_SELECTED_FOR_USER"); ?> '<?php echo $this->joomlauser->name; ?>'</h1>
<?php

foreach ($this->catogries as $category)
{
		echo "<h3>".$category->title ."</h3>";
}

if (count($this->catogries) == 0)
	echo "<h3>None Selected</h3>";
