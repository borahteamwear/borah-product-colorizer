<?php
/** @var $image \TBProductColorizerTM\DTO\ImageDTO */
/** @var int $increment */
?>
<div class="tb-wpc-template" data-image="<?php echo $increment?>">
    <?php woocommerce_wp_hidden_input([
        'id'    => 'tb_wpc_extended[images][' . $increment . '][id]',
        'class' => 'tb_wpc_form',
        'value' => (null !== $image) ? $image->getId() : '',
    ])?>

    <div class="form-field tb_wpc_text-center tb_wpc_padding">
        <h3>
            Image: <?php echo $increment?>

            <?php if (1 < $increment): ?>
                <small><a href="#" class="tb-wpc-remove-element">remove</a></small>
            <?php endif?>
        </h3>

        <?php
        woocommerce_wp_text_input([
            'id'            => 'tb_wpc_extended[images][' . $increment . '][targetInput]',
            'label'         => __('Image Target Input', $domain),
            'placeholder'   => __('Image Target Input', $domain),
            'description'   => __('Example: #target-input > input.target-input-class', $domain),
            'default'       => '',
            'class'         => 'tb_wpc_form',
            'desc_tip'      => true,
            'value'         => (null !== $image) ? $image->getTargetInput() : ''
        ])
        ?>

        <div class="tb-wpc-text-center">
            <img id="tb_wpc_image_preview_<?php echo $increment?>" src="<?php echo (null !== $image) ? $image->getUrl(): $this->url . 'images/image-mask.png'?>" class="tb_wpc_image_preview">
        </div>

        <div class="tb-wpc-text-center">
            <button id="tb_wpc_upload_image_<?php echo $increment?>" class="button tb_wpc_upload_image_button" data-input="tb_wpc_extended[images][<?php echo $increment?>][id]" data-preview="tb_wpc_image_preview_<?php echo $increment?>" type="button">
                <?php
                _e((!isset($image)) ? 'Add Mask Image' : 'Change Mask Image', $domain)
                ?>
            </button>
        </div>
    </div>
</div>