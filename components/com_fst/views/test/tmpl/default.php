<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<?php echo FST_Helper::PageStyle(); ?>
<?php echo FST_Helper::PageTitle("TESTIMONIALS");?>
<div class="fst_spacer"></div>
<?php $this->comments->DisplayAdd(); ?>
<div class="fst_spacer"></div>
<?php if (!empty($this->showresult)): ?>
	<div class='fst_comments_result_<?php echo $this->comments->uid; ?>'></div>
<?php endif; ?>
<div id="comments"></div>

<?php $testcount = 0; if (count($this->products) > 0) :
foreach($this->products as &$product): ?>
	<?php if (!array_key_exists("id", $product)) continue; ?>
	<?php if ($this->comments->GetCountOnly($product['id']) == 0 && FST_Settings::get('test_hide_empty_prod')) continue; ?>
	<?php include "components/com_fst/views/test/snippet/_prod.php" ?>
	<div class="fst_clear"></div>
<?php if ($this->test_show_prod_mode != "list"): ?>
	<div class='fst_test accordion_content_1'>
		<?php $testcount += $this->comments->DisplayCommentsOnly($product['id']); ?>
	</div>
<?php endif; ?>
<?php endforeach; endif; ?>

<?php if ($testcount == 0): ?>
<?php if ($this->test_show_prod_mode != "list"): ?>
	<div class=""><?php echo JText::_('THERE_ARE_NO_TESTIMONIALS_TO_DISPLAY'); ?></div>
<?php endif; ?>
<?php endif; ?>
<?php $this->comments->IncludeJS() ?>

<?php include JPATH_SITE.DS.'components'.DS.'com_fst'.DS.'_powered.php'; ?>

<?php echo FST_Helper::PageStyleEnd(); ?>
<?php $scrollf = FST_Helper::Is16() ? "start" : "scrollTo"; ?>

<?php if ($this->test_show_prod_mode == "accordian"): ?>
<script>
window.addEvent('domready', function() {
	
	if(window.ie6) var heightValue='100%';
	else var heightValue='';
	
	var togglerName='div.accordion_toggler_';
	var contentName='div.accordion_content_';
	
	var acc_elem = null;
	var acc_toggle = null;
	
	var counter=1;	
	var toggler=$$(togglerName+counter);
	var content=$$(contentName+counter);
	
	while(toggler.length>0)
	{
		// Accordion anwenden
<?php if (FSTJ3Helper::IsJ3()): ?>
		new Fx.Accordion(toggler, content, {
<?php else: ?>
		new Accordion(toggler, content, {
<?php endif; ?>
		opacity: false,
		alwaysHide: true,
		display: -1,
		onActive: function(toggler, content) {
				acc_elem = content;
				acc_toggle = toggler;
			},
			onBackground: function(toggler, content) {
			},
			onComplete: function(){
				var element=$(this.elements[this.previous]);
				if(element && element.offsetHeight>0) element.setStyle('height', heightValue);			

				if (!acc_elem)
					return;

				var  scroll =  new Fx.Scroll(window,  { 
					wait: false, 
					duration: 250, 
					transition: Fx.Transitions.Quad.easeInOut
				}); 
			
				var window_top = window.pageYOffset;
				var window_bottom = window_top + window.innerHeight;
				var elem_top = acc_toggle.getPosition().y;
				var elem_bottom = elem_top + acc_elem.offsetHeight + acc_toggle.offsetHeight;

				// is element off the top of the displayed windows??
				if (elem_top < window_top)
				{
					scroll.<?php echo $scrollf; ?>(window.pageXOffset,acc_toggle.getPosition().y);
				} else if (elem_bottom > window_bottom)
				{
					var howmuch = elem_bottom - window_bottom;
					if (elem_top - howmuch > 0)
					{
						scroll.<?php echo $scrollf; ?>(window.pageXOffset,window_top + howmuch + 22);				
					} else {
						scroll.<?php echo $scrollf; ?>(window.pageXOffset,acc_toggle.getPosition().y);
					}
				}
			}
		});
		
		counter++;
		toggler=$$(togglerName+counter);
		content=$$(contentName+counter);
	}
});
</script>
<?php endif; ?>