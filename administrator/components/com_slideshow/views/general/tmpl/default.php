 <?php 
/**
 * @package Slideshow
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 **/
?>
<?php
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::stylesheet(Juri::root() . 'media/com_slideshow/style/admin.style.css');
$doc = JFactory::getDocument();
$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js");
$doc->addScript("http://code.jquery.com/ui/1.10.4/jquery-ui.js");
$doc->addScript(JURI::root(true) . "/media/com_slideshow/js/admin.js");
$doc->addScript(JURI::root(true) . "/media/com_slideshow/js/simple-slider.js");
$doc->addScript(JURI::root(true) . "/media/com_slideshow/elements/jscolor/jscolor.js");
JHtml::stylesheet('media/com_slideshow/style/simple-slider.css');
JHtml::stylesheet('media/com_slideshow/style/admin.style.css');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'general.apply1') {
            alert("Sorry, the General Settings are disabled in this free version, please purchase the commercial version for the full features.");
        }
        else if (task == 'general.save')
        {
            Joomla.submitform('apply1', document.getElementById('application-form'));
            alert("Sorry, the General Settings are disabled in this free version, please purchase the commercial version for the full features.");
        }
        else if (task == 'application.cancel' || document.formvalidator.isValid(document.id('application-form')))
        {
            Joomla.submitform(task, document.getElementById('application-form'));
        }
    }
</script>


