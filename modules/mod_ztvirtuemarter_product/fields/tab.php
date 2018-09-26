<?php

defined('JPATH_BASE') or die;
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
if (!class_exists('ShopFunctions'))
    require(VMPATH_ADMIN . DS . 'helpers' . DS . 'shopfunctions.php');
if (!class_exists('TableCategories'))
    require(VMPATH_ADMIN . DS . 'tables' . DS . 'categories.php');

jimport('joomla.form.formfield');

// The class name must always be the same as the filename (in camel case)
class JFormFieldTab extends JFormField {


    //The field class must know its own type through the variable $type.
    protected $type = 'tab';

    public function getInput() {
        VmConfig::loadConfig();
        VmConfig::loadJLang('com_virtuemart');
        $categorylist = ShopFunctions::categoryListTree($this->value);
        $html = '<select id="'.$this->id.'" name="'.$this->name.'[]" multiple>'.$categorylist.'</select>';
        $script ="
            jQuery(document).ready(function ($) {
                if( $('#jform_params_layout').val() == 'tab'){
                    $('#jform_params_product_group').parent().parent().hide();
                    $('#jform_params_product_group_tab').parent().parent().show();
                }else {
                    $('#jform_params_product_group').parent().parent().show();
                    $('#jform_params_product_group_tab').parent().parent().hide();
                }
                $('#jform_params_layout').change(function() {
                    if( $(this).val() == 'tab'){
                        $('#jform_params_product_group').parent().parent().hide();
                        $('#jform_params_product_group_tab').parent().parent().show();
                    }else {
                        $('#jform_params_product_group').parent().parent().show();
                        $('#jform_params_product_group_tab').parent().parent().hide();
                    }
                });
            });

        ";

        JFactory::getDocument()->addScriptDeclaration($script);

        return $html;
    }
}
