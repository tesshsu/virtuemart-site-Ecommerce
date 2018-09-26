<?php
/**
 * @version    2.7.x
 * @package    K2
 * @author     JoomlaWorks http://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

// Define default image size (do not change)
K2HelperUtilities::setDefaultImage($this->item, 'itemlist', $this->params);

?>

<!-- Start K2 Item Layout -->
<article class="item col-xs-12 col-sm-6">
<div class="row">
    <div class="col-xxs-12 col-xs-6 col-sm-6 columns"> 
        <div class="seam_icon align_right">
    		<span class="seam seam-up"></span>
    		<span class="seam seam-down"></span>
    	</div>
        <div class="entry-image intro-image">
    	  <?php if($this->item->params->get('catItemImage') && !empty($this->item->image)): ?>
    	  <!-- Item Image -->
    	  <div class="catItemImageBlock">
    		  <span class="catItemImage">
    		    <a href="<?php echo $this->item->link; ?>" title="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>">
    		    	<img src="<?php echo $this->item->image; ?>" alt="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>" style="width:<?php echo $this->item->imageWidth; ?>px; height:auto;" />
    		    </a>
    		  </span>
    		  <div class="clr"></div>
    	  </div>
    	  <?php endif; ?>
          
          <?php if($this->item->params->get('catItemVideo') && !empty($this->item->video)): ?>
          
        	<div class="clr"></div>
    
          <!-- Item video -->
          <div class="catItemVideoBlock">
          	<h3><?php echo JText::_('K2_RELATED_VIDEO'); ?></h3>
        		<?php if($this->item->videoType=='embedded'): ?>
        		<div class="catItemVideoEmbedded">
        			<?php echo $this->item->video; ?>
        		</div>
        		<?php else: ?>
        		<span class="catItemVideo"><?php echo $this->item->video; ?></span>
        		<?php endif; ?>
          </div>
          <?php endif; ?>
      </div>
    </div>
	
	<div class="col-xxs-12 col-xs-6 col-sm-6 columns">
        <div class="blog_info align_left">  
        
        
        	<div class="post-date">
        		<?php if($this->item->params->get('catItemDateCreated')): ?>
        		<!-- Date created -->
        		<dd class="published"> 
                     <time datetime="<?php echo JHTML::_('date', $this->item->created, 'c'); ?>" itemprop="datePublished">
                		<span class="month"><?php echo JHTML::_('date', $this->item->created, 'F'); ?></span>
                		<span class="day"><?php echo JHTML::_('date', $this->item->created, 'd'); ?></span>
                	</time>
        		</dd>
        		<?php endif; ?>
          </div>
        
        	<!-- Plugins: BeforeDisplay -->
        	<?php echo $this->item->event->BeforeDisplay; ?>
        
        	<!-- K2 Plugins: K2BeforeDisplay -->
        	<?php echo $this->item->event->K2BeforeDisplay; ?> 
        
        	  <?php if($this->item->params->get('catItemTitle')): ?>
        	  <!-- Item title -->
        	  <div class="entry-header">
        			<?php if(isset($this->item->editLink)): ?>
        			<!-- Item edit link -->
        			<span class="catItemEditLink">
        				<a data-k2-modal="edit" href="<?php echo $this->item->editLink; ?>">
        					<?php echo JText::_('K2_EDIT_ITEM'); ?>
        				</a>
        			</span>
        			<?php endif; ?>
                <h2 class="title-article">
            	  	<?php if ($this->item->params->get('catItemTitleLinked')): ?>
            			<a href="<?php echo $this->item->link; ?>">
            	  		<?php echo $this->item->title; ?>
            	  	</a>
            	  	<?php else: ?>
            	  	<?php echo $this->item->title; ?>
            	  	<?php endif; ?>
                </h2>
        
        	  	<?php if($this->item->params->get('catItemFeaturedNotice') && $this->item->featured): ?>
        	  	<!-- Featured flag -->
        	  	<span>
        		  	<sup>
        		  		<?php echo JText::_('K2_FEATURED'); ?>
        		  	</sup>
        	  	</span>
        	  	<?php endif; ?>
        	  </div>
        	  <?php endif; ?>
        
          <!-- Plugins: AfterDisplayTitle -->
          <?php echo $this->item->event->AfterDisplayTitle; ?>
        
          <!-- K2 Plugins: K2AfterDisplayTitle -->
          <?php echo $this->item->event->K2AfterDisplayTitle; ?>
        
        	<?php if($this->item->params->get('catItemRating')): ?>
        	<!-- Item Rating -->
        	<div class="catItemRatingBlock">
        		<span><?php echo JText::_('K2_RATE_THIS_ITEM'); ?></span>
        		<div class="itemRatingForm">
        			<ul class="itemRatingList">
        				<li class="itemCurrentRating" id="itemCurrentRating<?php echo $this->item->id; ?>" style="width:<?php echo $this->item->votingPercentage; ?>%;"></li>
        				<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_1_STAR_OUT_OF_5'); ?>" class="one-star">1</a></li>
        				<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_2_STARS_OUT_OF_5'); ?>" class="two-stars">2</a></li>
        				<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_3_STARS_OUT_OF_5'); ?>" class="three-stars">3</a></li>
        				<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_4_STARS_OUT_OF_5'); ?>" class="four-stars">4</a></li>
        				<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_5_STARS_OUT_OF_5'); ?>" class="five-stars">5</a></li>
        			</ul>
        			<div id="itemRatingLog<?php echo $this->item->id; ?>" class="itemRatingLog"><?php echo $this->item->numOfvotes; ?></div>
        			<div class="clr"></div>
        		</div>
        		<div class="clr"></div>
        	</div>
        	<?php endif; ?>
            
    
    		<?php if($this->item->params->get('catItemAuthor')): ?>
    		<!-- Item Author -->
    		<span class="catItemAuthor">
    			<?php echo K2HelperUtilities::writtenBy($this->item->author->profile->gender); ?>
    			<?php if(isset($this->item->author->link) && $this->item->author->link): ?>
    			<a rel="author" href="<?php echo $this->item->author->link; ?>"><?php echo $this->item->author->name; ?></a>
    			<?php else: ?>
    			<?php echo $this->item->author->name; ?>
    			<?php endif; ?>
    		</span>
    		<?php endif; ?>
        
          
        
        
          <?php if($this->item->params->get('catItemImageGallery') && !empty($this->item->gallery)): ?>
          <!-- Item image gallery -->
          <div class="catItemImageGallery">
        	  <h4><?php echo JText::_('K2_IMAGE_GALLERY'); ?></h4>
        	  <?php echo $this->item->gallery; ?>
          </div>
          <?php endif; ?>
        
          <div class="clr"></div>
         
        
        	<?php if($this->item->params->get('catItemDateModified')): ?>
        	<!-- Item date modified -->
        	<?php if($this->item->modified != $this->nullDate && $this->item->modified != $this->item->created ): ?>
        	<span class="catItemDateModified">
        		<?php echo JText::_('K2_LAST_MODIFIED_ON'); ?> <?php echo JHTML::_('date', $this->item->modified, JText::_('K2_DATE_FORMAT_LC2')); ?>
        	</span>
        	<?php endif; ?>
        	<?php endif; ?>
        
          <!-- Plugins: AfterDisplay -->
          <?php echo $this->item->event->AfterDisplay; ?>
        
          <!-- K2 Plugins: K2AfterDisplay -->
          <?php echo $this->item->event->K2AfterDisplay; ?>
        
        </div>
    </div>

	</div>
</article>
<!-- End K2 Item Layout -->
