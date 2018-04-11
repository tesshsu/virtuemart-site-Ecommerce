<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );
require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fst'.DS.'settings.php');
jimport('joomla.html.pane');


class FstsViewFsts extends JViewLegacy
{
 
    function display($tpl = null)
	{
		JToolBarHelper::title( JText::_( 'FREESTYLE_TESTIMONIALS' ), 'fst.png' );
		FSTAdminHelper::DoSubToolbar();
	
		parent::display($tpl);
	}
	
	function Item($title, $link, $icon, $help)
	{
?>
		<div class="fst_main_item fsj_tip" title="<?php echo JText::_($help); ?>">	
			<div class="fst_main_icon">
				<a href="<?php echo FSTRoute::x($link); ?>">
					<img src="<?php echo JURI::root( true ); ?>/administrator/components/com_fst/assets/images/<?php echo $icon;?>-48x48.png" width="48" height="48">
				</a>
			</div>
			<div class="fst_main_text">
				<a href="<?php echo FSTRoute::x($link); ?>">
					<?php echo JText::_($title); ?>
				</a>
			</div>
		</div>	
<?php
	}
}


