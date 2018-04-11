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
?>
<div class="additional-images" id="gallery_image-zoom-product">
    <?php
    $start_image = VmConfig::get('add_img_main', 0) ? 0 : 1;

    for ($i = $start_image - 1; $i < count($this->product->images); $i++) :
        $image = $this->product->images[$i];
        ?>
        <a href="#" class="elevatezoom-gallery" data-image="<?php echo JUri::root().($image->file_url_thumb?$image->file_url_thumb:$image->file_url) ?>" data-zoom-image="<?php echo JUri::root().$image->file_url?>">
            <?php echo $image->displayMediaThumb('id="image-zoom-product_'.$i.'"',FALSE) ?>
<!--            <img id="image-zoom-product_--><?php //echo  $i; ?><!--" src="--><?php //echo JUri::root(). ($image->file_url_thumb?$image->file_url_thumb:$image->file_url) ?><!--"/>-->
        </a>
        <?php
    endfor;
    ?>
</div>
