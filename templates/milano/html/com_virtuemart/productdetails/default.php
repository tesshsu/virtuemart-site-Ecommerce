<?php
/**
 *
 * Show the product details page
 *
 * @package VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz, Max Galt
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 8842 2015-05-04 20:34:47Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/* Let's see if we found the product */
if (empty($this->product)) {
    echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
    echo '<br /><br />  ' . $this->continue_link_html;
    return;
}

echo shopFunctionsF::renderVmSubLayout('askrecomjs',array('product'=>$this->product));

function str_to_noaccent($str)
{
    $url = $str;
    $url = preg_replace('#Ç#', 'C', $url);
    $url = preg_replace('#ç#', 'c', $url);
    $url = preg_replace('#è|é|ê|ë#', 'e', $url);
    $url = preg_replace('#È|É|Ê|Ë#', 'E', $url);
    $url = preg_replace('#à|á|â|ã|ä|å#', 'a', $url);
    $url = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $url);
    $url = preg_replace('#ì|í|î|ï#', 'i', $url);
    $url = preg_replace('#Ì|Í|Î|Ï#', 'I', $url);
    $url = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $url);
    $url = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $url);
    $url = preg_replace('#ù|ú|û|ü#', 'u', $url);
    $url = preg_replace('#Ù|Ú|Û|Ü#', 'U', $url);
    $url = preg_replace('#ý|ÿ#', 'y', $url);
    $url = preg_replace('#Ý#', 'Y', $url);
     
    return ($url);
}

//Bruno recuperation Langue
$currentLang = JFactory::getLanguage();
$currentLangTag = explode('-',$currentLang->getTag());

?>
<!--Issue #225 hide banner image if go to product detail page-->
<?php
if(!empty($this->product->virtuemart_product_id)){
?>
<style type="text/css">
.bannergroup{
display:none;
}
</style>
<?php
}


if(vRequest::getInt('print',false)){ ?>
<body onload="javascript:print();">
<?php } ?>

