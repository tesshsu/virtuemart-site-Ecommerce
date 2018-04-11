<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


if (!empty($this->product->images)) :
    $image = $this->product->images[0];
    ?>
    <div class="main-image">
        <?php echo $image->displayMediaThumb('id="image-zoom-product" data-zoom-image="'.JUri::root().$image->file_url.'"' ,FALSE) ?>
<!--        <img id="image-zoom-product" src="--><?php //echo JUri::root().($image->file_url_thumb?$image->file_url_thumb:$image->file_url); ?><!--" data-zoom-image="--><?php //echo JUri::root().$image->file_url?><!--"/>-->
    </div>
<?php endif; ?>

