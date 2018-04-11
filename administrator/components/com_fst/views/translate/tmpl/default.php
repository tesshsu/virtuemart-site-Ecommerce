<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<h1>Translate Item</h1>
<p>Leave an item blank to use the default text. After saving, you will also need to save the item you are translating.</p>
<button onclick='saveTranslated()'>Save</button><button onclick='TINY.box.hide()'>Cancel</button>
<?php

foreach ($this->data as $field => $fielddata): ?>
	
	<h2>Field: <?php echo $fielddata['title']; ?></h2>
	<h3>Current: <span id="current-<?php echo $field; ?>"></span></h3>
	<table>
	<?php foreach ($this->langs as $key => $language): ?>
		<?php $language['id'] = str_replace("-", "", $language['tag']); ?>
		<tr>
			<td><?php echo $language['name']; ?></td>
			<td>
				<?php if ($fielddata['type'] == "textarea"): ?>
					<textarea id="tran-<?php echo $field; ?>-<?php echo $language['id']; ?>" name="tran-<?php echo $field; ?>-<?php echo $language['id']; ?>"></textarea>
				<?php elseif ($fielddata['type'] == "html"): ?>
					<?php
					$editor =& JFactory::getEditor("tinymce");
					echo $editor->display("tran-{$field}-{$language['id']}", "", '550', '200', '60', '20', array('pagebreak', 'readmore'));
					?>
				<?php else: ?>
					<input id="tran-<?php echo $field; ?>-<?php echo $language['id']; ?>" size="60" name="tran-<?php echo $field; ?>-<?php echo $language['id']; ?>" />
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<?php endforeach; ?>

<button onclick='saveTranslated()'>Save</button><button onclick='TINY.box.hide()'>Cancel</button>