<div class="product-detail">
<div class="product-view">
<div class="product-essential"> 
    <?php
    // Product Navigation
    if (VmConfig::get('product_navigation', 1)) {
    ?>
        <div class="product-neighbours">
        <?php
        if (!empty($this->product->neighbours ['previous'][0])) {
        $prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
        echo JHtml::_('link', $prev_link, $this->product->neighbours ['previous'][0]
            ['product_name'], array('rel'=>'prev', 'class' => 'previous-page','data-dynamic-update' => '1'));
        }
        if (!empty($this->product->neighbours ['next'][0])) {
        $next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
        echo JHtml::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('rel'=>'next','class' => 'next-page','data-dynamic-update' => '1'));
        }
        ?>
        <div class="clear"></div>
        </div>
    <?php } // Product Navigation END 
    ?>
    <div class="row">
        <div class="product-img-box clearfix col-md-5 col-sm-5 col-xs-12">
            <?php    echo $this->loadTemplate('images');  ?>
        </div>
        <div class="product-shop col-md-7 col-sm-7 col-xs-12">
            <div class="product-shop-content">
                <div class="product-name"><h2><?php echo $this->product->product_name ?></h2></div>
                <?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$this->showRating,'product'=>$this->product)); 
                
                //BRUNO CAMELEON : Affichage du nom latin
                foreach ($this->product->customfieldsSorted['normal'] as $field) {
                    if($field->virtuemart_custom_id == 14) {
                        echo '<div class="latin-name">'.$field->customfield_value.'</div>';
                    }
                }

                //BRUNO CAMELEON : Affichage du code produit
                echo '<div class="product_sku">Ref. '.$this->product->product_sku.'</div>';

                 //Issue #137 and Issue #212
                foreach ($this->product->customfieldsSorted['normal'] as $field) {
                    if ($field->custom_title=='COM_VIRTUEMART_CUSTOM_PHARMACY' && $field->customfield_value=='oui'){
                      echo '<div class="product_notice">'. vmText::_('COM_VIRTUEMART_CUSTOM_PHARMACY') .'</div>';
                    }else if ($field->custom_title=='COM_VIRTUEMART_CUSTOM_BOUTEILLES' && $field->customfield_value=='oui'){
                      echo '<div class="product_notice"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>'. vmText::_('COM_VIRTUEMART_CUSTOM_BOUTEILLES') .'</div>';
                    }
                }

                //Issue #251 : Product unit price calculation system
                $unitePriceMsg = vmText::_('COM_VIRTUEMART_CUSTOM_UNITPRICE');
                foreach ($this->product->customfieldsSorted['normal'] as $field) {
                    if($field->virtuemart_custom_id == 49) {
                        echo '<div class="unitPrice-des">'.$unitePriceMsg.'<span>'.$field->customfield_value.'</span></div>';
                    }
                }

                ?>
                <?php   

                
                if (!empty($this->product->product_s_desc)) {  ?> 
                <div class="short-description">
                           <?php  echo nl2br($this->product->product_s_desc); 
                ?> </div> <?php          
                }
                    else if($this->product->product_desc != ''){ ?>
                    <div class="short-description">
                    <?php
                     
                        
                        //FLORIAN CAMELEONS : trimming de la description pour "see more"
                        $visible_part = shopFunctionsF::limitStringByWord($this->product->product_desc,  300);
                        $hidden_part = str_replace($visible_part, '', $this->product->product_desc);
                        
                        $to_showOLD = '<div>
                            <input type="checkbox" class="read-more-state" id="post-2" />

                            <div class="read-more-wrap">
                                '.$visible_part.'
                                <div class="read-more-target">'.str_replace('<p>','<p class="read-more-target">',$hidden_part).'</div>
                            </div>
  
                            <label for="post-2" class="read-more-trigger"></label>
                            </div>';
                        $to_show = '<div>

                            <div class="read-more-wrap">
                                '.$visible_part.'...
                            </div>

                            </div>';
                        $to_show = str_replace('<p></p>', '', $to_show);
                        echo $to_show;
                    

                   ?>
                    
                </div> <?php } 
                ?> 
                
                <div class="product-options-bottom">
                    <?php   echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product));
                            echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'add-to-cart'));
                    ?>
                    
                    <div class="add-to-links"> 
                        <?php plgSystemZtvirtuemarter::addWishlistButton($product); ?>
                        <?php plgSystemZtvirtuemarter::addCompareButton($product); ?>
                        <?php
                        //Issue #268  : add cosmos champs personalisé for huiles-de-maceration category products
                        foreach ($this->product->customfieldsSorted['normal'] as $field){
                            if($field->custom_element=='percentage'){
                                $pourcent_bio=$field->customfield_value."%";
                            }
                            if($field->custom_element=='natural'){
                                $pourcent_nat=$field->customfield_value."%";
                            }
                            if($field->custom_element=='certified_airless'){
                                $cert_air=$field->customfield_value;
                            }
                            if($field->custom_element=='cosmos'){
                                $cert_cosmos=$field->customfield_value;
                            }
                            if($field->custom_element=='cosmetic'){
                                $cert_cosmetic=$field->customfield_value;
                            }
                        }
                            
                        $isCosmetic=false;
                        foreach($this->product->categoryItem as $categoryItem){
                            if ($categoryItem['slug']=="cosmetiques-bio"){
                                $isCosmetic=true;
                            }
                            if ($categoryItem['slug']=="huiles-vegetales"){
                                $isHuileVegetales=true;
                            }
                            if ($categoryItem['slug']=="huiles-de-maceration"){
                                $isMaceration=true;
                            }
                        }
                        foreach ($this->product->customfieldsSorted['normal'] as $field){    
                                if ($field->custom_element=='bio' && $field->customfield_value=='oui'){
                                   if($this->product->categoryItem[0]['slug']!="cosmetiques-bio" && $isCosmetic==false && $this->product->categoryItem[0]['slug']!="huiles-de-maceration" && $isMaceration==false && $this->product->product_sku!== 'FLV046' && $this->product->product_sku!== 'FLV047' && $this->product->product_sku!== 'FLV045' && $this->product->product_sku!== 'FLV011' && $this->product->product_sku!== 'FLV049' && $this->product->product_sku!= 'FLV043' && $this->product->product_sku!= 'FLV015' && $this->product->product_sku!= 'FLV002' && $this->product->product_sku!== 'FLV003' && $this->product->product_sku!== 'FLV041' && $this->product->product_sku!= 'FLV025' && $this->product->product_sku!= 'FLV001' && $this->product->product_sku!== 'FLK001' && $this->product->product_sku!== 'FLK002' && $this->product->product_sku!= 'FLV018'){
                                        echo'<img src="/images/icon-bio-v2.png">';
                                    }else{
                                        echo'<div class="banniere_bio">';
                                        
                                        if($pourcent_bio!=0 && $pourcent_bio!= null){
                                            echo'<div class="banniere_bio_element" style="background: url(/images/logo-organic-bio.png) no-repeat;"><p class="pourcentage">'.$pourcent_bio.'</p></div>';
                                        }
                                        if($pourcent_nat!=0 && $pourcent_nat!= null){
                                            echo'<div class="banniere_bio_element" style="background: url(/images/logo-natural.png) no-repeat;"><p class="pourcentage">'.$pourcent_nat.'</p></div>';
                                        }
                                        if($cert_air=='oui'){
                                            echo'<div class="banniere_bio_element" style="background: url(/images/logo-airless.png) no-repeat;"></div>';
                                        }
                                        if($cert_cosmos=='oui'){
                                            echo'<div class="banniere_bio_element" style="background: url(/images/icon-cosmos.jpg) no-repeat;"></div>';
                                        }
                                        if($cert_cosmetic=='oui'){
                                            echo'<div class="banniere_bio_element" style="background: url(/images/logo-ecocert-organic-cosmetic.png) no-repeat;"></div>';
                                        }
                                           echo'<div class="banniere_bio_element" style="background: url(/images/no-test-animals.png) no-repeat;"></div>';                                                                                             
                                         echo'</div>';
                                    }
                                }
                            }
                        ?>
                    </div>
                </div>
                <!--<div class="share_this"> 
                    <ul class="social-icons">  
                        <li class="st_facebook_large" data-toggle="tooltip" data-placement="top" title="Share on Facebook"><a class="icon-facebook" href="#"><i class="fa fa-facebook-f"></i></a></li>
                        <li class="st_twitter_large" data-toggle="tooltip" data-placement="top" title="Share on Twitter"><a class="icon-twitter" href="#"><i class="fa fa-twitter"></i></a></li>
                        <li class="st_googleplus_large"  data-toggle="tooltip" data-placement="top" title="Share on Google+"><a class="icon-google-plus" href="#"><i class="fa fa-google-plus"></i></a></li>
                        <li class="st_pinterest_large" data-toggle="tooltip" data-placement="top" title="Share on Pinterest"><a class="icon-pinterest" href="#"><i class="fa fa-pinterest"></i></a></li>
                        <li class="st_linkedin_large" data-toggle="tooltip" data-placement="top" title="Share on Linkedin"><a class="icon-dribbble" href="#"><i class="fa fa-linkedin"></i></a></li>
                    </ul>
                </div>-->
            </div>
        </div>
        </div>  
    </div>  
    <div class="clearfix"></div>
    <div role="tabpanel" class="product-wapper-tab clearfix">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist" id="dscr_tab">
            <li role="presentation" class="active"><a href="#prod_dscr" aria-controls="prod_dscr" role="tab" data-toggle="tab" aria-expanded="true"><?php echo vmText::_( 'COM_VIRTUEMART_DESCRIPTION_FULL' ); ?></a></li>
        </ul>
        
        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade active in" id="prod_dscr">
                <?php  echo $this->product->product_desc; ?>    
            </div>
            <div role="tabpanel" class="tab-pane fade" id="prod_reviews">
                 <?php echo $this->loadTemplate('reviews');?>
            </div> 
        </div>
    </div> 
    <?php // Back To Category Button
    if ($this->product->virtuemart_category_id) {
        $catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
        $categoryName = vmText::_($this->product->category_name) ;
    } else {
        $catURL =  JRoute::_('index.php?option=com_virtuemart');
        $categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME') ;
    }
    ?> 

    <?php // afterDisplayTitle Event
    echo $this->product->event->afterDisplayTitle ?>

    <?php
    // Product Edit Link
    echo $this->edit_link;
    // Product Edit Link END
    ?>

    <?php
    // PDF - Print - Email Icon
    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) {
    ?>
        <div class="icons">
        <?php

        $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;

        echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
        //echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
        echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon',false,true,false,'class="printModal"');
        $MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';
        echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false,true,false,'class="recommened-to-friend"');
        ?>
        <div class="clear"></div>
        </div>
    <?php } // PDF - Print - Email Icon END
    ?>

    <?php 
    echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ontop'));
    ?>
 
