<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_falang
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.language.helper');
jimport('joomla.utilities.utility');
jimport('joomla.html.parameter');
jimport('joomla.filesystem.file');

JLoader::register('MenusHelper', JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

abstract class modFaLangHelper
{
	public static function getList(&$params)
	{
		$lang   = JFactory::getLanguage();
		$languages	= JLanguageHelper::getLanguages();
		$app	= JFactory::getApplication();

        //use to remove default language code in url
        $lang_codes 	= JLanguageHelper::getLanguages('lang_code');
        $default_lang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
        $default_sef 	= $lang_codes[$default_lang]->sef;

        $menu = $app->getMenu();
        $active = $menu->getActive();
        $uri = JURI::getInstance();

        //On edit mode the flag/name must be disabled

        // Get menu home items
        $homes = array();

        foreach ($menu->getMenu() as $item)
        {
            if ($item->home)
            {
                $homes[$item->language] = $item;
            }
        }


        if (FALANG_J30) {
            //since 3.2
            if (version_compare(JVERSION, '3.2', 'ge')) {
                $assoc =  JLanguageAssociations::isEnabled();
            } else {
                $assoc = isset($app->item_associations) ? (boolean) $app->item_associations : false;
            }
        } else {
            $assoc = (boolean) $app->get('menu_associations', true);
        }


		if ($assoc) {
			if ($active) {
				$associations = MenusHelper::getAssociations($active->id);
			}
            //v2.2.0 support component assoication
            // Load component associations
            $class = str_replace('com_', '', $app->input->get('option')) . 'HelperAssociation';
            JLoader::register($class, JPATH_COMPONENT_SITE . '/helpers/association.php');

            if (class_exists($class) && is_callable(array($class, 'getAssociations')))
            {
                //don't load association for eshop
                if ( $class != 'eshopHelperAssociation'){
                    $cassociations = call_user_func(array($class, 'getAssociations'));
                }
            }
		}
   		foreach($languages as $i => &$language) {
			// Do not display language without frontend UI
			if (!JLanguage::exists($language->lang_code)) {
				unset($languages[$i]);
			}
            if (FALANG_J30) {
                $language_filter = JLanguageMultilang::isEnabled();
            } else {
                $language_filter = $app->getLanguageFilter();
            }

            //set language active before language filter use for sh404 notice
            $language->active =  $language->lang_code == $lang->getTag();

            //since v1.4 change in 1.5 , ex rsform preview don't have active
            //this method don't set display for component association set after
            if (isset($active)){
                $language->display = ($active->language == '*' || $language->active)?true:false;
            } else {
                $language->display = true;
            }

            if (modFaLangHelper::isEditMode()){
                $language->display = false;
            }

            if ($language_filter) {
                //use component association
                if (isset($cassociations[$language->lang_code])) {
                    $language->link = JRoute::_($cassociations[$language->lang_code] . '&lang=' . $language->sef);
                    //fix mijoshop link
					if (isset($_GET['mijoshop_store_id'])) {
						$_link = explode('?', $language->link);

						$language->link = $_link[0];
					}
                    //if association existe for this language display flag.
                    $language->display = true;
                }elseif (isset($associations[$language->lang_code]) && $menu->getItem($associations[$language->lang_code])) {
                    //use menu association.
                    $language->display = true;
                    $itemid = $associations[$language->lang_code];

                    //use to have component parameters in case of menu association
                    $router = JApplication::getRouter();
                    $tmpuri = clone($uri);
                    $router->parse($tmpuri);
                    $vars = $router->getVars();
                    $vars['lang'] = $language->sef;

                    if ($_GET['option'] != 'com_mijoshop') {
                        $vars['Itemid'] = $itemid;
                    }
                    $url = 'index.php?'.JURI::buildQuery($vars);

                    if ($app->getCfg('sef')=='1') {
                        $language->link = JRoute::_($url);
                    }
                    else {
                        $language->link = $url;
                    }
                }
                else {
                    //sef case
                    if ($app->getCfg('sef')=='1') {

                        //sefToolsEnabled
                        if (modFaLangHelper::mijosefToolEnabled()) {
                            $itemid = isset($homes[$language->lang_code]) ? $homes[$language->lang_code]->id : $homes['*']->id;
                            if ($_GET['option'] != 'com_mijoshop') {
                                $language->link = JRoute::_('index.php?lang='.$language->sef.'&Itemid='.$itemid);
                            } else {
                                $language->link = JRoute::_('index.php?lang='.$language->sef);
                            }
                            continue;
                        }

                        if (modFaLangHelper::sh404Enabled()){
                            $router = JSite::getRouter();
                            $urlvars = $router->getVars();
                            $urlvars['lang'] = $language->sef;
                            $url = 'index.php?'.JURI::buildQuery($urlvars);
                            getSefUrlFromDatabase($url, $sef);
                            $language->link = $url;
                            continue;
                        }

                         //$uri->setVar('lang',$language->sef);
                         $router = JApplication::getRouter();
                         $tmpuri = clone($uri);

                         $router->parse($tmpuri);

                         $vars = $router->getVars();
                         //workaround to fix index language
                         $vars['lang'] = $language->sef;

                        //since 2.2.1
                        //case of article category view
                        //set the language used to reload category with the right language
                        $jfm = FalangManager::getInstance();
                        if (!empty($vars['view']) && $vars['view'] == 'category'  && !empty($vars['option']) && $vars['option'] == 'com_content') {
                            if (($language->lang_code != $default_lang) || ($lang->getTag() != $default_lang) ){
                                JCategories::$instances = array();
                                $jfm->setLanguageForUrlTranslation($language->lang_code);
                            }
                        }
                        //end since 2.2.1

                        //case of category article
                        //set the language used to reload category with the right language
                        if (!empty($vars['view']) && $vars['view'] == 'article'  && !empty($vars['option']) && $vars['option'] == 'com_content') {

                            //since 2.2.1
                            if (($language->lang_code != $default_lang) || ($lang->getTag() != $default_lang) ){
                                JCategories::$instances = array();
                                $jfm->setLanguageForUrlTranslation($language->lang_code);
                            }
                            //end 2.2.1

                            if (FALANG_J30){
                                JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
                                $model = JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request'=>true));
                                $appParams = JFactory::getApplication()->getParams();
                            } else {
                                JModel::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
                                $model =& JModel::getInstance('Article', 'ContentModel', array('ignore_request'=>true));
                                $appParams = JFactory::getApplication()->getParams();
                            }


                            $model->setState('params', $appParams);

                            //in sef some link have this url
                            //index.php/component/content/article?id=39
                            //id is not in vars but in $tmpuri
                            if (empty($vars['id'])) {
                                $tmpid = $tmpuri->getVar('id');
                                if (!empty($tmpid)) {
                                    $vars['id'] = $tmpuri->getVar('id');
                                } else {
                                    continue;
                                }
                            }

                            $item = $model->getItem($vars['id']);

                            //get alias of content item without the id , so i don't have the translation
                            $db = JFactory::getDbo();
                            $query = $db->getQuery(true);
                            $query->select('alias')->from('#__content')->where('id = ' . (int) $item->id);
                            $db->setQuery($query);
                            $alias = $db->loadResult();

                            $vars['id'] = $item->id.':'.$alias;
                            $vars['catid'] =$item->catid.':'.$item->category_alias;
                        }

                        //new version 1.5
                        //case for k2 item alias write twice
                        //since k2 v 1.6.9 $vars['task'] don't exist.
                        //v2.2.3 fix for archive notice
                        if (isset($vars['option']) && $vars['option'] == 'com_k2'){
                            if (isset($vars['task']) && isset($vars['id']) && ($vars['task'] == $vars['id'])){
                                unset($vars['id']);
                            }
                        }

                        //new 2.5.0
                        //fix for virtuemart url with showall, limitstart, limit on productsdetail page
                        if (isset($vars['option']) && $vars['option'] == 'com_virtuemart'){
                            if (isset($vars['view']) && $vars['view'] == 'productdetails'){
                                unset($vars['showall']);
                                unset($vars['limitstart']);
                                unset($vars['limit']);
                            }
                        }


                        $url = 'index.php?'.JURI::buildQuery($vars);
                        $language->link = JRoute::_($url);


                        //since 2.2.1
                        //on restaure les categories pour le cas des liste de categories
                        if (!empty($vars['view']) && $vars['view'] == 'category'  && !empty($vars['option']) && $vars['option'] == 'com_content') {
                            if (($language->lang_code != $default_lang) || ($lang->getTag() != $default_lang)) {
                                JCategories::$instances = array();
                                $jfm->setLanguageForUrlTranslation(null);
                            }
                        }

                        if (!empty($vars['view']) && $vars['view'] == 'article'  && !empty($vars['option']) && $vars['option'] == 'com_content') {

                            if (($language->lang_code != $default_lang) || ($lang->getTag() != $default_lang)) {
                                JCategories::$instances = array();
                                $jfm->setLanguageForUrlTranslation(null);
                            }
                        }
                        //end 2.2.1


                        //TODO check performance 3 queries by languages -1
                        /**
                         * Replace the slug from the language switch with correctly translated slug.
                         * $language->lang_code language de la boucle (icone lien)
                         * $lang->getTag() => language en cours sur le site
                         * $default_lang langue par default du site
                         */
                        if($lang->getTag() != $language->lang_code && !empty($vars['Itemid']))
                        {
                            $fManager = FalangManager::getInstance();
                            $id_lang = $fManager->getLanguageID($language->lang_code);
                            $db = JFactory::getDbo();
                            // get translated path if exist
                            $query = $db->getQuery(true);
                            $query->select('fc.value')
                                ->from('#__falang_content fc')
                                ->where('fc.reference_id = '.(int)$vars['Itemid'])
                                ->where('fc.language_id = '.(int) $id_lang )
                                ->where('fc.reference_field = \'path\'')
                                ->where('fc.published = 1')
                                ->where('fc.reference_table = \'menu\'');
                            $db->setQuery($query);
                            $translatedPath = $db->loadResult();

                            // $translatedPath not exist if not translated or site default language
                            // don't pass id to the query , so no translation given by falang
                            $query = $db->getQuery(true);
                            $query->select('m.path')
                                ->from('#__menu m')
                                ->where('m.id = '.(int)$vars['Itemid']);
                            $db->setQuery($query);
                            $originalPath = $db->loadResult();

                            $pathInUse = null;
                            //si on est sur une page traduite on doit récupérer la traduction du path en cours
                            if ($default_lang != $lang->getTag() ) {
                                $id_lang = $fManager->getLanguageID($lang->getTag());
                                // get translated path if exist
                                $query = $db->getQuery(true);
                                $query->select('fc.value')
                                    ->from('#__falang_content fc')
                                    ->where('fc.reference_id = '.(int)$vars['Itemid'])
                                    ->where('fc.language_id = '.(int) $id_lang )
                                    ->where('fc.reference_field = \'path\'')
                                    ->where('fc.published = 1')
                                    ->where('fc.reference_table = \'menu\'');
                                $db->setQuery($query);
                                $pathInUse = $db->loadResult();

                            }

                            if (!isset($translatedPath)) {
                                $translatedPath = $originalPath;
                            }

                            // not exist if not translated or site default language
                            if (!isset($pathInUse)) {
                                $pathInUse = $originalPath ;
                            }

                            //make replacement in the url

                            //si language de boucle et language site
                            if($language->lang_code == $default_lang) {
                                if (isset($pathInUse) && isset($originalPath)){
                                    $language->link = str_replace($pathInUse, $originalPath, $language->link);
                                }
                            } else {
                                if (isset($pathInUse) && isset($translatedPath)){
                                    $language->link = str_replace($pathInUse, $translatedPath, $language->link);
                                }
                            }

                        }
                    }
                    //default case
             else {
                     if (version_compare(JVERSION, '3.4.3', 'ge')) {
                         JUri::reset();
                         $uri = JUri::getInstance();
                         $uri->setVar('lang',$language->sef);
                         $language->link = JUri::getInstance()->toString(array('scheme', 'host', 'port', 'path', 'query'));
                         //fix problem on mod_login (same position before falang module
                         JUri::reset();
                     } else {
                         //we can't remove default language in the link
                         $uri->setVar('lang',$language->sef);
                         $language->link = 'index.php?'.$uri->getQuery();
                     }
                 }
                }
            }
            else {
                $language->link = 'index.php';
            }

		}
		return $languages;
	}

    public static function isFalangDriverActive() {
        $db = JFactory::getDBO();
        if (!is_a($db,"JFalangDatabase")){
           return false;
        }
           return true;
    }

    public static function isEditMode(){
        $layout = JFactory::getApplication()->input->get('layout');
        if ($layout == 'edit'){
            return true;
        } else {
            return false;
        }
    }

    public static function mijosefToolEnabled() {
        //check mijosef
        $mijoseffilename = JPATH_ADMINISTRATOR . '/components/com_mijosef/library/mijosef.php';
        if (JFile::exists($mijoseffilename)) {
            require_once($mijoseffilename);
            $mijoconfig = Mijosef::getConfig();

            if ($mijoconfig->mode == 1){
                return true;
            }
        }
        return false;
    }

    public static function sh404Enabled() {
        $sh404filename = JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php';
        if (JFile::exists($sh404filename)) {
            require_once($sh404filename);
            // get our configuration
            $sefConfig = &Sh404sefFactory::getConfig();

            if ($sefConfig->Enabled)
            {
                return true;
            }
        }
    }

}
