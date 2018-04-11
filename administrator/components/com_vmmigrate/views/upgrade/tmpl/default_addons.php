<?php
/*------------------------------------------------------------------------
# vm_migrate - Virtuemart 2 Migrator
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die;

if ( is_array($this->extensionsFeed) && count($this->extensionsFeed) ) {
	?>
	<fieldset class="adminform">
		<legend><?php echo JText::_('VMMIGRATE_EXTENSION_ADDONS')?></legend>
        <div id="cpanel" class="adminform">
	<?php
	$j=0;
	foreach ($this->extensionsFeed as $item){
		// This is directly related to extensions.virtuemart.net
		$image="";
		if (!empty($item->link)) {
			$description = $item->description;
			preg_match('/<img[^>]+>/i',$description, $result);
			if (is_array($result) and isset($result[0])){
				$image=$result[0];
				$description=str_replace($image,"",$description);
				$description=strip_tags($description);
				$description=str_replace(JText::_ ('COM_VIRTUEMART_FEED_READMORE') ,"",$description);
			} else {
				$description="";
			}
			?>
            <div class="icon-wrapper">
                <div class="icon migratoricon48" >
                    <a href="<?php echo $item->link; ?>" target="_blank" title="<?php echo $description ?>">
                        <?php
                        if ($image){
							$image = preg_replace('/style=".*"/i','',$image);
							//$image = str_replace('left','none',$image);
                            echo  $image ;
                        }
                        echo '<span>'.$item->title.'</span>';
                        ?>
                    </a>
                </div>
            </div>
		<?php
		}
		$j++;
	} ?>
    	</div>
	</fieldset>
<?php
}
?>
