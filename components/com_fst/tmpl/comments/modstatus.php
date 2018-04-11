<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<div class="fst_moderate_status">
	<ul>
<?php if (is_array($this->_moderatecounts)) foreach ($this->_moderatecounts as $ident => $count) : ?>
<li><?php echo $this->handlers[$ident]->GetDesc(); ?>: <b><?php echo $count['count']; ?></b> - <a href="<?php echo FSTRoute::_( 'index.php?option=com_fst&view=admin&layout=moderate&ident=' . $ident ); ?>"><?php echo JText::_('VIEW_NOW'); ?></a></li>
<?php endforeach; ?>
	</ul>
</div>

