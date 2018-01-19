<?php
/** @var $template \TBProductColorizerTM\DTO\WCProductTemplateDTO */
/** @var int $increment */
?>
<div class="options_group" data-template="<?php echo $increment?>">
    <h4 class="options_group tb_wpc_padding">
        Template <?php echo $increment?>

        <?php if (1 < $increment): ?>
            <small><a href="#" class="tb-wpc-remove-template">remove</a></small>
        <?php endif?>
    </h4>

    <?php
    woocommerce_wp_text_input([
        'id'            => 'tb_wpc_data[templates][' . $increment . '][value]',
        'label'         => __('Template Value', $domain),
        'placeholder'   => __('Template Value', $domain),
        'description'   => __('Example: Borah_0', $domain),
        'default'       => '',
        'class'         => 'tb_wpc_form',
        'desc_tip'      => true,
        'value'         => (null !== $template) ? $template->getValue() : '',
    ]);

    woocommerce_wp_select([
        'id'            => 'tb_wpc_data[templates][' . $increment . '][templateId]',
        'label'         => __('Product Colorizer Template', $domain),
        'class'         => 'tb_wpc_form',
        'value'         => (null !== $template) ? $template->getTemplateId() : '',
        'options'       => $this->getTemplates(),
    ])
    ?>
</div>
