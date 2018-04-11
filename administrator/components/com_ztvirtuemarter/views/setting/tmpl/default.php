<?php
/**
 * Zt Virtuemarter
 *
 * @package     Joomla
 * @subpackage  Component
 * @version     1.0.0
 * @author      ZooTemplate
 * @email       support@zootemplate.com
 * @link        http://www.zootemplate.com
 * @copyright   Copyright (c) 2015 ZooTemplate
 * @license     GPL v2
 */

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
$input = JFactory::getApplication()->input;
$settings = json_decode($this->item->setting);
?>
<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
    Joomla.submitbutton = function (task) {
        if (task == 'setting.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
            Joomla.submitform(task, document.getElementById('item-form'));
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_ztvirtuemarter') ?>" method="post" name="adminForm"
      id="item-form" class="form-validate"">

<div class="row-fluid">
    <div class="span10 form-horizontal">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#general" data-toggle="tab">Details</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="general">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('enable_wishlist'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('enable_wishlist', '', $settings->enable_wishlist); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('enable_compare'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('enable_compare', '', $settings->enable_compare); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('enable_quickview'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('enable_quickview', '', $settings->enable_quickview); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('enable_countdown'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('enable_countdown', '', $settings->enable_countdown); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('enable_photozoom'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('enable_photozoom', '', $settings->enable_photozoom); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('enable_auto_insert'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('enable_auto_insert', '', $settings->enable_auto_insert); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div>
    <input type="hidden" name="task" value="<?php echo $input->get('task'); ?>"/>
    <input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</div>
</form>