<?php
$domain     = \TBProductColorizerTM\ProductColorizer::SLUG;
$postType   = \TBProductColorizerTM\ProductColorizer::POST_TYPE;
$taxonomy   = \TBProductColorizerTM\ProductColorizer::TAXONOMY_COLORS;
?>
<div class="wrap woocommerce">
    <div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
    <h1>
        <?php
        sprintf(
            __('Colors for %s', $domain),
            ucfirst($term->name)
        )
        ?>
    </h1>
    <?php if (isset($_GET['error_msg']) && strlen($_GET['error_msg']) > 0):?>
        <div id="message" class="updated notice notice-error is-dismissible">
            <p>
                <?php echo urldecode($_GET['error_msg'])?>
            </p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </div>
    <?php endif;?>
    <br class="clear" />
    <div id="col-container">
        <div id="col-right">
            <div class="col-wrap">
                <table class="widefat attributes-table wp-list-table ui-sortable" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col"><?php _e('Name', $domain)?></th>
                            <th scope="col" colspan="2"><?php _e('Price', $domain)?></th>
                            <th scope="col" colspan="2"><?php _e('Hex Value', $domain)?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($colors && !empty($colors)):
                        foreach ($colors as $color):?>
                            <tr>
                                <!-- Name -->
                                <td>
                                    <strong>
                                        <a href="<?php echo 'edit.php?post_type=' . $postType . '&page=' . $taxonomy . '&id=' . $color->term_id?>">
                                            <?php echo esc_html($color->name)?>
                                        </a>
                                    </strong>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a href="<?php echo esc_url(add_query_arg(['id' => $color->parent, 'edit' => $color->term_id], 'edit.php?post_type=' . $postType . '&amp;page=' . $taxonomy))?>">
                                                <?php _e('Edit', $domain)?>
                                            </a> |
                                        </span>
                                        <span class="delete">
                                            <a class="delete" href="<?php echo esc_url(wp_nonce_url(add_query_arg('delete', $color->term_id, 'edit.php?post_type=' . $postType . '&amp;page=' . $taxonomy), 'wp_tbc_delete_palette_' . $color->term_id))?>">
                                                <?php _e('Delete', $domain)?>
                                            </a>
                                        </span>
                                    </div>
                                </td>
                                <!-- /Name -->

                                <!-- Price -->
                                <td class="attribute-terms">
                                    <?php
                                    $price = get_term_meta($color->term_id, 'tb_wpc_price', true);

                                    if ($price)
                                    {
                                        echo number_format($price, 2);
                                    }
                                    ?>
                                </td>
                                <!-- /Price -->

                                <!-- Hex Value -->
                                <td class="attribute-terms">
                                    #<?php echo get_term_meta($color->term_id, 'tb_wpc_color', true)?>
                                </td>
                                <!-- /Hex Value -->

                                <td class="attribute-actions">
                                    <a href="<?php echo esc_url(add_query_arg(['id' => $color->parent, 'edit' => $color->term_id], 'edit.php?post_type=' . $postType . '&amp;page=' . $taxonomy))?>" class="button alignright tips configure-terms" data-tip="<?php esc_attr_e('Configure terms', $domain)?>">
                                        <?php _e('Edit', $domain)?>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                    else :
                        ?>
                        <tr>
                            <td colspan="1">
                                <?php
                                _e(
                                    (0 == (int) $term->term_id) ? 'No palettes currently exist.' : 'No color currently exist for this palette',
                                    $domain
                                )
                                ?>
                            </td>
                        </tr>
                        <?php
                    endif;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="col-left">
            <div class="col-wrap">
                <div class="form-wrap">
                    <h2><?php _e('Add New Color', $domain)?></h2>
                    <p><?php _e('Add a new color to your palette and make it easier to add pre-defined colors to your products', $domain)?></p>
                    <form action="<?php echo esc_url(admin_url('admin-post.php?action=tb_wpc_add_color'))?>" method="post">
                        <?php wp_nonce_field('tb_wpc_post_nonce', 'wb_wpc_nonce')?>
                        <input type="hidden" name="parent_id" value="<?php echo $term->term_id?>" />

                        <div class="form-field">
                            <label for="palette_label">
                                <?php _e('Name', $domain)?>
                            </label>

                            <input name="name" id="palette_label" type="text" value="" />

                            <p class="description">
                                <?php _e('Name of your color', $domain)?>
                            </p>
                        </div>

                        <div class="form-field">
                            <label for="palette_price">
                                <?php _e('Price', $domain)?>
                            </label>

                            <input name="price" id="palette_price" type="text" value="" />

                            <p class="description">
                                <?php _e('Additional price for this color, leave empty for none!', $domain)?>
                            </p>
                        </div>

                        <div class="form-field">
                            <input type="hidden" name="color" id="tb_wpc_color" />
                            <div class="tb_wpc_colorPicker tb_wpc_padding" data-target="<?php echo "tb_wpc_color"?>"></div>
                        </div>

                        <p class="submit">
                            <input type="submit" name="add_new_attribute" id="submit" class="button button-primary" value="<?php esc_attr_e('Add Color', $domain)?>">
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>