<?php
foreach ($this->optionsPropertie as $optionsPropertie) {
    $key = $optionsPropertie->name;
    $value = $optionsPropertie->value;
    $param_values[$key] = $value;
}
?>
<?php $path_site = JURI::root() . 'media/com_slideshow/images/Front_images' ?>
<div id="slideshow-options-list">
    <div class="wrap">
        <div id="poststuff">
               <div id="h-sidebar-container" class="h-sidebar-container h-sidebar-visible">
                <div id="h-toggle-sidebar-wrapper">
                    <div id="h-toggle-sidebar-header" class="h-toggle-sidebar-header">
                        <div id="sidebar" class="sidebar">
                            <div class="sidebar-nav">
                                <ul id="submenu" class="nav nav-list">
                                    <li>
                                        <a href="index.php?option=com_slideshow"><?php echo JText::_('COM_SLIDESHOW') ?></a>
                                    </li>
                                    <li  class="active">
                                        <a href="index.php?option=com_slideshow&amp;view=general"><?php echo JText::_('COM_GENERAL') ?></a>
                                    </li>
                                    <li>
                                        <a href="index.php?option=com_slideshow&amp;view=featured"><?php echo JText::_('COM_FEAUTURED') ?></a>
                                    </li>
                                </ul>
                                <div>
                                </div>
                                <div id="h-toggle-sidebar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="h-main-container" class="span10 h-toggle-main" style="border-top:1px solid  #ccc">
                <div class="wrap">

                    <?php $path_site2 = JUri::root() . "media/com_slider/images" ?>

                    <div id="poststuff">
                        <?php $path_site2 = JUri::root() . "media/com_slider/images/"; ?>
                        <?php $path_site2 = JUri::root() . "media/com_slider/images";
                        ?>
                        <style>
                            .free_version_banner {
                                position:relative;
                                display:block;
                                background-image:url(<?php echo $path_site2; ?>/wp_banner_bg.jpg);
                                background-position:top left;
                                backround-repeat:repeat;
                                overflow:hidden;
                                margin-left:12px;
                            }

                            .free_version_banner .manual_icon {
                                position:absolute;
                                display:block;
                                top:15px;
                                left:15px;
                            }

                            .free_version_banner .usermanual_text {
                                font-weight: bold !important;
                                display:block;
                                float:left;
                                width:270px;
                                margin-left:75px;
                                font-family:'Open Sans',sans-serif;
                                font-size:12px;
                                font-weight:300;
                                font-style:italic;
                                color:#ffffff;
                                line-height:10px;
                                margin-top: 0;
                                padding-top: 15px;
                            }

                            .free_version_banner .usermanual_text a,
                            .free_version_banner .usermanual_text a:link,
                            .free_version_banner .usermanual_text a:visited {
                                display:inline-block;
                                font-family:'Open Sans',sans-serif;
                                font-size:17px;
                                font-weight:600;
                                font-style:italic;
                                color:#ffffff;
                                line-height:30.5px;
                                text-decoration:underline;
                            }

                            .free_version_banner .usermanual_text a:hover,
                            .free_version_banner .usermanual_text a:focus,
                            .free_version_banner .usermanual_text a:active {
                                text-decoration:underline;
                            }

                            .free_version_banner .get_full_version,
                            .free_version_banner .get_full_version:link,
                            .free_version_banner .get_full_version:visited {
                                padding-left: 60px;
                                padding-right: 4px;
                                display: inline-block;
                                position: absolute;
                                top: 15px;
                                right: calc(50% - 167px);
                                height: 38px;
                                width: 285px;
                                border: 1px solid rgba(255,255,255,.6);
                                font-family: 'Open Sans',sans-serif;
                                font-size: 23px;
                                color: #ffffff;
                                line-height: 43px;
                                text-decoration: none;
                                border-radius: 2px;
                            }

                            .free_version_banner .get_full_version:hover {
                                background:#ffffff;
                                color:#bf1e2e;
                                text-decoration:none;
                                outline:none;
                            }

                            .free_version_banner .get_full_version:focus,
                            .free_version_banner .get_full_version:active {

                            }

                            .free_version_banner .get_full_version:before {
                                content:'';
                                display:block;
                                position:absolute;
                                width:33px;
                                height:23px;
                                left:25px;
                                top:9px;
                                background-image:url(<?php echo $path_site2; ?>/wp_shop.png);
                                background-position:0px 0px;
                                background-repeat:repeat;
                            }

                            .free_version_banner .get_full_version:hover:before {
                                background-position:0px -27px;
                            }

                            .free_version_banner .huge_it_logo {
                                float:right;
                                margin:15px 15px;
                            }

                            .free_version_banner .description_text {
                                padding:0 0 13px 0;
                                position:relative;
                                display:block;
                                width:100%;
                                text-align:center;
                                float:left;
                                font-family:'Open Sans',sans-serif;
                                color:#fffefe;
                                line-height:inherit;
                            }
                            .free_version_banner .description_text p{
                                margin:0;
                                padding:0;
                                font-size: 14px;
                            }
                        </style>
                        <div class="free_version_banner">
                            <img class="manual_icon" src="<?php echo $path_site2; ?>/icon-user-manual.png" alt="user manual" />
                            <p class="usermanual_text">If you have any difficulties in using the options, Follow the link to <a href="http://huge-it.com/joomla-slideshow-user-manual/" target="_blank">User Manual</a></p>
                            <a class="get_full_version" href="http://huge-it.com/joomla-slideshow/" target="_blank">GET THE FULL VERSION</a>
                            <a href="http://huge-it.com" target="_blank"><img class="huge_it_logo" src="<?php echo $path_site2; ?>/Huge-It-logo.png"/></a>
                            <div style="clear: both;"></div>
                            <div  class="description_text"><p>This is the free version of the plugin. In order to use options from this section, get the full version. We appreciate every customer.</p></div>
                        </div>
                        <div style="clear:both;"></div>
                        <div style="color: #a00; margin-bottom: 15px;margin-left:12px">This options are for commercial users, it includes one of Personal, Multi-Site or Developer versions.Please upgrade to use this section.
                        </div>

                    </div>
                </div>
                <form action="<?php echo JRoute::_('index.php?option=com_slideshow'); ?>" id="application-form" method="post" name="adminForm" class="form-validate">
                    <div class="options-block" id="options-block-title" >
                        
                         <div>
                            <label for="title-background-transparency">Title Width <div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Select the width for the titl</div>
                                </div>
                            </label>
                            <div class="slideshow-container">
                                <input name="params[slideshow_title_width]" id="title-background-transparency" data-slider-highlight="true"  data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" value="<?php echo $param_values['slideshow_title_width']; ?>" />
                                <span><?php echo $param_values['slideshow_title_width']; ?>%</span>
                            </div>
                        </div>
                        
                       
                        <div>
                            <label for="slider_title_has_margin">Title Has Margin
                                <div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                        <div class="pnt"></div>
                                    <div class="showme">Choose the margin level for title</div>
                                </div>
                            </label>    
                            <input type="hidden" value="off" name="params[slideshow_title_has_margin]" />                   
                            <input type="checkbox" id="slider_title_has_margin"  <?php if ($param_values['slideshow_title_has_margin'] == 'on') {
    echo 'checked="checked"';
} ?>  name="params[slideshow_title_has_margin]"  value="on" />
                        </div>
                        <div class="has-background">
                            <label for="slider_title_font_size">Title Font Size<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Specify the font size for the image title</div>
                                </div>
                            </label>
                            <input type="number" name="params[slideshow_title_font_size]" id="slider_title_font_size" value="<?php echo $param_values['slideshow_title_font_size']; ?>" class="text" />
                            <span>px</span>
                        </div>
                        <div>
                            <label for="slider_title_color">Title Text Color<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the color for the title</div>
                                </div>
                            </label>
                            <input name="params[slideshow_title_color]" type="text" class="color" id="slider_title_color" value="#<?php echo $param_values['slideshow_title_color']; ?>" size="10" />
                        </div>
                        <div  class="has-background">
                            <label for="slider_title_text_align">Title Text Align<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Choose where to place the title</div>
                                </div>
                            </label>
                            <select id="slider_title_text_align" name="params[slideshow_title_text_align]">
                                <option <?php if ($param_values['slideshow_title_text_align'] == 'justify') {
                                        echo 'justify';
                                    } ?> value="justify">Full width</option>
                                                                    <option <?php if ($param_values['slideshow_title_text_align'] == 'center') {
                                        echo 'selected';
                                    } ?> value="center">Center</option>
                                                                    <option <?php if ($param_values['slideshow_title_text_align'] == 'left') {
                                        echo 'selected';
                                    } ?> value="left">Left</option>
                                                                    <option <?php if ($param_values['slideshow_title_text_align'] == 'right') {
                                        
                                    } ?> value="right">Right</option>
                            </select>
                        </div>
                        <div>
                            <label for="title-background-transparency">Title Background Transparency<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the level of transparency for the title</div>
                                </div>
                            </label>
                            <div class="slideshow-container">
                                <input name="params[slideshow_title_background_transparency]" id="title-background-transparency" data-slider-highlight="true"  data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" value="<?php echo $param_values['slideshow_title_background_transparency']; ?>" />
                                <span><?php echo $param_values['slideshow_title_background_transparency']; ?>%</span>
                            </div>
                        </div>
                        <div class="has-background">
                            <label for="slider_title_background_color">Title Background Color<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Choose the color for the cell containing the title</div>
                                </div>
                            </label>
                            <input name="params[slideshow_title_background_color]" type="text" class="color" id="slider_title_background_color" value="#<?php echo $param_values['slideshow_title_background_color']; ?>" size="10" />
                        </div>
                        <div>
                            <label for="slider_title_border_size">Title Border Size<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the border size for the title</div>
                                </div></label>
                            <input type="number" name="params[slideshow_title_border_size]" id="slider_title_border_size" value="<?php echo $param_values['slideshow_title_border_size']; ?>" class="text" />
                            <span>px</span>
                        </div>
                        <div class="has-background">
                            <label for="slider_title_border_color">Title Border Color<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Select the border color for the title</div>
                                </div></label>
                            <input name="params[slideshow_title_border_color]" type="text" class="color" id="slider_title_border_color" value="#<?php echo $param_values['slideshow_title_border_color']; ?>" size="10">
                        </div>
                        <div>
                            <label for="slider_title_border_radius">Title Border Radius<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the border radius for the title</div>
                                </div></label>
                            <input type="number" name="params[slideshow_title_border_radius]" id="slider_title_border_radius" value="<?php echo $param_values['slideshow_title_border_radius']; ?>" class="text" />
                            <span>px</span>
                        </div>
                        <div class="has-height has-background" style="height:65px;">
                            <label for="">Title Position<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Define the position of the title using the view graph</div>
                                </div></label>
                            <div>
                                <table class="bws_position_table">
                                    <tbody>
                                        <tr>
                                            <td><input type="radio" value="left-top" id="slider_title_top-left" name="params[slideshow_title_position]" <?php if ($param_values['slideshow_title_position'] == 'left-top') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="center-top" id="slider_title_top-center" name="params[slideshow_title_position]" <?php if ($param_values['slideshow_title_position'] == 'center-top') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="right-top" id="slider_title_top-right" name="params[slideshow_title_position]"  <?php if ($param_values['slideshow_title_position'] == 'right-top') {
    echo 'checked="checked"';
} ?> /></td>
                                        </tr>
                                        <tr>
                                            <td><input type="radio" value="left-middle" id="slider_title_middle-left" name="params[slideshow_title_position]" <?php if ($param_values['slideshow_title_position'] == 'left-middle') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="center-middle" id="slider_title_middle-center" name="params[slideshow_title_position]" <?php if ($param_values['slideshow_title_position'] == 'center-middle') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="right-middle" id="slider_title_middle-right" name="params[slideshow_title_position]" <?php if ($param_values['slideshow_title_position'] == 'right-middle') {
    echo 'checked="checked"';
} ?> /></td>
                                        </tr>
                                        <tr>
                                            <td><input type="radio" value="left-bottom" id="slider_title_bottom-left" name="params[slideshow_title_position]" <?php if ($param_values['slideshow_title_position'] == 'left-bottom') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="center-bottom" id="slider_title_bottom-center" name="params[slideshow_title_position]" <?php if ($param_values['slideshow_title_position'] == 'center-bottom') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="right-bottom" id="slider_title_bottom-right" name="params[slideshow_title_position]" <?php if ($param_values['slideshow_title_position'] == 'right-bottom') {
    echo 'checked="checked"';
} ?> /></td>
                                        </tr>
                                    </tbody>    
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="options-block" id="options-block-description">
                        
                          <div>
                            <label for="description-background-transparency">Description Width<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the width for the description texty</div>
                                </div></label>
                            <div class="slideshow-container">
                                <input name="params[slideshow_description_width]" id="description-background-transparency" data-slider-highlight="true"  data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" value="<?php echo $param_values['slideshow_description_width']; ?>" />
                                <span><?php echo $param_values['slideshow_description_width']; ?>%</span>
                            </div>
                        </div>
                     
                        <!--<div>
                                <label for="params[slideshow_description_height]">description height</label>
                                <div class="slideshow-container">
                                        <input name="params[slideshow_description_height]" id="description-container-height" data-slider-range="1,100" type="text" data-slider="true"  data-slider-highlight="true" value="<?php echo $param_values['slideshow_description_height']; ?>" />
                                        <span><?php echo $param_values['slideshow_description_height']; ?>%</span>
                                </div>
                        </div>-->
                        <div>
                            <label for="slider_description_has_margin">Description Has Margin<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Choose the margin for description text</div>
                                </div></label>
                            <input type="hidden" value="off" name="params[slideshow_description_has_margin]" />
                            <input type="checkbox" id="slider_description_has_margin"  <?php if ($param_values['slideshow_description_has_margin'] == 'on') {
    echo 'checked="checked"';
} ?>  name="params[slideshow_description_has_margin]" value="on" />
                        </div>
                        <div class="has-background">
                            <label for="slider_description_font_size">Description Font Size<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Specify the font size for the image description</div>
                                </div></label>
                            <input type="number" name="params[slideshow_description_font_size]" id="slider_description_font_size" value="<?php echo $param_values['slideshow_description_font_size']; ?>" class="text" />
                            <span>px</span>
                        </div>
                        <div>
                            <label for="slider_description_color">Description Text Color<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the color for the image description</div>
                                </div></label>
                            <input name="params[slideshow_description_color]" type="text" class="color" id="slider_description_color" value="#<?php echo $param_values['slideshow_description_color']; ?>" size="10" />
                        </div>
                        <div  class="has-background">
                            <label for="slider_description_text_align">Description Text Align<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">choose where to place the description text</div>
                                </div></label>
                                <select id="slider_description_text_align" name="params[slideshow_description_text_align]"> 
                                        <option <?php if ($param_values['slideshow_description_text_align'] == 'justify') {
                                        echo 'selected';
                                    } ?> value="justify">Full width</option>
                                        <option <?php if ($param_values['slideshow_description_text_align'] == 'center') {
                                        echo 'selected';
                                    } ?> value="center">Center</option>
                                        <option <?php if ($param_values['slideshow_description_text_align'] == 'left') {
                                        echo 'selected';
                                    } ?> value="left">Left</option>
                                        <option <?php if ($param_values['slideshow_description_text_align'] == 'right') {
                                        echo 'selected';
                                    } ?> value="right">Right</option>
                               </select>
                                                    </div>
                        <div>
                            <label for="description-background-transparency">Description Background Transparency<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the level of description background transparency</div>
                                </div></label>
                            <div class="slideshow-container">
                                <input name="params[slideshow_description_background_transparency]" id="description-background-transparency" data-slider-highlight="true"  data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" value="<?php echo $param_values['slideshow_description_background_transparency']; ?>" />
                                <span><?php echo $param_values['slideshow_description_background_transparency']; ?>%</span>
                            </div>
                        </div>
                        <div class="has-background">
                            <label for="slider_description_background_color">Description Background Color<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Choose the color for description's background</div>
                                </div></label>
                            <input name="params[slideshow_description_background_color]" type="text" class="color" id="slider_description_background_color" value="#<?php echo $param_values['slideshow_description_background_color']; ?>" size="10">
                        </div>
                        <div>
                            <label for="slider_description_border_size">Description Border Size<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Specify the border for the image description</div>
                                </div></label>
                            <input type="number" name="params[slideshow_description_border_size]" id="slider_description_border_size" value="<?php echo $param_values['slideshow_description_border_size']; ?>" class="text" />
                            <span>px</span>
                        </div>
                        <div class="has-background">
                            <label for="slider_description_border_color">Description Border Color<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Select the border color for the image description</div>
                                </div></label>
                            <input name="params[slideshow_description_border_color]" type="text" class="color" id="slider_description_border_color" value="#<?php echo $param_values['slideshow_description_border_color']; ?>" size="10">
                        </div>
                        <div>
                            <label for="slider_description_border_radius">Description Border Radius<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the border radius for the image description cell</div>
                                </div></label>
                            <input type="number" name="params[slideshow_description_border_radius]" id="slider_description_border_radius" value="<?php echo $param_values['slideshow_description_border_radius']; ?>" class="text" />
                            <span>px</span>
                        </div>
                        <div class="has-height has-background" style="height: 65px">
                            <label for="params[slideshow_description_position]">Description Position<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Select the positioning of the description. Please make sure it does not coincide with the title position avoiding overloading</div>
                                </div>
                            </label>
                            <div>
                                <table class="bws_position_table">
                                    <tbody>
                                        <tr>
                                            <td><input type="radio" value="left-top" id="slider_description_top-left" name="params[slideshow_description_position]" <?php if ($param_values['slideshow_description_position'] == 'left-top') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="center-top" id="slider_description_top-center" name="params[slideshow_description_position]" <?php if ($param_values['slideshow_description_position'] == 'center-top') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="right-top" id="slider_description_top-right" name="params[slideshow_description_position]"  <?php if ($param_values['slideshow_description_position'] == 'right-top') {
    echo 'checked="checked"';
} ?> /></td>
                                        </tr>
                                        <tr>
                                            <td><input type="radio" value="left-middle" id="slider_description_middle-left" name="params[slideshow_description_position]" <?php if ($param_values['slideshow_description_position'] == 'left-middle') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="center-middle" id="slider_description_middle-center" name="params[slideshow_description_position]" <?php if ($param_values['slideshow_description_position'] == 'center-middle') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="right-middle" id="slider_description_middle-right" name="params[slideshow_description_position]" <?php if ($param_values['slideshow_description_position'] == 'right-middle') {
    echo 'checked="checked"';
} ?> /></td>
                                        </tr>
                                        <tr>
                                            <td><input type="radio" value="left-bottom" id="slider_description_bottom-left" name="params[slideshow_description_position]" <?php if ($param_values['slideshow_description_position'] == 'left-bottom') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="center-bottom" id="slider_description_bottom-center" name="params[slideshow_description_position]" <?php if ($param_values['slideshow_description_position'] == 'center-bottom') {
    echo 'checked="checked"';
} ?> /></td>
                                            <td><input type="radio" value="right-bottom" id="slider_description_bottom-right" name="params[slideshow_description_position]" <?php if ($param_values['slideshow_description_position'] == 'right-bottom') {
    echo 'checked="checked"';
} ?> /></td>
                                        </tr>
                                    </tbody>    
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="options-block" id="options-block-slider">
                        <div>
                            <label for="slider_crop_image">Image Behaviour<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Choose how to behave the image in slider</div>
                                </div>
                            </label>
                            <select id="slider_crop_image" name="params[slideshow_crop_image]">
                                <option <?php if ($param_values['slideshow_crop_image'] == 'crop') {
    echo 'selected';
} ?> value="crop">Natural</option>
                                <option <?php if ($param_values['slideshow_crop_image'] == 'resize') {
    echo 'selected';
} ?> value="resize">Resize</option>
                            </select>
                        </div>
                        <div class="has-background">
                            <label for="slider_slider_background_color">Slider Background Color<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the color for background of the slider</div>
                                </div>
                            </label>
                            <input name="params[slideshow_slideshow_background_color]" type="text" class="color" id="slider_slider_background_color" value="#<?php echo $param_values['slideshow_slideshow_background_color']; ?>" size="10">
                        </div>

                        <div>
                            <label for="slider_slider_border_size">Slideshow Border Size<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the border for the slider</div>
                                </div>
                            </label>
                            <input type="number" name="params[slideshow_slideshow_border_size]" id="slider_slider_border_size" value="<?php echo $param_values['slideshow_slideshow_border_size']; ?>" class="text" />
                        </div>
                        <div class="has-background">
                            <label for="slider_slider_border_color">Slideshow Border Color<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Select the border color for the slider</div>
                                </div>
                            </label>
                            <input name="params[slideshow_slideshow_border_color]" type="text" class="color" id="slider_slider_border_color" value="#<?php echo $param_values['slideshow_slideshow_border_color']; ?>" size="10">
                        </div>
                        <div> 
                            <label for="slider_slider_border_radius">Slideshow Border radius<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the border radius for the slider</div>
                                </div>
                            </label>
                            <input type="number" name="params[slideshow_slideshow_border_radius]" id="slider_slider_border_radius" value="<?php echo $param_values['slideshow_slideshow_border_radius']; ?>" class="text" />
                        </div>
                    </div>
                    <div class="options-block" id="options-block-navigation">
                        <div>
                            <label for="slider_show_arrows">Show Navigation Arrows <div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Choose whether to show navigation arrows</div>
                                </div>
                            </label>
                            <input type="hidden" value="off" name="params[slideshow_show_arrows]" />        
                            <input type="checkbox" id="slider_show_arrows" <?php if ($param_values['slideshow_show_arrows'] == 'on') {
    echo 'checked="checked"';
} ?> name="params[slideshow_show_arrows]" value="on" />
                        </div>
                        <div class="has-background">
                            <label for="slider_dots_position">Navigation Dots Position / Hide Dots<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Set the position for the navigation arrows</div>
                                </div>
                            </label>
                            <select id="slider_dots_position" name="params[slideshow_dots_position]">
                                <option <?php if ($param_values['slideshow_dots_position'] == 'none') {
    echo 'selected';
} ?> value="none">Dont Show</option>
                                <option <?php if ($param_values['slideshow_dots_position'] == 'top') {
    echo 'selected';
} ?> value="top">Top</option>
                                <option <?php if ($param_values['slideshow_dots_position'] == 'bottom') {
    echo 'selected';
} ?> value="bottom">Bottom</option>
                            </select>
                        </div>
                        <div>
                            <label for="slider_dots_color">Navigation Dots Color<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Select the dot color for the navigation</div>
                                </div>
                            </label>
                            <input type="text" class="color" name="params[slideshow_dots_color]" id="slider_dots_color" value="<?php echo $param_values['slideshow_dots_color']; ?>" class="text" />
                        </div>
                        <div class="has-background">
                            <label for="slider_active_dot_color">Navigation Active Dot Color<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Specify the color for the dot for the currently displayed image</div>
                                </div>
                            </label>
                            <input type="text" class="color" name="params[slideshow_active_dot_color]" id="slider_active_dot_color" value="<?php echo $param_values['slideshow_active_dot_color']; ?>" class="text" />
                        </div>
                        <div class="has-height" style="padding-top:20px;">
                            <label for="">Navigation Type<div class="help">
                                    <div style="position: relative;top: 3px;">?</div>
                                    <div class="pnt"></div>
                                    <div class="showme">Select the type of the navigation arrows to be used for the website</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="has-height">
                        <div>
                            <ul id="arrows-type">
                                <li <?php if ($param_values['slideshow_navigation_type'] == 1) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.simple.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="1" <?php if ($param_values['slideshow_navigation_type'] == 1) {
    echo 'checked="checked"';
} ?>>
                                </li>
                                <li <?php if ($param_values['slideshow_navigation_type'] == 2) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.circle.shadow.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="2" <?php if ($param_values['slideshow_navigation_type'] == 2) {
    echo 'checked="checked"';
} ?>>
                                </li>
                                <li <?php if ($param_values['slideshow_navigation_type'] == 3) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.circle.simple.dark.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="3" <?php if ($param_values['slideshow_navigation_type'] == 3) {
    echo 'checked="checked"';
} ?>>
                                </li>

                                <li <?php if ($param_values['slideshow_navigation_type'] == 4) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.cube.dark.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="4" <?php if ($param_values['slideshow_navigation_type'] == 4) {
    echo 'checked="checked"';
} ?>>
                                </li>
                                <li <?php if ($param_values['slideshow_navigation_type'] == 5) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.light.blue.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="5" <?php if ($param_values['slideshow_navigation_type'] == 5) {
    echo 'checked="checked"';
} ?>>
                                </li>
                                <li <?php if ($param_values['slideshow_navigation_type'] == 6) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.light.cube.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="6" <?php if ($param_values['slideshow_navigation_type'] == 6) {
    echo 'checked="checked"';
} ?>>
                                </li>
                                <li <?php if ($param_values['slideshow_navigation_type'] == 7) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.light.transparent.circle.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="7" <?php if ($param_values['slideshow_navigation_type'] == 7) {
    echo 'checked="checked"';
} ?>>
                                </li>
                                <li <?php if ($param_values['slideshow_navigation_type'] == 8) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="8" <?php if ($param_values['slideshow_navigation_type'] == 8) {
    echo 'checked="checked"';
} ?>>
                                </li>
                                <li <?php if ($param_values['slideshow_navigation_type'] == 9) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.circle.blue.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="9" <?php if ($param_values['slideshow_navigation_type'] == 9) {
    echo 'checked="checked"';
} ?>>
                                </li>   
                                <li <?php if ($param_values['slideshow_navigation_type'] == 10) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.circle.green.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="10" <?php if ($param_values['slideshow_navigation_type'] == 10) {
    echo 'checked="checked"';
} ?>>
                                </li>
                                <li <?php if ($param_values['slideshow_navigation_type'] == 11) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.blue.retro.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="11" <?php if ($param_values['slideshow_navigation_type'] == 11) {
    echo 'checked="checked"';
} ?>>
                                </li>
                                <li <?php if ($param_values['slideshow_navigation_type'] == 12) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.green.retro.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="12" <?php if ($param_values['slideshow_navigation_type'] == 12) {
    echo 'checked="checked"';
} ?>>
                                </li>   
                                <li <?php if ($param_values['slideshow_navigation_type'] == 13) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.red.circle.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="13" <?php if ($param_values['slideshow_navigation_type'] == 13) {
    echo 'checked="checked"';
} ?>>
                                </li>   
                                <li class="color" <?php if ($param_values['slideshow_navigation_type'] == 14) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.triangle.white.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="14" <?php if ($param_values['slideshow_navigation_type'] == 14) {
    echo 'checked="checked"';
} ?>>
                                </li>   
                                <li <?php if ($param_values['slideshow_navigation_type'] == 15) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.ancient.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="15" <?php if ($param_values['slideshow_navigation_type'] == 15) {
    echo 'checked="checked"';
} ?>>
                                </li>
                                <li <?php if ($param_values['slideshow_navigation_type'] == 16) {
    echo 'class="active"';
} ?>>
                                    <div class="image-block">
                                        <img src="<?php echo $path_site; ?>/arrows/arrows.black.out.png" alt="" />
                                    </div>
                                    <input type="radio" name="params[slideshow_navigation_type]" value="16" <?php if ($param_values['slideshow_navigation_type'] == 16) {
    echo 'checked="checked"';
} ?>>
                                </li>                           
                            </ul>
                        </div>
                         
                    </div>
                    <div>
                        <input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
                    </div>
                </form>
            </div>
           
            
        </div>
    </div>
    
  
</div>