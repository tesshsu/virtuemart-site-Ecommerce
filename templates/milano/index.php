<?php
/**
 * @package Helix3 Framework
 * Template Name - Shaper Helix3
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

$doc = JFactory::getDocument();

JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework'); //Force load Bootstrap
unset($doc->_scripts[$this->baseurl . '/media/jui/js/bootstrap.min.js']); // Remove joomla core bootstrap

//Load Helix
$helix3_path = JPATH_PLUGINS.'/system/helix3/core/helix3.php';

if (file_exists($helix3_path)) {
    require_once($helix3_path);
    $this->helix3 = helix3::getInstance();
} else {
    die('Please install and activate helix plugin');
}

//Coming Soon
if($this->helix3->getParam('comingsoon_mode')) header("Location: ".$this->baseUrl."?tmpl=comingsoon");

//Class Classes
$body_classes = '';

if($this->helix3->getParam('sticky_header')) {
    $body_classes .= ' sticky-header';
}

$body_classes .= ($this->helix3->getParam('boxed_layout', 0)) ? ' layout-boxed' : ' layout-fluid';

$app = JFactory::getApplication();
$menu = $app->getMenu()->getActive();

if (is_object($menu)) {
	$pageclass = $menu->params->get('pageclass_sfx');
	$body_classes .= " ".$pageclass;
}

//Body Background Image
if($bg_image = $this->helix3->getParam('body_bg_image')) {

    $body_style  = 'background-image: url(' . JURI::base(true ) . '/' . $bg_image . ');';
    $body_style .= 'background-repeat: '. $this->helix3->getParam('body_bg_repeat') .';';
    $body_style .= 'background-size: '. $this->helix3->getParam('body_bg_size') .';';
    $body_style .= 'background-attachment: '. $this->helix3->getParam('body_bg_attachment') .';';
    $body_style .= 'background-position: '. $this->helix3->getParam('body_bg_position') .';';
    $body_style  = 'body.site {' . $body_style . '}'; 

    $doc->addStyledeclaration( $body_style );
}

//Body Font
$webfonts = array();

if( $this->params->get('enable_body_font') ) {
    $webfonts['body'] = $this->params->get('body_font');
}

//Heading1 Font
if( $this->params->get('enable_h1_font') ) {
    $webfonts['h1'] = $this->params->get('h1_font');
}

//Heading2 Font
if( $this->params->get('enable_h2_font') ) {
    $webfonts['h2'] = $this->params->get('h2_font');
}

//Heading3 Font
if( $this->params->get('enable_h3_font') ) {
    $webfonts['h3'] = $this->params->get('h3_font');
}

//Heading4 Font
if( $this->params->get('enable_h4_font') ) {
    $webfonts['h4'] = $this->params->get('h4_font');
}

//Heading5 Font
if( $this->params->get('enable_h5_font') ) {
    $webfonts['h5'] = $this->params->get('h5_font');
}

//Heading6 Font
if( $this->params->get('enable_h6_font') ) {
    $webfonts['h6'] = $this->params->get('h6_font');
}

//Navigation Font
if( $this->params->get('enable_navigation_font') ) {
    $webfonts['.sp-megamenu-parent'] = $this->params->get('navigation_font');
}

//Custom Font
if( $this->params->get('enable_custom_font') && $this->params->get('custom_font_selectors') ) {
    $webfonts[ $this->params->get('custom_font_selectors') ] = $this->params->get('custom_font');
}

$this->helix3->addGoogleFont($webfonts);

//Custom CSS
if($custom_css = $this->helix3->getParam('custom_css')) {
    $doc->addStyledeclaration( $custom_css );
}

//Custom JS
if($custom_js = $this->helix3->getParam('custom_js')) {
    $doc->addScriptdeclaration( $custom_js );
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    if($favicon = $this->helix3->getParam('favicon')) {
        $doc->addFavicon( JURI::base(true) . '/' .  $favicon);
    } else {
        $doc->addFavicon( $this->helix3->getTemplateUri() . '/images/favicon.ico' );
    }
    ?>

    <jdoc:include type="head" />  
  <script language="javascript" src="https://maps.googleapis.com/maps/api/js?v=3&amp;key=AIzaSyDOCpNu9NaRiiF4Z43y0pDuZuPJosSnevQ"></script>
    <?php

    $this->helix3->addCSS('bootstrap.min.css, assets/owl.carousel.min.css, jquery.bxslider.css,cameleons.css, jquery.mCustomScrollbar.css') // CSS Files
        ->lessInit()->setLessVariables(array(
            'bg_color'=> $this->helix3->PresetParam('_bg'),
            'text_color'=> $this->helix3->PresetParam('_text'),
            'major_color'=> $this->helix3->PresetParam('_major')
            ))
        ->addLess('legacy/bootstrap', 'legacy')
        ->addLess('master', 'template');

        //RTL
        if($this->direction=='rtl') {
            $this->helix3->addCSS('bootstrap-rtl.min.css')
            ->addLess('rtl', 'rtl');
        }
        //Before Head
        if($before_head = $this->helix3->getParam('before_head')) {
            echo $before_head . "\n";
        }
    ?>
    <script type="text/javascript">
        var helix3_template = "<?php echo $this->template; ?>";
        var helix3_template_uri = "<?php echo $this->baseurl . '/templates/'. $this->template; ?>";
    </script>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-7243574-1', 'auto');
      ga('send', 'pageview');
    </script>
    <!-- TrustBox script -->
    <script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
    <!-- End Trustbox script -->
</head>
<body class="<?php echo $this->helix3->bodyClass( $body_classes ); ?>">
    <div class="body-innerwrapper">
        <?php $this->helix3->generatelayout(); ?>

        <div class="offcanvas-menu">
            <a href="#" class="close-offcanvas"><i class="fa fa-remove"></i></a>
            <div class="offcanvas-inner">
                <?php if ($this->helix3->countModules('offcanvas')) { ?>
                    <jdoc:include type="modules" name="offcanvas" style="sp_xhtml" />
                <?php } else { ?>
                    <p class="alert alert-warning"><?php echo JText::_('HELIX_NO_MODULE_OFFCANVAS'); ?></p>
                <?php } ?>
            </div>
        </div>
        <div class="overlay-window"></div>
    </div>
    <?php
    
    if($this->params->get('compress_css')) {
        $this->helix3->compressCSS();
    }

    if($this->params->get('compress_js')) {
        $this->helix3->compressJS( $this->params->get('exclude_js') );
    }

    if($before_body = $this->helix3->getParam('before_body')) {
        echo $before_body . "\n";
    }

    ?>
    <jdoc:include type="modules" name="debug" />
    <?php
    $this->helix3->addJS('bootstrap.min.js, bootstrap-select.min.js, owlcarousel2/owl.carousel.min.js, elevateZoom/jquery.elevateZoom-3.0.8.min.js, main.js, jquery.bootstrap-touchspin.min.js, wow.js, jquery.mCustomScrollbar.min.js, cameleons.js, jquery.fitvids.js, jquery.bxslider.min.js, jquery.columnizer.js, jquery.treeview.js, jquery.treeview-mobile.js') // JS Files
    ?>
</body>
</html>