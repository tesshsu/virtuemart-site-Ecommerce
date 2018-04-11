<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

if (isset($this->error)) : ?>
	<div class="contact-error">
		<?php echo $this->error; ?>
	</div>
<?php endif; ?>
 
<div class="contact-form">
	<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate">
        <div class="row">
            <div class="col-md-4 no-padding-left text-left"> 
    		    <div class="control-group">
                    <label><?php echo JText::_('COM_CONTACT_CONTACT_EMAIL_NAME_LABEL'); ?></label>
    				<?php echo $this->form->getInput('contact_name'); ?>
    			</div>
    			<div class="control-group">
                    <label><?php echo JText::_('COM_CONTACT_EMAIL_LABEL'); ?></label>
    				<?php echo $this->form->getInput('contact_email'); ?>
    			</div>
    			<div class="control-group">
                    <label><?php echo JText::_('COM_CONTACT_CONTACT_MESSAGE_SUBJECT_LABEL'); ?></label>
    				<?php echo $this->form->getInput('contact_subject'); ?>
    			</div>
            </div>
    		<div class="col-md-8 text-left">
                <label><?php echo JText::_('COM_CONTACT_CONTACT_ENTER_MESSAGE_LABEL'); ?></label>
    			<?php echo $this->form->getInput('contact_message'); ?>
    		</div>
        </div>
		<?php if ($this->params->get('show_email_copy')) : ?>
			<?php echo $this->form->getInput('contact_email_copy'); ?>
		<?php endif; ?>
		<?php // Dynamically load any additional fields from plugins. ?>
		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
			<?php if ($fieldset->name != 'contact') : ?>
				<?php $fields = $this->form->getFieldset($fieldset->name); ?>
				<?php foreach ($fields as $field) : ?>
					<div class="control-group">
						<?php if ($field->hidden) : ?> 
								<?php echo $field->input; ?> 
						<?php else: ?> 
						 <?php echo $field->input; ?> 
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<div class="control-group">
			<button class="btn btn-primary" type="submit"><?php echo JText::_('COM_CONTACT_CONTACT_SEND'); ?></button>
			<input type="hidden" name="option" value="com_contact" />
			<input type="hidden" name="task" value="contact.submit" />
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div> 
	</form>
</div>
