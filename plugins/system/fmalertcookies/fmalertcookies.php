<?php
/**
 * fmalertcookies.php
 *
 * php version 5
 *
 * @category  Joomla
 * @package   Joomla
 * @author    Folcomedia <contact@folcomedia.fr>
 * @copyright 2014 Folcomedia
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link      http://www.folcomedia.fr
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Plugin Système Folcomedia Alert Cookies
 *
 * @category Joomla
 * @package  Joomla
 * @author   Folcomedia <contact@folcomedia.fr>
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link     http://www.folcomedia.fr
 */
class plgSystemFmAlertCookies extends JPlugin
{

    private function isUserAgent($agente, $tabUserAgent)
    {
        foreach ($tabUserAgent as $UserAgent) {
            if (strpos($agente, $UserAgent) !== false) {
                return true;
            }
        }

        return false;
    }

	function onBeforeCompileHead(){

		$app = JFactory::getApplication();

		if ($app->isSite()) {
			//insert les scripts nécessaire au bon fonctionnement du plugin
			$document = JFactory::getDocument();

			//Gestion des scripts suivant la version de Joomla utilisée.
			if ($position = $this->params->get('ajouter_jquery')) {
	        	if (JVERSION < 3) {
					$document->addScript("https://code.jquery.com/jquery-1.11.1.min.js");
				} else {
					//JHtml::_('jquery.framework');
					JHTML::script('media/jui/js/jquery.min.js');
	        	}
			}

			$document->addStyleSheet(JURI::root().'plugins/system/fmalertcookies/assets/css/bootstrap.min.css');
			if(file_exists(dirname(__FILE__).'/assets/css/custom.css')){
				$document->addStyleSheet(JURI::root().'plugins/system/fmalertcookies/assets/css/custom.css');
			}


			if($this->params->get('type_affichage') == 0) {
				$document->addScript('https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.0.0/jquery.magnific-popup.min.js');
				$document->addStyleSheet(JURI::root().'plugins/system/fmalertcookies/assets/css/magnific-popup.css');
			}

		} else {
			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root().'plugins/system/fmalertcookies/assets/css/fmalertcookie-admin-joomla.css');	
		}
   }

	public function onContentPrepareForm($form, $data)
	{
		// Check we have a form
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}

		// Check we are manipulating a valid form.
		$app = JFactory::getApplication();

		if ($form->getName() != 'com_plugins.plugin'
			|| isset($data->name) && $data->name != 'PLG_SYSTEM_FMALERTCOOKIES') {
			return true;
		}
		$bar = JToolBar::getInstance('toolbar');
		$bar->addButtonPath( JPATH_PLUGINS . '/system/fmalertcookies/classes/');

		$bar->appendButton( 'ExportToolBarButton', 'download', $_SERVER["REQUEST_URI"], JText::_( 'PLG_SYSTEM_FMALERTCOOKIES_UPLOAD'), $task='export');
		$bar->appendButton( 'ImportToolBarButton', 'upload', $_SERVER["REQUEST_URI"], JText::_( 'PLG_SYSTEM_FMALERTCOOKIES_IMPORT'), $task='upload');
		
		$lang = JFactory::getLanguage();		
		$bar->appendButton( 'DonToolBarButton', 'don', str_replace("-", "_", $lang->getTag()));		

		switch ($app->input->getString('fmalertcookiestask')) {
			case 'export':
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('params'));
				$query->from($db->quoteName('#__extensions'));
				$query->where($db->quoteName('element').' = '.$db->quote("fmalertcookies"));
				$db->setQuery($query);
				$params_plugin_cookies = $db->loadResult();

				$filename = "plg_system_fmalertcookies_export";
				$handle = fopen(dirname(__FILE__).'/'.$filename.'.txt',"w");
				fwrite($handle,$params_plugin_cookies);
				fclose($handle);
				header("Content-Description: File Transfer");
				header("Content-type: application/force-download");
				header("Content-Disposition: attachment; filename=".$filename."_".date('Ymd').".txt");
				readfile(JURI::root()."plugins/system/fmalertcookies/".$filename.'.txt');
				unlink(dirname(__FILE__).'/'.$filename.'.txt');
				exit();
			break;

			case "import" :
				//Récupére le contenu du fichier.
				$param = file($_FILES['id_fmalertcookies']['tmp_name']);

				//Vérifie si le fichier est correctement formaté.
				$var_controle = json_decode($param[0]);
				if(!isset($var_controle->name_plugin) || $var_controle->name_plugin != 'fmalertcookies' || $_FILES['id_fmalertcookies']['type'] != 'text/plain'
				|| $_FILES['id_fmalertcookies']['size'] == 0 || $_FILES['id_fmalertcookies']['error'] > 0) {
					$app->redirect(JURI::root(false, '').substr($_SERVER["REQUEST_URI"],1,strrpos($_SERVER["REQUEST_URI"],'&fmalertcookiestask=import')-1),JText::_( 'PLG_SYSTEM_FMALERTCOOKIES_CONF_UPLOAD_KO'),'error');
					break;
				}

				//Insére les paramêtes en base de données.
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__extensions'));
				$query->set($db->quoteName('params').' = '.$db->quote($param[0]));
				$query->where($db->quoteName('element').' = '.$db->quote("fmalertcookies"));
				$db->setQuery($query);
				$db->query();
				$app->redirect(JURI::root(false, '').substr($_SERVER["REQUEST_URI"],1,strrpos($_SERVER["REQUEST_URI"],'&fmalertcookiestask=import')-1),JText::_( 'PLG_SYSTEM_FMALERTCOOKIES_CONF_UPLOAD_OK'));
				exit;
			break;
		}

		// Récupére le langage du contenu du site
		$languages = JLanguageHelper::getLanguages();

		//Teste si la langue par defaut est aussi instanciée dans les langues de contenu
		$params = JComponentHelper::getParams('com_languages');
		$tab_lang = JLanguage::getKnownLanguages();
		$langue_default = $params->get("site");

		$status = 0;
		foreach ($languages as $key => $value) {
			if($value->lang_code == $langue_default) {
				$status = 1;
			}
		}

		if ($status == 0) {
			$lang_name = $tab_lang[$params->get("site")]["name"];
			JError::raiseNotice( 100, JText::sprintf('PLG_SYSTEM_FMALERTCOOKIES_MESSAGE_ALERTE_LANGUE_DEFAUT_NON_PRESENTE',$lang_name, JURI::root()));
		}

		// Injection des nouveaux champs dans le formulaire.
		foreach ($languages as $tag => $language) {
			if(strpos( $language->title_native, '(')) {
				$langue_name = trim(substr($language->title_native, 0 , strpos($language->title_native,'(')-1));
			} else {
				$langue_name = trim($language->title_native);
			}

			$language->lang_code = str_replace('-', '_', $language->lang_code);
			$path_img_lang = "<img src=media/mod_languages/images/.$language->image.'.gif>";
			
				$form->load('
				<form>
					<fields name="params">
						<fieldset name="'.addslashes($language->lang_code).'" label="'.JText::sprintf('PLG_SYSTEM_FMALERTCOOKIES_SAISIE_LANGUE',$language->image).' '.$langue_name.'">
			               <field name="langue_activate_'.$language->lang_code.'" type="radio"
			                    description="PLG_SYSTEM_FMALERTCOOKIES_LANGUE_ACTIVATE_DESC"
			                    default="1" class="btn-group" labelclass="control-label" label="PLG_SYSTEM_FMALERTCOOKIES_LANGUE_ACTIVATE_LABEL">
			                    	<option value="1">JSHOW</option>
			                    	<option value="0">JHIDE</option>
						   </field>
						   <field name="texte_alert_cookies_'.$language->lang_code.'" type="editor" height="150"
			                    description="PLG_SYSTEM_FMALERTCOOKIES_TEXTE_DESC"
			                    label="PLG_SYSTEM_FMALERTCOOKIES_TEXTE_LABEL" filter="safehtml"
			                />
			                <field name="texte_readmore_'.$language->lang_code.'" type="text"
			                    description="PLG_SYSTEM_FMALERTCOOKIES_TEXTE_READMORE_DESC"
			                    label="PLG_SYSTEM_FMALERTCOOKIES_TEXTE_READMORE_LABEL"
			                />
			               <field name="link_readmore_menu_'.$language->lang_code.'" type="menuitem"
			                    description="PLG_SYSTEM_FMALERTCOOKIES_LINK_READMORE_MENU_DESC"
			                    label="PLG_SYSTEM_FMALERTCOOKIES_LINK_READMORE_MENU_LABEL"
			                />
			               <field name="ancre_link_readmore_menu_'.$language->lang_code.'" type="text"
			                    description="PLG_SYSTEM_FMALERTCOOKIES_ANCRE_LINK_READMORE_MENU_DESC"
			                    label="PLG_SYSTEM_FMALERTCOOKIES_ANCRE_LINK_READMORE_MENU_LABEL"
			                />
						    <field name="texte_close_'.$language->lang_code.'" type="text"
			                    description="PLG_SYSTEM_FMALERTCOOKIES_TEXTE_CLOSE_DESC"
			                    label="PLG_SYSTEM_FMALERTCOOKIES_TEXTE_CLOSE_LABEL"
			                />
						</fieldset>
					</fields>
				</form>
			');
		}
		return true;
	}

	function onAfterRender()
    {
    	$app      = JFactory::getApplication();
		$display_offline = ($this->params->get('display_offline') == '')? '0' : $this->params->get('display_offline');

		//Vérifie si votre site est en maintenance
		if ($app->getCfg('offline') == 0 || $display_offline) {
			$mydefaultlanguage = $this->params->get('mylanguage');

			//Récupére la langue active
			$lang = JFactory::getLanguage();
			$langue_code_actif = $lang->get('tag');


			$langue_code_actif = str_replace("-","_", $langue_code_actif);
			$mydefaultlanguage = str_replace("-","_", $mydefaultlanguage);


			$link_readmore_menu = $this->params->get('link_readmore_menu_'.$langue_code_actif);
			$lang_activate = $this->params->get('langue_activate_'.$langue_code_actif);
			if($link_readmore_menu == '') $link_readmore_menu = $this->params->get('link_readmore_menu_'.$mydefaultlanguage);

			$affichage_msg_page_cookie = $this->params->get('affichage_msg_page_cookie');
			$user_agent = $this->params->get('user_agent','Facebot,facebookexternalhit,Teoma,alexa,froogle,Gigabot,inktomi,looksmart,URL_Spider_SQL,Firefly,NationalDirectory,AskJeeves,TECNOSEEK,InfoSeek,WebFindBot,girafabot,crawler,www.galaxy.com,Googlebot,Scooter,Slurp,bing,msnbot,appie,FAST,WebBug,Spade,ZyBorg,rabaz,Baiduspider,Feedfetcher-Google,TechnoratiSnoop,Rankivabot,Mediapartners-Google,Sogouwebspider,WebAltaCrawler,TweetmemeBot,Butterfly,Twitturls,Me.dium,Twiceler');
			$tabUser_agent = explode(',', strtolower($user_agent));

			//Test si l'on se trouve bien sur le site et non en administration
	        if (!$app->isSite() || $this->isUserAgent(strtolower($_SERVER['HTTP_USER_AGENT']),$tabUser_agent)  || $lang_activate == 0 || (strtolower($_SERVER['REQUEST_URI']) == strtolower(JRoute::_("index.php?Itemid=".$link_readmore_menu)) && !$affichage_msg_page_cookie)) {
	            return;
	        }

			// Récupération des paramêtres
			$deleteCookie = $this->params->get('deleteCookie', '0');
	        $position = $this->params->get('position');
			$type_affichage = $this->params->get('type_affichage');
	        $taille_cadre = $this->params->get('taille_cadre');
			$duree_cookie = ($this->params->get('duree_cookie') == '')? '30' : $this->params->get('duree_cookie');

			if(!strpos($taille_cadre,"%")){
				$taille_cadre = str_replace('px', '', $taille_cadre).'px';
			}
			$texte = $this->params->get('texte_alert_cookies_'.$langue_code_actif);
			if($texte == '') $texte = $this->params->get('texte_alert_cookies_'.$mydefaultlanguage);
			$texte_couleur = $this->params->get('couleur_texte');
			$fond_couleur = $this->params->get('couleur_fond');
			$position_fixe = $this->params->get('position_fixe');
			$marge_ext = $this->params->get('marge_ext');
			$marge_int = $this->params->get('marge_int');
			$position_contenu = $this->params->get('position_contenu');
			$opacity = $this->params->get('num_opacity');
			if ($opacity > 0) {
				$opacity = $opacity / 100;
			}

			$position_fixe_cookie = "";
			if($position_fixe && $type_affichage == 1){
				$position_fixe_cookie.= "position:fixed;z-index:10000;left: 0;right: 0;";
				if($position == 1) {
					$position_fixe_cookie.= "bottom: 0;";
				}
			}

			//Bordures
			$type_bordure = $this->params->get('type_bordure');
			$taille_bordure = $this->params->get('taille_bordure');
			$couleur_bordure = $this->params->get('couleur_bordure');

			//Params btn
			$first_btn = $this->params->get('first_btn');
			$position_btn = $this->params->get('position_btn');

			//Params btn more
			$texte_readmore = $this->params->get('texte_readmore_'.$langue_code_actif);
			if($texte_readmore == '') $texte_readmore = $this->params->get('texte_readmore_'.$mydefaultlanguage);
			$btn_more = $this->params->get('btn_more');
			$position_btn_more = $this->params->get('position_btn_more');
			$taille_btn_more = $this->params->get('taille_btn_more');
			$couleur_btn_more = $this->params->get('couleur_btn_more');
			$couleur_texte_btn_more = $this->params->get('couleur_texte_btn_more');
			$couleur_btn_more_custom = $this->params->get('couleur_btn_more_custom');
			$couleur_btn_more_style = "";
			if($couleur_btn_more == "btn-custom") {
				$couleur_btn_more_style = 'background:'.$couleur_btn_more_custom.';';
				$couleur_btn_more = "";
			}


			$ancre_link_readmore_menu= $this->params->get('ancre_link_readmore_menu_'.$langue_code_actif);
			if($ancre_link_readmore_menu == '') $ancre_link_readmore_menu = $this->params->get('ancre_link_readmore_menu_'.$mydefaultlanguage);
			if(!$ancre_link_readmore_menu == '') {
				$ancre_link_readmore_menu = '#'.str_replace('#', '', $ancre_link_readmore_menu);
			}
			
			

			//Params btn close
			$texte_close = $this->params->get('texte_close_'.$langue_code_actif);
			if($texte_close == '') $texte_close = $this->params->get('texte_close_'.$mydefaultlanguage);
			$btn_close = $this->params->get('btn_close');
			$position_btn_close = $this->params->get('position_btn_close');
			$taille_btn_close = $this->params->get('taille_btn_close');
			$couleur_btn_close = $this->params->get('couleur_btn_close');
			$couleur_texte_btn_close = $this->params->get('couleur_texte_btn_close');
			$couleur_btn_close_custom = $this->params->get('couleur_btn_close_custom');
			$couleur_btn_close_style = "";
			if($couleur_btn_close == "btn-custom") {
				$couleur_btn_close_style = 'background:'.$couleur_btn_close_custom.';';
				$couleur_btn_close = "";
			}
			$scriptDeleteCookie = "";
			if($deleteCookie) {
				$scriptDeleteCookie ='if(!acceptCookie) { ;';
				$scriptDeleteCookie .='for(var i=0; i<ca.length; i++) {';
				$scriptDeleteCookie .='var c1 = ca[i];';
				$scriptDeleteCookie .="document.cookie= c1+'; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';";
				$scriptDeleteCookie .='}}';		
			}
			
			$application = JFactory::getApplication();
			$menu = $application->getMenu();
			$item = $menu->getItem($link_readmore_menu);

			switch ($type_bordure) {
				//Bordure arrondie
				case '0':
					$css_bordure = "border:".$taille_bordure."px solid ".$couleur_bordure."; border-radius:5px";
					break;
				//Sans bordure
				case '2':
					$css_bordure = "";
					break;
				//Autres
				default:
					$css_bordure = "border: ".$taille_bordure."px solid ".$couleur_bordure.";";
					break;
			}

			//Teste si les deux ou un seul boutons est affichés
			$span = $meme_ligne_btn_more = $meme_ligne_btn_close = $meme_ligne = "";

			//Position des bouttons à la ligne
			if($position_btn == 0){
				$span= "col-md-6";
				if (($btn_more && !$btn_close) || (!$btn_more && $btn_close)) {
					$span= "col-md-12";
				}
			}
			//Position des bouttons sur la même ligne que le texte
			elseif($position_btn == 1) {
				$meme_ligne = "pull-left";
			}

			$function_close = "";
			//Affichage encadré
			if ($type_affichage == 1) {	$function_close = 'onclick="CloseCadreAlertCookie();"'; }

			$text_btn_more = '<div class="'. $meme_ligne .' '.$span.' col-sm-6 btn_readmore" style="margin:0;text-align:'.$position_btn_more.'"><a style="'.$couleur_btn_more_style.'color:'.$couleur_texte_btn_more.'" class="btn '.$couleur_btn_more.' '.$taille_btn_more.' read_more" href="'.JRoute::_("index.php?Itemid=".$link_readmore_menu).$ancre_link_readmore_menu.'">'.$texte_readmore.'</a></div>';
			$text_btn_more_poup = '<div class="'.$span.' col-sm-6 btn_readmore" style="margin:5px 0;text-align:'.$position_btn_more.'"><a style="'.$couleur_btn_more_style.'color:'.$couleur_texte_btn_more.'" class="btn '.$couleur_btn_more.' '.$taille_btn_more.' read_more" onclick="jQuery.magnificPopup.close();" href="'.JRoute::_("index.php?Itemid=".$link_readmore_menu).$ancre_link_readmore_menu.'">'.$texte_readmore.'</a></div>';
			$text_btn_close = '<div class="'.$meme_ligne.' '.$span.' col-sm-6 btn_close" style="margin:0;text-align:'.$position_btn_close.'"><button '.$function_close.' style="'.$couleur_btn_close_style.'color:'.$couleur_texte_btn_close.'" class="btn '.$couleur_btn_close.' '.$taille_btn_close.' popup-modal-dismiss">'.$texte_close.'</button></div>';

			$text_out ='<!--googleoff: all--><div class="cadre_alert_cookies" id="cadre_alert_cookies" style="opacity:'.$opacity.';text-align:'.$position_contenu.';'.$position_fixe_cookie.' margin:'.$marge_ext.'px;">';
			$text_out .='<div class="cadre_inner_alert_cookies" style="display: inline-block;width: 100%;margin:auto;max-width:'.$taille_cadre.';background-color: '.$fond_couleur.';'.$css_bordure.'">';
			$text_out .='<div class="cadre_inner_texte_alert_cookies" style="display: inline-block;padding:'.$marge_int.'px;color: '.$texte_couleur.'"><div class="cadre_texte '.$meme_ligne.'">'.$texte.'</div>';
			$text_out .='<div class="cadre_bouton '.$meme_ligne.'">';

			//Affichage encadré
			if ($type_affichage == 1) {	$text_out_btn_more = $text_btn_more; }
			//Affichage popup
			elseif($type_affichage == 0) { $text_out_btn_more = $text_btn_more_poup; }

			//Boutton fermer en premier
			if($first_btn == 0) {
				if($btn_close) { $text_out .= $text_btn_close;	}
				if($btn_more) { $text_out .= $text_out_btn_more; }
			}
			//Boutton en savoir plus en premier
			elseif($first_btn == 1) {
				if($btn_more) { $text_out .= $text_out_btn_more; }
				if($btn_close) { $text_out .= $text_btn_close;	}
			}

			$text_out .='</div></div></div></div><!--googleon: all-->';

			//Récupére le code html de la page affichée
			$buffer = JResponse::getBody();
			
			//Affichage encadré
			if ($type_affichage == 1) {

				$script = '<script type="text/javascript">/*<![CDATA[*/';
				$script .='var name = "fmalertcookies" + "=";';
				$script .='var ca = document.cookie.split(";");';
				$script .='var acceptCookie = false;';
				$script .='for(var i=0; i<ca.length; i++) {';
				$script .='var c = ca[i];';
				$script .='while (c.charAt(0)==" ") c = c.substring(1);';
				$script .='if (c.indexOf(name) == 0){ acceptCookie = true; document.getElementById("cadre_alert_cookies").style.display="none";}';
				$script .='}';
				
				$script .= $scriptDeleteCookie;
				
				$script .='var d = new Date();';
				$script .='d.setTime(d.getTime() + ('.$duree_cookie.'*(24*60*60*1000)));';
				$script .='var expires_cookie = "expires="+d.toUTCString();';
				$script .="function CloseCadreAlertCookie(){document.getElementById('cadre_alert_cookies').style.display='none'; document.cookie='fmalertcookies=true; '+expires_cookie+'; path=/';}";
				$script .="";
				$script .="/*]]>*/</script>";

				// Position Haut
				if ($position == 0) {
	                $buffer = preg_replace('/<body(.*?)>/i', '<body$1>'.$text_out.$script, $buffer, 1);
				}
				// Position Bas
				elseif ($position == 1) {

	                $parts = explode('</body>', $buffer);
					if (sizeof($parts)<2) {
						return; // il n'y a pas </body> dans la page
					}

					$parts[sizeof($parts)-2] .= $text_out.$script;
					$buffer = implode('</body>', $parts);
				}
			}

			//Affichage Popup
			elseif($type_affichage == 0) {

				$params = "";
				if ($btn_close) {
					$params = "modal: true";
				} else {
					//Insert un cookie pour éviter de réafficher le message.
					setcookie('fmalertcookies',true,time()+60*60*24*$duree_cookie,'/');
				}

				$popup = '<script type="text/javascript">';
				$popup .='var d = new Date();';
				$popup .='var acceptCookie = false;';
				$popup .='d.setTime(d.getTime() + ('.$duree_cookie.'*(24*60*60*1000)));';
				$popup .='var expires_cookie = "expires="+d.toUTCString();';
				$popup .="jQuery.magnificPopup.open({items: {src: '". str_replace("\r\n", "",addslashes($text_out))."',type: 'inline'},$params});";
				$popup .="jQuery(document).on('click', '.popup-modal-dismiss', function (e) {e.preventDefault();jQuery.magnificPopup.close(); document.cookie='fmalertcookies=true; '+expires_cookie+'; path=/'});";
				$popup .='var name = "fmalertcookies" + "=";';
				$popup .='var ca = document.cookie.split(";");';
				
				$popup .= $scriptDeleteCookie;				
				
				$popup .='for(i = 0; i < ca.length; i++) {';
				$popup .='var c = ca[i];';
				$popup .='while (c.charAt(0)==" ") c = c.substring(1);';
				$popup .='if (c.indexOf(name) == 0){ acceptCooke = true; jQuery.magnificPopup.close();}';
				$popup .='}';			
				$popup .="</script>";
				$buffer = str_replace('</body>',$popup.'</body>', $buffer);			
			}

			JResponse::setBody($buffer);
		}
    }
}

?>