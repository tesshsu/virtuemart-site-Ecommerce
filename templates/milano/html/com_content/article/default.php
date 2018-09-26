<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$tpl_params 	= JFactory::getApplication()->getTemplate(true)->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();
$info    = $params->get('info_block_position', 0);
JHtml::_('behavior.caption');
$useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
	|| $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author'));

	$post_format = $params->get('post_format', 'standard');

	$has_post_format = $tpl_params->get('show_post_format');

	if($this->print) $has_post_format = false;

?>
<article class="item item-page<?php echo $this->pageclass_sfx . ($this->item->featured) ? ' item-featured' : ''; ?>" itemscope itemtype="http://schema.org/Article">
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
	</div>
	<?php endif;

	if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) {
		echo $this->item->pagination;
	}
?>

	<?php
		if($post_format=='standard') {
			echo JLayoutHelper::render('joomla.content.full_image', $this->item);
		} else {
			echo JLayoutHelper::render('joomla.content.post_formats.post_' . $post_format, array('params' => $params, 'item' => $this->item));
		}
	?>

	<div class="entry-header<?php echo $has_post_format ? ' has-post-format': ''; ?> text-center">
		<?php echo JLayoutHelper::render('joomla.content.post_formats.icons',  $post_format); ?> 
		
		<?php if ($params->get('show_title') || $params->get('show_author')) : ?>
			<h2 itemprop="name">
				<?php if ($params->get('show_title')) : ?>
					<?php echo $this->escape($this->item->title); ?>
				<?php endif; ?>
			</h2>
			<?php if ($this->item->state == 0) : ?>
				<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
			<?php endif; ?>
			<?php if (strtotime($this->item->publish_up) > strtotime(JFactory::getDate())) : ?>
				<span class="label label-warning"><?php echo JText::_('JNOTPUBLISHEDYET'); ?></span>
			<?php endif; ?>
			<?php if ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != '0000-00-00 00:00:00') : ?>
				<span class="label label-warning"><?php echo JText::_('JEXPIRED'); ?></span>
			<?php endif; ?>
            <div class="entry-meta">
                <?php if (!$this->print && $useDefList && ($info == 1 || $info == 2)) : ?>
            		<?php echo JLayoutHelper::render('joomla.content.info_block.author', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
                    <span class="space">/</span>
                    <?php echo JLayoutHelper::render('joomla.content.info_block.publish_date', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
            	<?php  endif; ?>
            </div>
		<?php endif; ?>
	</div>

	<?php if (!$this->print) : ?>
		<?php if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
			<?php echo JLayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
		<?php endif; ?>
	<?php else : ?>
		<?php if ($useDefList) : ?>
			<div id="pop-print" class="btn hidden-print">
				<?php echo JHtml::_('icon.print_screen', $this->item, $params); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!$params->get('show_intro')) : echo $this->item->event->afterDisplayTitle; endif; ?>
	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position)))
		|| (empty($urls->urls_position) && (!$params->get('urls_position')))) : ?>
	<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>
	<?php if ($params->get('access-view')):?>

	<?php //echo JLayoutHelper::render('joomla.content.full_image', $this->item); ?>

	<?php
	if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative):
		echo $this->item->pagination;
	endif;
	?>
	<?php if (isset ($this->item->toc)) :
		echo $this->item->toc;
	endif; ?>
	<div itemprop="articleBody">
		<?php echo $this->item->text; ?>
	</div>

	<div class="share_this text-center"> 
		<ul class="social-icons">  
			<li class="st_facebook_large" displayText='Facebook' data-toggle="tooltip" data-placement="top" title="Share on Facebook"><a class="icon-facebook" href="#"><i class="fa fa-facebook-f fa-2x"></a></i></li>
            <li class="st_twitter_large" displayText='Tweet' data-toggle="tooltip" data-placement="top" title="Share on Twitter"><a class="icon-twitter" href="#"><i class="fa fa-twitter fa-2x"></a></i></li>
			<li class="st_googleplus_large"  displayText='Google +' data-toggle="tooltip" data-placement="top" title="Share on Google+"><a class="icon-google-plus" href="#"><i class="fa fa-google-plus fa-2x"></a></i></li>
			<li class="st_pinterest_large" displayText='Pinterest' data-toggle="tooltip" data-placement="top" title="Share on Pinterest"><a class="icon-pinterest" href="#"><i class="fa fa-pinterest fa-2x"></a></i></li>
			<li class="st_linkedin_large" displayText='LinkedIn' data-toggle="tooltip" data-placement="top" title="Share on Linkedin"><a class="icon-dribbble" href="#"><i class="fa fa-linkedin fa-2x"></a></i></li>
		</ul>
	</div>
    <footer class="entry-meta">
	<?php if (!$this->print && $useDefList && ($info == 1 || $info == 2)) : ?>
		<?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
	<?php  endif; ?>

	<?php if ($info == 1 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
        <span class="sep"> | </span>
		<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
		<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
	<?php endif; ?>
    </footer>

	<?php
if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative):
	echo $this->item->pagination;
?>
	<?php endif; ?>
	<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))) : ?>
	<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>
	<?php // Optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>
	<?php echo $this->item->introtext; ?>
	<?php //Optional link to let them register to see the whole article. ?>
	<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
		$link1 = JRoute::_('index.php?option=com_users&view=login');
		$link = new JUri($link1);?>
	<p class="readmore">
		<a href="<?php echo $link; ?>">
		<?php $attribs = json_decode($this->item->attribs); ?>
		<?php
		if ($attribs->alternative_readmore == null) :
			echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
		elseif ($readmore = $this->item->alternative_readmore) :
			echo $readmore;
			if ($params->get('show_readmore_title', 0) != 0) :
				echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
			endif;
		elseif ($params->get('show_readmore_title', 0) == 0) :
			echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
		else :
			echo JText::_('COM_CONTENT_READ_MORE');
			echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
		endif; ?>
		</a>
	</p>
	<?php endif; ?>
	<?php endif; ?>

	<?php
if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative) :
	echo $this->item->pagination;
?>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayContent; ?>

	<?php if(!$this->print) : ?>
		<?php echo JLayoutHelper::render('joomla.content.social_share.share', $this->item); //Helix Social Share ?>
		<?php echo JLayoutHelper::render('joomla.content.comments.comments', $this->item); //Helix Comment ?>
	<?php endif; ?>
	
</article>
<script type="text/javascript" src="https://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "0f5be4d4-f599-4a8a-a6b6-42b6c788f6a8", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
 