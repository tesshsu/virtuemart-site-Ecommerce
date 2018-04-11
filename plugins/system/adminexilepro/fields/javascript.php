<?php
/**
 * @copyright	Copyright (C) 2010 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.version');


require_once(JPATH_ROOT.'/plugins/system/adminexilepro/classes/aehelper.class.php');
class JFormFieldJavascript extends JFormField
{
	protected $type = 'Javascript';
        protected function getLabel(){
            return '';
        }
	protected function getInput()
	{
                $options = array();
                $joomla = array('JACTION_DELETE');
                $messages = array('INVALIDASCII','INVALIDCHAR','NOTNUMERIC');
                $chars = array('DOLLAR','AMPERSAND','PLUS','COMMA','PERIOD','FORWARDSLASH','COLON','SEMICOLON','EQUALS','QUESTION','AT','SPACE','QUOTE','LESSTHAN','GREATERTHAN','POUND','PERCENT','LEFTCURLY','RIGHTCURLY','PIPE','BACKSLASH','CARAT','TILDE','LEFTBRACKET','RIGHTBRACKET','GRAVE');
                foreach($messages as $string) JText::script('PLG_SYS_ADMINEXILEPRO_MESSAGE_'.$string);
                foreach($chars as $string) JText::script('PLG_SYS_ADMINEXILEPRO_CHAR_'.$string);
		/* pro feature start */
                $table = array('ADDRESS','FAILS','PENALTYSTART','PENALTYEND','DELETE','BLACKLIST','CONFIRM_BLACKLIST');
		$plugin = JPluginHelper::getPlugin('system', 'adminexilepro');
		$params = new JRegistry($plugin->params);
		$options['enablebruteforce'] = $params->get('enablebruteforce',0);
		$options['enableip'] = $params->get('enableip',0);
		$options['bfthreshold'] = $params->get('bfthreshold',5);
		$options['bfpenalty'] = $params->get('bfpenalty',5);
		$options['bfmultiplier'] = $params->get('bfmultiplier',1);
                foreach($table as $string) JText::script('PLG_SYS_ADMINEXILEPRO_BFTABLE_'.$string);
		/* pro feature end */
                foreach($joomla as $string) JText::script($string);
		$options['uri']=JURI::root(false);
                JHtml::_('jquery.framework',true);
		$doc = JFactory::getDocument();
                $doc->addScriptDeclaration("\n".'window.plg_sys_adminexilepro_config = '.json_encode($options).';'."\n");
                $doc->addScript(JURI::root(true).'/media/plg_system_adminexilepro/js/admin.js');
		return;
	}
}
