<?php
/** @var \TBProductColorizerTM\DTO\WCProductDTO $metaData */
$domain = \TBProductColorizerTM\ProductColorizer::SLUG;
?>

<div id="tb_wpc_discounts_tab" class="panel woocommerce_options_panel">
    <div class="toolbar toolbar-top tb_wpc_padding">
        <button id="tb_wpc_save_top" type="button" class="button button-primary">
            <?php _e('Save', $domain)?>
        </button>

        <button id="tb_wpc_add_top" type="button" class="button button-secondary">
            <?php _e('Add New Discount', $domain)?>
        </button>
    </div>

    <!-- Discounts -->
    <div class="tb_wpc_discounts_container"></div>
    <!-- /Discounts  -->
</div>