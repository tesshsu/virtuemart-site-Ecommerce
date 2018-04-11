 
<div class="template-options">
    <div class="options-inner">
        <a href="#" class="helix3-toggler">
            <i class="fa fa-cog fa-spin"></i>
        </a>
        <div class="option-section">
            <h4>Layout Type</h4>
            <div class="checkbox">
                <label>
                    <input id="helix3-boxed" type="checkbox" />Enable Boxed Layout</label>
            </div>
        </div>
        <div class="option-section">
            <h4>Presets Color</h4>
            <ul class="helix3-presets clearfix">
            <?php foreach($presets as $i => $preset) : ?>
                <li class="helix3-preset<?php echo $i ?> <?php echo $i == 1 ? 'active' : ''?>" data-preset="<?php echo $i ?>">
                    <a style="background-color: <?php echo $preset ?>" href="#"></a>
                </li> 
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>