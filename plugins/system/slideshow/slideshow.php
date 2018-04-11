 <?php 
/**
 * @package Slideshow
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.event.plugin');

class plgSystemSlideshow extends JPlugin {
    
      function __construct( &$subject ) {
        parent::__construct( $subject );
        $this->_plugin = JPluginHelper::getPlugin( 'system', 'slideshow' );
        $this->_params = json_decode( $this->_plugin->params );
        JPlugin::loadLanguage('plg_system_slideshow', JPATH_ADMINISTRATOR);
    }
    function cis_make_slideshow($m) {
       $id_slideshow = (int) $m[2];
    	require_once JPATH_SITE.'/components/com_slideshow/helpers/helper.php';
    	$cis_class = new SlideshowsHelper;
    	$cis_class->slideshow_id = $id_slideshow;
    	$cis_class->type = 'plugin';
    	$cis_class->class_suffix = 'cis_plg';
    	$cis_class->module_id = $this->plg_order;
    	$this->plg_order ++;
    	return  $cis_class->render_html();
    }
    function render_styles_scripts() {
        $document = JFactory::getDocument();
    	$content = JResponse::getBody();
    	$scripts = '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js" type="text/javascript"></script>'."\n";
    	$content = str_replace('</head>', $scripts . '</head>', $content);
    	return $content;
    }
    function onAfterRender() {
      $mainframe = JFactory::getApplication();
      if($mainframe->isAdmin())
        return;

      $plugin = JPluginHelper::getPlugin('system', 'slideshow');
      $pluginParams = json_decode( $plugin->params );

      $content = JResponse::getBody();
      
      //add scripts
      if(preg_match('/(\[huge_it_slideshow_id="([0-9]+)"\])/s',$content))
        $content = $this->render_styles_scripts();
      else
      	return;
      $this->plg_order = 100000;
      $c = preg_replace_callback('/(\[huge_it_slideshow_id="([0-9]+)"\])/s',array($this, 'cis_make_slideshow'),$content);
      JResponse::setBody($c);
    }
   

}
function huge_it_slideshow_id($id_cat) {  
     include 'components/com_slideshow/views/slideshow/tmpl/shortcode.php';
        shortcode($id_cat);
}