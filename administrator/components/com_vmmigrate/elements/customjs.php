<?php
/*------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined ('_JEXEC') or die();

class JFormFieldCustomjs extends JFormField {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Customjs';

	public function getInput () {
		
		//JLoader::discover('VMMigrateHelper', JPATH_ADMINISTRATOR.'/components/com_vmmigrate/helpers');
		//VMMigrateHelperVMMigrate::loadCssJs();

		$doc =  JFactory::getDocument();
		$jversion = new JVersion();
		$joomla_version_dest = $jversion->getShortVersion();
		if (version_compare($joomla_version_dest, 3, 'gt')) {
			// load jQuery, if not loaded before
			if (!JFactory::getApplication()->get('jquery')) {
				JFactory::getApplication()->set('jquery', true);
				JHtml::_('jquery.framework');
			}
			$doc->addScriptDeclaration("
			;
			jQuery(document).ready(function($) {
				handleFtp = function() {
					var val = $('#filesystem #jform_ftp_enable input:checked').val();
					if (val == 1) {
						$('.ftp').parents('.control-group').show();
						$('.path').parents('.control-group').hide();
					} else {
						$('.ftp').parents('.control-group').hide();
						$('.path').parents('.control-group').show();
					}
				}
				
				$('#filesystem #jform_ftp_enable input').change(function() {
					handleFtp();
				});
				$('#filesystem #jform_ftp_enable.btn-group-yesno label.btn').click(function() {
					handleFtp();
				});
				
				$('#usepath').click(function(){
					var path = $(this).data('value');
					$('#jform_source_path').val(path);
					return false;
				});
				
				handleFtp();
			});");
		} else {
			// load jQuery, if not loaded before
			if (!JFactory::getApplication()->get('jquery')) {
				JFactory::getApplication()->set('jquery', true);
				$doc->addScript(JURI::root().'administrator/components/com_vmmigrate/assets/js/jquery-1.9.1.js');
			}
			$doc->addScriptDeclaration("
			;
			jQuery(document).ready(function($) {
				handleFtp = function() {
					var val = $('#jform_ftp_enable input:checked').val();
					if (val == 1) {
						$('.ftp').parents('li').show();
						$('.path').parents('li').hide();
					} else {
						$('.ftp').parents('li').hide();
						$('.path').parents('li').show();
					}
				}
				
				$('#jform_ftp_enable input').change(function() {
					handleFtp();
				});
				
				$('#usepath').click(function(){
					var path = $(this).data('value');
					$('#jform_source_path').val(path);
					return false;
				});
				
				handleFtp();
			});");
		}
		
		return '';		
	}

}