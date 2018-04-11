<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<div class="contact-info">
	<?php if ($this->contact->misc && $this->params->get('show_misc')) : ?> 
		<h2 class="section-title">
					<?php echo $this->contact->misc; ?> 
		</h2>  
	<?php endif; ?>
<div class="media-list" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
	<?php if (($this->params->get('address_check') > 0) &&
		($this->contact->address || $this->contact->suburb  || $this->contact->state || $this->contact->country || $this->contact->postcode)) : ?>
        
        <div class="media">
            <div class="media-body"> 
        		<?php if ($this->contact->address && $this->params->get('show_street_address')) : ?>
                <?php echo '<strong>' . JText::_('COM_CONTACT_ADDRESS') . '</strong>';  ?>
        			<p>
        					<?php echo nl2br($this->contact->address); ?>
       				</p>
        		<?php endif; ?>
            </div>
        </div>
        
        <div class="media">
            <div class="media-body"> 
        		<?php if ($this->contact->telephone && $this->params->get('show_telephone') || $this->contact->mobile && $this->params->get('show_mobile')) : ?>
                <?php echo '<strong>' . JText::_('COM_CONTACT_TELEPHONE') . '</strong>';  ?>
        			<ul>
                    <li>
        					<?php echo nl2br($this->contact->telephone); ?>
       				</li>
                    <li>
        					<?php echo nl2br($this->contact->mobile); ?>
       				</li>
                    </ul>
        		<?php endif; ?>
            </div>
        </div> 
 
        <div class="media">
            <div class="media-body"> 
        		<?php if ($this->contact->email_to && $this->params->get('show_email')) : ?>
                <?php echo '<strong>' . JText::_('COM_CONTACT_EMAIL_LABEL') . '</strong>';  ?>
        			<p>
        					<?php echo $this->contact->email_to; ?>
       				</p> 
        		<?php endif; ?>
            </div>
        </div> 
        
 
		<?php if ($this->contact->suburb && $this->params->get('show_suburb')) : ?>
			<dd>
				<span class="contact-suburb" itemprop="addressLocality">
					<?php echo $this->contact->suburb . '<br />'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->contact->state && $this->params->get('show_state')) : ?>
			<dd>
				<span class="contact-state" itemprop="addressRegion">
					<?php echo $this->contact->state . '<br />'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->contact->postcode && $this->params->get('show_postcode')) : ?>
			<dd>
				<span class="contact-postcode" itemprop="postalCode">
					<?php echo $this->contact->postcode . '<br />'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->contact->country && $this->params->get('show_country')) : ?>
		<dd>
			<span class="contact-country" itemprop="addressCountry">
				<?php echo $this->contact->country . '<br />'; ?>
			</span>
		</dd>
		<?php endif; ?>
	<?php endif; ?>

<?php if ($this->contact->fax && $this->params->get('show_fax')) : ?>
	<dt>
		<span class="<?php echo $this->params->get('marker_class'); ?>">
			<?php echo $this->params->get('marker_fax'); ?>
		</span>
	</dt>
	<dd>
		<span class="contact-fax" itemprop="faxNumber">
		<?php echo nl2br($this->contact->fax); ?>
		</span>
	</dd>
<?php endif; ?>
<?php if ($this->contact->webpage && $this->params->get('show_webpage')) : ?>
	<dt>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
		</span>
	</dt>
	<dd>
		<span class="contact-webpage">
			<a href="<?php echo $this->contact->webpage; ?>" target="_blank" itemprop="url">
			<?php echo JStringPunycode::urlToUTF8($this->contact->webpage); ?></a>
		</span>
	</dd>
<?php endif; ?>
</div>
</div>