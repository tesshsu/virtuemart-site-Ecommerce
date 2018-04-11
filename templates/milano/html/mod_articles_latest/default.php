<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
?>
<div class="latestnews<?php echo $moduleclass_sfx; ?> recent-content">
<?php foreach ($list as $item) :  ?> 
	<div class="item" itemscope itemtype="http://schema.org/Article">
        <div class="media-body">
            <h4 class="media-heading">
            <a href="<?php echo $item->link; ?>" itemprop="url">
    			<span itemprop="name">
    				<?php echo $item->title; ?>
    			</span>
    		</a>
            </h4>
    		<p><?php
                $wsdescription=explode(' ',$item->introtext,20);
                array_pop($wsdescription);
                $wsdescription_string=implode(' ',$wsdescription); 
                echo $wsdescription_string; ?>
            </p> 
    	</div>
    </div>
<?php endforeach; ?>
</div>
