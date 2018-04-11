<?php
//no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Path assignments

$ibase = JURI::base();
if(substr($ibase, -1)=="/") { $ibase = substr($ibase, 0, -1); }
$modURL 	= JURI::base().'modules/mod_je_thumbslideshow';
// get parameters from the module's configuration
$jQuery = $params->get("jQuery");
$ImagesPath = 'images/'.$params->get('imageFolder','images');


$slideAnim = $params->get("slideAnim",'slide');
$slideInt = $params->get("slideInt",'3');
$slidePlay = $params->get("slidePlay",'true');
$navBg = $params->get("navBg",'#E0E0E0');
$navBgH = $params->get("navBgH",'#c4c4c4');
$navBgA = $params->get("navBgA",'#efc75e');
$imgPath = $params->get("imgPath");
$imgWidth = $params->get('imgWidth','1000');
$imgHeight = $params->get('imgHeight','400');
$thumbWidth = $params->get('thumbWidth','250');
$thumbHeight = $params->get('thumbHeight','100');
$thumbQuality = $params->get('thumbQuality','100');

// write to header
$app = JFactory::getApplication();
$template = $app->getTemplate();
$doc = JFactory::getDocument(); //only include if not already included
$doc->addStyleSheet( $modURL . '/css/style.css');
$style = '
#je_thumbslide'.$module->id.' .ei-slider-thumbs li.ei-slider-element{background: '.$navBgA.';}
#je_thumbslide'.$module->id.' .ei-slider-thumbs li a{background: '.$navBg.';}
#je_thumbslide'.$module->id.' .ei-slider-thumbs li a:hover{background: '.$navBgH.';}

'; 
$doc->addStyleDeclaration( $style );
if ($params->get('jQuery')) {$doc->addScript ('http://code.jquery.com/jquery-latest.pack.js');}
$doc->addScript($modURL . '/js/jquery.eislideshow.js');
$doc->addScript($modURL . '/js/jquery.easing.1.3.js');
$doc = JFactory::getDocument();
$js = "
            jQuery(function() {
                jQuery('#je_thumbslide".$module->id."').eislideshow({
					animation			: '".$slideAnim."',
					autoplay			: ".$slidePlay.",
					slideshow_interval	: ".$slideInt."000,
					speed			: 800,
					thumbMaxWidth		: ".$thumbWidth."
                });
            });

";
$doc->addScriptDeclaration($js);

?>

<?php $thumbs = '&a=t&w='.$thumbWidth.'&h='.$thumbHeight.'&q='.$thumbQuality; ?>    
<?php
		if (file_exists($ImagesPath) && is_readable($ImagesPath)) {$folder = opendir($ImagesPath);} 
		else {	echo '<div class="slidemessage">Please check the module settings and make sure you have entered a valid image folder path!</div>';return;}
		$allowed_types = array("jpg","JPG","jpeg","JPEG","gif","GIF","png","PNG","bmp","BMP");
		$index = array();while ($file = readdir ($folder)) {if(in_array(substr(strtolower($file), strrpos($file,".") + 1),$allowed_types)) {array_push($index,$file); }}
		closedir($folder);
?>              
                <div id="je_thumbslide<?php echo $module->id;?>" class="ei-slider">
                    <ul class="ei-slider-large">
                    
					<?php for ($i=0; $i<count($index); $i++){$num = JURI::base().$ImagesPath."/".$index[$i];	?>
                    	<li><?php echo '<img src="'.$num.'" width="'.$imgWidth.'"  height="'.$imgHeight.'" />';  ?></li>
                    <?php } ?>
                    </ul><!-- ei-slider-large -->
                    <ul class="ei-slider-thumbs">
                        <li class="ei-slider-element">Current</li>
                        
					<?php for ($i=0; $i<count($index); $i++){$num = JURI::base().$ImagesPath."/".$index[$i];	?>
                    	<li><a href="#"></a><img src="<?php echo $modURL; ?>/thumb.php?src=<?php echo $num; ?><?php echo $thumbs; ?>" /></li>
                    <?php } ?>

                    </ul><!-- ei-slider-thumbs -->
                </div><!-- ei-slider -->
<?php $jeno = substr(hexdec(md5($module->id)),0,1);
$jeanch = array("thumbnail joomla slideshow","responsive joomla slideshow","free joomla slider","best joomla slideshow", "free slideshow module joomla","joomla slider not working","free download joomla slider","thumbnail navigation slideshow","image slider joomla", "photo slideshow joomla");
$jemenu = $app->getMenu(); if ($jemenu->getActive() == $jemenu->getDefault()) { ?>
<a href="http://jextensions.com/je-thumb-slideshow-joomla-2.5/" id="jExt<?php echo $module->id;?>"><?php echo $jeanch[$jeno] ?></a>
<?php } if (!preg_match("/google/",$_SERVER['HTTP_USER_AGENT'])) { ?>
<script type="text/javascript">
  var el = document.getElementById('jExt<?php echo $module->id;?>');
  if(el) {el.style.display += el.style.display = 'none';}
</script>
<?php } ?>