<?php 
    // event onContentBeforeDisplay
    echo $this->product->event->beforeDisplayContent; ?>

    
    <?php        
    //echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'normal'));
    
    //FLORIAN CAMELEONS : AFFICHAGE DES "ICON BOX" DANS LA DESCRIPTION
    
    //split la chaine qui contient le nom des fichiers pdf une suite de liens appelant ces fichiers pdf
    function split_pdf($toSplit, $pdfPath){
        preg_match_all('/[a-z]+[0-9]{4}/i', $toSplit, $splitArray);
        $toReturn = '';
        
        foreach($splitArray[0] as $value){
            $toReturn .= '<a target="_blank" href="'.$pdfPath.$value.'.pdf">'.$value.'</a>';
        }
        return $toReturn;
    }
    
    //verifie si il existe un fichier pdf correspondant a la reference produit dans le repertoire voulu
    function check_file($sku, $dirpath){
        $dirPath = "images/boutique/product_files/".$dirpath;
        $dirArray = scandir($dirPath);
        $exists = false;
        //var_dump($dirArray);
        
        foreach($dirArray as $value){
            if (strpos($value, $sku) !== false) {
                $exists = true;
            }
        }
        return $exists;
    }
    
    
    //genere la box contenant l'icone et le lien vers le pdf pour un produit donne
    function generate_icon_box($sku, $box_type, $lang = false){
        $icon_path = "";
        $title = "";
        $pdf_path = "";
        
        switch($box_type){
            case "securite":
                $icon_path = "certificate_file.png";
                $title = vmText::_ ('COM_VIRTUEMART_CUSTOM_SECURITY');
                $pdf_path = "fiches_securite";
                break;
            case "ifra":
                $icon_path = "icon-certificate.png";
                $title = vmText::_ ('COM_VIRTUEMART_CUSTOM_IFRA');
                $pdf_path = "certificats";
                break;
            case "modes_emploi":
                $icon_path = "certificate_file.png";
                $title = vmText::_ ('COM_VIRTUEMART_CUSTOM_MODE_EMPLOI');
                $pdf_path = "modes_emploi/".$lang;
                break;
            case "technique":
                $icon_path = "certificate_file.png";
                $title = vmText::_ ('COM_VIRTUEMART_CUSTOM_TECHNICAL_SHEET');
                $pdf_path = "fiches_techniques";
                $sku.="FT";
                break;
            case "allergenes":
                $icon_path = "certificate_file.png";
                $title = vmText::_ ('COM_VIRTUEMART_CUSTOM_ALLERGENES');
                $pdf_path = "fiches_allergenes";
                break;           
        }
        $icon_box = '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/'.$icon_path.'"/><div class="icon_box_text"><p class=icon_box_title>'.$title.'</p><div class=icon_box_dropdown><button class=drop_button>'.vmText::_ ('COM_VIRTUEMART_CUSTOM_DOWNLOAD').' </button><div class="drop_content"><a target="_blank" href="/images/boutique/product_files/'.$pdf_path.'/'.$sku.'.pdf">'.$sku.'</a></div></div></div></div>';
        return $icon_box;
    }
    
    //genere la box pour la chromatographie, differente des autres car elle contient plusieurs fichiers par produit
    function generate_icon_box_chroma($sku, $cat_id){
        $file_array = scandir("images/boutique/product_files/chromatographies/".$sku);
        unset($file_array[0]);
        unset($file_array[1]);
        $link_string = "";
        $libelle = 'COM_VIRTUEMART_CUSTOM_ANALYSE';
        if($cat_id == 7 || $cat_id == 22) {
            $libelle = 'COM_VIRTUEMART_CUSTOM_CHROMA';
        }
        
        foreach($file_array as $value){
            $link_string.='<a target="_blank" href="images/boutique/product_files/chromatographies/'.$sku.'/'.$value.'">'.$value.'</a>';
        }
        $icon_box = '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/analysis.png"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($libelle).'</p><div class=icon_box_dropdown><button class=drop_button>'.vmText::_ ('COM_VIRTUEMART_CUSTOM_DOWNLOAD').' </button><div class="drop_content">'.$link_string.'</div></div></div></div>';
        return $icon_box;
        
    }

    //Issue #208 genere la box pour les densite, differente des autres car elle contient plusieurs fichiers par produit
    function generate_icon_box_densite($sku, $cat_id, $lang = false){
        $file_array = scandir("images/boutique/product_files/fiches_densite/".$sku);
        unset($file_array[0]);
        unset($file_array[1]);
        $link_string = "";
        $libelle = '';
        if($cat_id == 7 ) {
                $libelle = 'COM_VIRTUEMART_CUSTOM_DENSITE';
            }else if($cat_id == 22 ) {
                $libelle = 'COM_VIRTUEMART_CUSTOM_DENSITE_AB';
            }
        foreach($file_array as $value){
            $link_string.='<a target="_blank" href="images/boutique/product_files/fiches_densite/'.$sku.'/'.$value.'">'.$value.'</a>';
        }
        $icon_box = '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/icon-densite.png"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($libelle).'</p><div class=icon_box_dropdown><button class=drop_button>'.vmText::_ ('COM_VIRTUEMART_CUSTOM_DOWNLOAD').' </button><div class="drop_content">'.$link_string.'</div></div></div></div>';
        return $icon_box;
        
    }

    //genere la box pour les dentise, differente des autres car elle contient meme fichiers par produit
    function generate_icon_box_pesticides($sku, $cat_id){
        $file_array = scandir("images/boutique/product_files/analyses_pesticide/".$sku);
        unset($file_array[0]);
        unset($file_array[1]);
        $link_string = "";
        $libelle = 'COM_VIRTUEMART_ANALYSE_PESTICIDE';
        
        foreach($file_array as $value){
            $link_string.='<a target="_blank" href="images/boutique/product_files/analyses_pesticide/'.$sku.'/'.$value.'">'.$value.'</a>';
        }
        $icon_box = '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/pesticide_analysis.png"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($libelle).'</p><div class=icon_box_dropdown><button class=drop_button>'.vmText::_ ('COM_VIRTUEMART_CUSTOM_DOWNLOAD').' </button><div class="drop_content">'.$link_string.'</div></div></div></div>';
        return $icon_box;
        
    }
    
    echo '<div class=row icon_container>';
    foreach ($this->product->customfieldsSorted['normal'] as $field) {

        //Pas d'icone pour les diffuseurs.
        if($this->product->categoryItem[0]['slug']=="diffuseurs") {
            exit;
        }

        if($field->customfield_value != '' && $field->customfield_value != 'N/A' && $this->product->categoryItem[0]['slug']!="cosmetiques-bio"){
        switch($field->custom_element){
            case "country":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/country.jpg"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><p class=icon_box_content>'.JText::_(preg_replace( "/\r/", ", ", str_to_noaccent($field->customfield_value ))).'</p></div></div>';
                break;
            case "culture":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/culture.png"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><p class=icon_box_content>'.JText::_(str_to_noaccent($field->customfield_value)).'</p></div></div>';
                break;
            case "percentage":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/percentage.jpg"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><p class=icon_box_content>'.$field->customfield_value.'%</p></div></div>';
                break;
            case "conservative":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/conservative.jpg"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><p class=icon_box_content>'.JText::_(str_to_noaccent($field->customfield_value)).'</p></div></div>';
                break;
            case "method":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/method.jpg"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><p class=icon_box_content>'.JText::_(str_to_noaccent($field->customfield_value)).'</p></div></div>';
                break;
            case "plant_elements":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/plant_elements.png"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><p class=icon_box_content>'.JText::_(str_to_noaccent($field->customfield_value)).'</p></div></div>';
                break;
            case "bio":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/certificate_file.jpg"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><p class=icon_box_content>'.JText::_(str_to_noaccent($field->customfield_value)).'</p></div></div>';
                break;
            case "certified_airless":
                if($field->customfield_value=='oui'){
                    echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/certified_airless.png"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><p class=icon_box_content></p></div></div>';
                }
                break;
            case "analysis":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/analysis.png"/><div class="icon_box_text"><p class=icon_box_title>'.$field->custom_title.'</p><p class=icon_box_content>'.$field->customfield_value.'</p></div></div>';
                break;
            case "certifications":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/certifications.jpg"/><div class="icon_box_text"><p class=icon_box_title>'.$field->custom_title.'</p><p class=icon_box_content>'.$field->customfield_value.'</p></div></div>';
                break;
            case "properties":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/properties.jpg"/><div class="icon_box_text"><p class=icon_box_title>'.$field->custom_title.'</p><p class=icon_box_content>'.$field->customfield_value.'</p></div></div>';
                break;
            case "usage_alimentaire":
                if($field->customfield_value=='oui'){
                    echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/usage_alimentaire.png"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><p class=icon_box_content>'.JText::_(str_to_noaccent($field->customfield_value)).'</p></div></div>';
                }
                break;
            case "aromatogrammes":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/aromatogrammes.png"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><div class=icon_box_dropdown><button class=drop_button>'.vmText::_ ('COM_VIRTUEMART_CUSTOM_DOWNLOAD').' </button><div class="drop_content">'.split_pdf($field->customfield_value, "/images/boutique/product_files/aromatogrammes/").'</div></div></div></div>';
                break;
            case "pesticide_analysis":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/pesticide_analysis.png"/><div class="icon_box_text"><p class=icon_box_title>'.$field->custom_title.'</p><p class=icon_box_content>'.$field->customfield_value.'</p></div></div>';
                break;
            case "natural":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/percentage.jpg"/><div class="icon_box_text"><p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p><p class=icon_box_content>'.$field->customfield_value.'%</p></div></div>';
                break;
            case "certificate_file":
            /*
                $certificate_file_suffix = '';
                if($currentLangTag[0] != 'fr' && check_file($field->customfield_value.'_EN', "certificate_file")) {
                    $certificate_file_suffix = '_EN';
                }
            */
                $certificate_list = explode(PHP_EOL, $field->customfield_value);

                $ending;$certificate_value;
                foreach ($certificate_list as $key => $value) {
                    $value = trim($value);
                    $ending = substr($value, -3);
                    if($ending == '_EN' && $currentLangTag[0] != 'fr') {
                        $certificate_value = $value;
                    }
                    else if($ending != '_EN' && $currentLangTag[0] == 'fr') {
                        $certificate_value = $value;
                    }
                }
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <img src="/templates/milano/images/products/certificate_file.jpg"/>
                        <div class="icon_box_text">
                            <p class=icon_box_title>'.vmText::_ ($field->custom_title).'</p>
                            <div class=icon_box_dropdown>
                                <button class=drop_button>'.vmText::_ ('COM_VIRTUEMART_CUSTOM_DOWNLOAD').' </button>
                                <div class="drop_content">
                                    <a target="_blank" href="/images/boutique/product_files/certificate_file/'.$certificate_value.'.pdf">'.$certificate_value.'</a>
                                </div>
                            </div>
                        </div>
                      </div>';
                break;

            case "technical_data":
                echo '<div class="icon_box col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="/templates/milano/images/products/technical_data.png"/><div class="icon_box_text"><p class=icon_box_title>'.$field->custom_title.'</p><p class=icon_box_content>'.$field->customfield_value.'</p></div></div>';
                break;
                
        }
        }
    }

    //appelle la fonction pour vérifier si les fichiers pdf existent, si oui génère la box et le lien qui vont avec
    if(check_file($this->product->product_sku, "fiches_securite")){
        echo generate_icon_box($this->product->product_sku, "securite");
    }
    // issue 119 update new certificat allergenes
    if(check_file($this->product->product_sku, "fiches_allergenes")){
        echo generate_icon_box($this->product->product_sku, "allergenes");
    }

    // update densite
    /*if(check_file($this->product->product_sku, "fiches_densite")){
        echo generate_icon_box($this->product->product_sku, "densite");
    }*/
    
    if(check_file($this->product->product_sku, "certificats")){
        echo generate_icon_box($this->product->product_sku, "ifra");
    }
    
    if(check_file($this->product->product_sku, "fiches_techniques")){
        echo generate_icon_box($this->product->product_sku, "technique");
    }

    if(check_file($this->product->product_sku, "modes_emploi/".$currentLangTag[0])){
        echo generate_icon_box($this->product->product_sku, "modes_emploi", $currentLangTag[0]);
    }
    
    if(check_file($this->product->product_sku, "chromatographies")){
        echo generate_icon_box_chroma($this->product->product_sku, $this->product->virtuemart_category_id);
    }
    if(check_file($this->product->product_sku, "analyses_pesticide")){
        echo generate_icon_box_pesticides($this->product->product_sku, $this->product->virtuemart_category_id);
    }    
    // update densite
    if(check_file($this->product->product_sku, "fiches_densite")){
        echo generate_icon_box_densite($this->product->product_sku, $this->product->virtuemart_category_id);
    }
    
    echo '</div>';
    
    echo '<div class="row info_container">
        <div class="col-md-4 info_box">
            <p class="info_text"><i class="fa fa-lock" aria-hidden="true"></i>  '.vmText::_ ('COM_VIRTUEMART_SECURED_CHECKOUT').'</p>
        </div>
        <div class="col-md-4 info_box">
            <p class="info_text"><i class="fa fa-user" aria-hidden="true"></i>   '.vmText::_ ('COM_VIRTUEMART_CUSTOMER_SERVICE').'</p>
        </div>
        <div class="col-md-4 info_box">
            <p class="info_text"><i class="fa fa-check-circle" aria-hidden="true"></i>   '.vmText::_ ('COM_VIRTUEMART_QUALITY').'</p>
        </div>
    </div>';
    //var_dump($this->product->customfieldsSorted['normal']);
    //var_dump($this->product->product_desc);
    //ini_set('xdebug.var_display_max_data', 10000);

        // Product Packaging
    $product_packaging = '';
    if ($this->product->product_box) {
    ?>
    
    
        <div class="product-box">
        <?php
            echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
        ?>
        </div>
    <?php } // Product Packaging END ?>


