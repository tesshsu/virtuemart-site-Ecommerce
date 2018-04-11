<?php
/*------------------------------------------------------------------------
# vm_migrate - Virtuemart 2 Migrator
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
jimport( 'joomla.html.html' );

$tick 		= JHtml::_('image','admin/tick.png', '', array('border' => 0), true);
$publish_x 	= JHtml::_('image','admin/publish_x.png', '', array('border' => 0), true);
$publish_y 	= JHtml::_('image','admin/publish_y.png', '', array('border' => 0), true);

$params = JComponentHelper::getParams('com_vmmigrate');
$show_disclaimer = $params->get('show_disclaimer', 1);
$show_not_installed = $params->get('show_not_installed', 1);

$doc = JFactory::getDocument();
$js = 'var warning_message_label="'.JText::_('VMMIGRATE_WARNING_LABEL').'";';
$js .= 'var confirm_message_label="'.JText::_('VMMIGRATE_WARNING_CONFIRM_PROCEED').'";';
$doc->addScriptDeclaration($js);


if ( is_array($this->extensionsFeed) && count($this->extensionsFeed) ) {
	JSubMenuHelper::addEntry('<h3>'.JText::_('VMMIGRATE_EXTENSION_ADDONS').'</h3>', '',false);
	foreach ($this->extensionsFeed as $item){
		JSubMenuHelper::addEntry($item->title, $item->link,false);
	}
}
?>
    <div class="container">
    	<ul class="nav nav-tabs">
        	<?php if ($show_disclaimer) { ?>
        	<li class="active"><a href="#disclaimer" data-toggle="tab"><?php echo JText::_('VMMIGRATE_IMPORTANT_INFO'); ?></a></li>
        	<?php } 
			$i=0;
			if (isset($this->extensions)) {
				foreach ($this->extensions as $extension) {
				$migratorClass = 'VMMigrateModel'.ucfirst($extension);
				$migratorInstance = new $migratorClass();
				$migratorInstance->setExtension($extension);
				$versions[$extension]['src'] = $migratorInstance->getSrcVersion();
				$versions[$extension]['dst'] = $migratorInstance->getDstVersion();
				$title = JText::_($extension);
				if ($versions[$extension]['src'] && $versions[$extension]['dst']) {
					if (JText::_($extension.'_VERSION_BOTH') != $extension.'_VERSION_BOTH') {
						$title = JText::sprintf($extension.'_VERSION_BOTH',$versions[$extension]['src'],$versions[$extension]['dst']);
					} else {
						$title = JText::sprintf($extension.'_VERSION_DST',$versions[$extension]['dst']);
					}
				} else if ($versions[$extension]['dst']) {
					$title = JText::sprintf($extension.'_VERSION_DST',$versions[$extension]['dst']);
				}
				$versions[$extension]['title'] = $title ;
				
				$tabclass = (!$show_disclaimer && $i==0) ? ' class="active"' : '';
				$i++;
				 ?>
            	<li<?php echo $tabclass; ?>><a href="#<?php echo $extension; ?>" data-toggle="tab"><?php echo $versions[$extension]['title'];?></a></li>
            	<?php
			}
			}
			?>
            
        </ul>
        <?php //print_a($versions);?>
        <div class="tab-content span5 pull-left">
        	<?php if ($show_disclaimer) { ?>
        	<div id="disclaimer" class="tab-pane active">
				<fieldset class="adminform">
					<?php
						echo JHtml::_('image', 'administrator/components/com_vmmigrate/assets/images/stop.png','STOP');
						echo JText::_('VMMIGRATE_DISCLAIMER');
					?>
				</fieldset>
           	</div>
        	<?php } 
			$i=0;
			foreach ($this->extensions as $extension) {
				$extensionSteps = $this->steps[$extension];
				$extensionMessages = $this->messages[$extension];

				$class = (!$show_disclaimer && $i==0) ? ' class="tab-pane active"' : ' class="tab-pane"';
				$i++;

				?>
                <div id="<?php echo $extension; ?>" <?php echo $class; ?>>
                	<?php
					if (count($extensionMessages)) {
						foreach ($extensionMessages as $type => $messagesByType) { 
							if (count($messagesByType)) {
								foreach ($messagesByType as $message) {
									?>
									<div class="alert alert-<?php echo $type; ?>">
										<?php echo $message; ?>
									</div>
									<?php
								}
							}
						}
					}
					if (count($extensionSteps)) { ?>
						<form style="position:relative;" action="index.php" method="post" name="vmMigrateForm_<?php echo $extension; ?>" id="vmMigrateForm_<?php echo $extension; ?>">
							<input type="hidden" name="task" value="upgrade" />
							<input type="hidden" name="option" value="com_vmmigrate" />
							<input type="hidden" name="view" value="upgrade" />
							<?php echo JHTML::_( 'form.token' ); ?>
							
                            <div>
	                            <button class="btn_stop btn btn-danger"><i class='fa fa-pause'></i>&nbsp;<?php echo JText::_('VMMIGRATE_PAUSE'); ?></button>
    	                        <button class="btn_run btn btn-success" data-extension="<?php echo $extension; ?>" rel="<?php echo $extension; ?>" ><i class='fa fa-play'></i>&nbsp;<?php echo JText::sprintf('VMMIGRATE_MIGRATE_EXTENSION',$versions[$extension]['title']); ?></button>
                            </div>

							<table class="table table-striped">
								<thead class="progressbars">
									<tr>
										<th width="1%">
											<input type="checkbox" id="cbktoggle_<?php echo $extension; ?>" class="checkall-toggle" data-extension="<?php echo $extension; ?>" rel="<?php echo $extension; ?>" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" />
										</th>
										<th class="left" width="150">
											<label for="cbktoggle_<?php echo $extension; ?>"><?php echo JText::_('VMMIGRATE_STEPS'); ?></label>
										</th>
										<th class="left" width="*">
											<?php echo JText::_('VMMIGRATE_PROGRESS'); ?>
										</th>
										<th width="1%"></th>
									</tr>
								</thead>
								<tbody class="progressbars" id="<?php echo $extension; ?>">
									<?php 
									foreach ($extensionSteps as $i => $step) { 
										$tooltiptext = (JText::_($step['name'].'_desc') != ($step['name'].'_desc') && JText::_($step['name'].'_desc')) ? JText::_($step['name'].'_desc') : '';
										if (isset($step['joomfish']) && $step['joomfish']) {
											//$tooltiptext .= JHtml::_('image', 'icon-16-joomfish.png','/administrator/components/com_vmmigrate/assets/images/');
											$tooltiptext .= ' '.JText::_('JOOMFISH_TOOLTIP');
										}
										$tooltip = ($tooltiptext) ? 'class="hasTip" title="'.$tooltiptext.'"' : '';
									?>
									<tr <?php echo $tooltip; ?>>
										<td width="1%">
											<input type="checkbox" id="cbk_<?php echo $extension; ?>_<?php echo $step['name']; ?>" class="stepcbk" name="steps_<?php echo $extension; ?>[]" value="<?php echo $step['name']; ?>" data-extension="<?php echo $extension; ?>" data-step="<?php echo $step['name']; ?>" data-warning="<?php echo isset($step['warning']) ? addslashes($step['warning']) : ''; ?>" <?php echo ($step['default']) ? 'checked="checked"' : '' ?> />
										</td>
										<td class="left <?php echo (isset($step['joomfish']) && $step['joomfish']) ? 'joomfish' : ''; ?>">
											<label for="cbk_<?php echo $extension; ?>_<?php echo $step['name']; ?>"><?php echo JText::_($step['name']); ?>
												<?php //if (isset($step['joomfish']) && $step['joomfish']) {echo JHtml::_('image', 'icon-16-joomfish.png','/administrator/components/com_vmmigrate/assets/images/');} ?>
											</label>
										</td>
										<td class="left">
										  <div class="pg" default="<?php echo ($step['default']) ? '-1' : '0' ?>" id="step_<?php echo $step['name']; ?>"><em class="pg-label"><?php echo ($step['default']) ? JText::_('VMMIGRATE_WAITING') : JText::_('VMMIGRATE_SKIP') ?></em></div>
										</td>
                                        <td><a class="btn btn-mini scrollToLog" id="scrollToLog_<?php echo $extension; ?>_<?php echo $step['name']; ?>" data-target="<?php echo $extension; ?>_<?php echo $step['name']; ?>" href="#" style="display:none;" >log</a></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							<?php
							if (!$this->isPro[$extension]) { ?>
								<div class="block_disabled">
									<br />
									<a class="btn_getaddon" href="https://www.daycounts.com/shop/migrator-addons/" target="new"><?php echo JText::_('VMMIGRATE_GET_PRO_MIGRATOR') ?></a>
								</div>
								
							<?php
							}
							?>
						</form>
						<?php
					} else if ($show_not_installed) { ?>
                        <div style="text-align:center;">
                            <h3><?php echo JText::_('VMMIGRATE_EXTENSION_NOT_FOUND'); ?></h3>
                            <!--p><?php echo JText::_('VMMIGRATE_EXTENSION_NOT_FOUND_DESC'); ?></p-->
                            <p style="text-align:left;"><?php echo JText::_('VMMIGRATE_EXTENSION_NOT_FOUND_DESC2'); ?></p>
                            
                        </div>
					<?php } ?>
                </div>
                <?php
			}
			?>
        </div>
        <div class="span7 pull-right">
        	<fieldset class="adminform">
            	<legend><?php echo JText::_('LOG'); ?></legend>
                <div id="live_log_filters">
                    <input type="button" id="toggleLogInfo" class="btn btn-small btn-success" value="<?php echo JText::_('VMMIGRATE_INFO'); ?>" />
                    <input type="button" id="toggleLogWarnings" class="btn btn-small btn-warning" value="<?php echo JText::_('VMMIGRATE_WARNING'); ?>" />
                    <input type="button" id="toggleLogErrors" class="btn btn-small btn-danger" value="<?php echo JText::_('VMMIGRATE_ERROR'); ?>" />
                    <input type="button" id="toggleLogTranslations" class="btn btn-small btn-info" value="<?php echo JText::_('VMMIGRATE_TRANSLATIONS'); ?>" />
                    <input type="button" id="toggleLogSystemErrors" class="btn btn-small off" value="<?php echo JText::_('VMMIGRATE_SYSTEM_ERRORS'); ?>" />
                    <input type="button" id="toggleLogDebug" class="btn btn-small off" value="<?php echo JText::_('VMMIGRATE_DEBUG'); ?>" />
                </div>
            	<div id="live_log_container">
	            	<div id="live_log"></div>
                </div>
            </fieldset>
        </div>
    </div>
