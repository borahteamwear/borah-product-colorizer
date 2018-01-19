<?php
/** @var \TBProductColorizerTM\DTO\WCProductDTO $metaData */

$domain = \TBProductColorizerTM\ProductColorizer::SLUG;
?>
<div id="product_colorizer_tab" class="panel woocommerce_options_panel">
    <div class="toolbar toolbar-top tb_wpc_padding">
        <button id="tb_wpc_save_top" type="button" class="button button-primary">
            <?php _e('Save', $domain)?>
        </button>

        <button id="tb_wpc_add_top" type="button" class="button button-secondary">
            <?php _e('Add New Template', $domain)?>
        </button>

        <button id="tb_wpc_add_color_top" type="button" class="button button-secondary">
            <?php _e('Add New Color', $domain)?>
        </button>
    </div>

    <!-- Is Active -->
    <div class="options_group">
        <?php
        woocommerce_wp_checkbox([
            'id'            => 'tb_wpc_data[isActive]',
            'label'         => __('Enable', $domain),
            'description'   => __(
                'Enable Product Colorizer ',
                $domain
            ),
            'class'         => 'tb_wpc_form',
            'value'         => ($metaData->isActive()) ? 'yes' : false,
            'default'       => '0',
            'desc_tip'      => true,
        ])
        ?>
    </div>
    <!-- /Is Active -->

    <!-- Click-able Product Select Box -->
    <div class="options_group">
        <?php
        woocommerce_wp_text_input([
            'id'            => 'tb_wpc_data[productSelectSelector]',
            'label'         => __('Product Select Box Selector', $domain),
            'placeholder'   => __('Product Select Box Selector', $domain),
            'description'   => __('Example: select#product-selector', $domain),
            'default'       => '',
            'class'         => 'tb_wpc_form',
            'desc_tip'      => true,
            'value'         => $metaData->getProductSelectSelector(),
        ])
        ?>
    </div>
    <!-- /Click-able Product Select Box -->

    <!-- Click-able Products -->
    <div class="options_group">
        <?php
        woocommerce_wp_text_input([
            'id'            => 'tb_wpc_data[productSelector]',
            'label'         => __('Product Selector', $domain),
            'placeholder'   => __('Product Selector', $domain),
            'description'   => __('Example: #products-listing container img.products', $domain),
            'default'       => '',
            'class'         => 'tb_wpc_form',
            'desc_tip'      => true,
            'value'         => $metaData->getProductSelector(),
        ])
        ?>
    </div>
    <!-- /Click-able Product Select Box -->

    <!-- Template Selector -->
    <div class="options_group">
        <?php
        woocommerce_wp_text_input([
            'id'            => 'tb_wpc_data[templateSelector]',
            'label'         => __('Template Selector', $domain),
            'placeholder'   => __('Template Selector', $domain),
            'description'   => __('Example: select.template-selector', $domain),
            'default'       => '',
            'class'         => 'tb_wpc_form',
            'desc_tip'      => true,
            'value'         => $metaData->getTemplateSelector(),
        ])
        ?>
    </div>
    <!-- /Template Selector  -->

    <!-- Colors -->
    <div class="tb_wpc_colors_container"></div>
    <!-- /Colors  -->

    <!-- Templates -->
    <div class="tb_wpc_templates_container"></div>
    <!-- /Templates  -->
</div>