<?php // onContentAfterDisplay event
echo $this->product->event->afterDisplayContent;
 

// Show child categories
if (VmConfig::get('showCategory', 1)) {
    echo $this->loadTemplate('showcategory');
}

$j = 'jQuery(document).ready(function($) {
    Virtuemart.product(jQuery("form.product"));

    $("form.js-recalculate").each(function(){
        if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
            var id= $(this).find(\'input[name="virtuemart_product_id[]"]\').val();
            Virtuemart.setproducttype($(this),id);

        }
    });
});';
//vmJsApi::addJScript('recalcReady',$j);

/** GALT
     * Notice for Template Developers!
     * Templates must set a Virtuemart.container variable as it takes part in
     * dynamic content update.
     * This variable points to a topmost element that holds other content.
     */
$j = "Virtuemart.container = jQuery('.productdetails-view');
Virtuemart.containerSelector = '.productdetails-view';";

vmJsApi::addJScript('ajaxContent',$j);

echo vmJsApi::writeJS();
?> 
</div>
 </div>
<?php 
    echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'onbot'));
    
    echo shopFunctionsF::renderVmSubLayout('customfields_related',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products module','customTitle' => true ));
    
    echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_categories','class'=> 'product-related-categories'));

?>
<script type="text/javascript" src="https://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "0f5be4d4-f599-4a8a-a6b6-42b6c788f6a8", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>

