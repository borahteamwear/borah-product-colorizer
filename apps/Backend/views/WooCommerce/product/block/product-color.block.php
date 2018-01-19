<?php
/** @var $color \TBProductColorizerTM\DTO\WCProductColorDTO */
/** @var int $increment */
?>
<div class="options_group" data-color="<?php echo $increment?>">
    <h4 class="options_group tb_wpc_padding">
        Color <?php echo $increment?>

        <?php if (1 < $increment): ?>
            <small><a href="#" class="tb-wpc-remove-color">remove</a></small>
        <?php endif?>
    </h4>

    <?php
    woocommerce_wp_text_input([
        'id'            => 'tb_wpc_data[colors][' . $increment . '][selector]',
        'label'         => __('Color ' . $increment . ' Selector', $domain),
        'placeholder'   => __('Color ' . $increment . '  Selector', $domain),
        'description'   => __('Example: input#color-' . $increment, $domain),
        'default'       => '',
        'class'         => 'tb_wpc_form',
        'desc_tip'      => true,
        'value'         => (null !== $color) ? $color->getSelector() : '',
    ]);

    woocommerce_wp_select([
        'id'            => 'tb_wpc_data[colors][' . $increment . '][paletteId]',
        'label'         => __('Product Colorizer Color Template', $domain),
        'class'         => 'tb_wpc_form tb_wpc_palette',
        'value'         => (null !== $color) ? $color->getPaletteId() : '',
        'options'       => $this->getColors(),
    ]);

    woocommerce_wp_select([
        'id'                    => 'tb_wpc_data[colors][' . $increment . '][defaultColor]',
        'label'                 => __('Product Colorizer Default Color', $domain),
        'class'                 => 'tb_wpc_form tb_wpc_defaultColor',
        'value'                 => (null !== $color) ? $color->getDefaultColor() : '',
        'custom_attributes'     => [
                'data-selected-color'   => $color->getDefaultColor(),
        ],
        'options'               => [],
    ])
    ?>
</div>
