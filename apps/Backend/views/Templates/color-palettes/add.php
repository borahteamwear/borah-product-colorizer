<?php
$domain     = \TBProductColorizerTM\ProductColorizer::SLUG;
$postType   = \TBProductColorizerTM\ProductColorizer::POST_TYPE;
$taxonomy   = \TBProductColorizerTM\ProductColorizer::TAXONOMY_COLORS;
?>

<div class="wrap woocommerce">
    <div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
    <h1><?php _e('Color Palettes', $domain)?></h1>

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
                            <th scope="col" colspan="2"><?php _e('Colors', $domain)?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($terms && !empty($terms)):
                        foreach ($terms as $term):?>
                            <tr>
                                <!-- Name -->
                                <td>
                                    <strong>
                                        <a href="<?php echo 'edit.php?post_type=' . $postType . '&page=' . $taxonomy . '&id=' . $term->term_id?>">
                                            <?php echo esc_html($term->name)?>
                                        </a>
                                    </strong>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a href="<?php echo esc_url(add_query_arg('edit', $term->term_id, 'edit.php?post_type=' . $postType . '&amp;page=' . $taxonomy))?>">
                                                <?php _e('Edit', $domain)?>
                                            </a> |
                                        </span>
                                        <span class="delete">
                                            <a class="delete" href="<?php echo esc_url(wp_nonce_url(add_query_arg('delete', $term->term_id, 'edit.php?post_type=' . $postType . '&amp;page=' . $taxonomy), 'wp_tbc_delete_palette_' . $term->term_id))?>">
                                                <?php _e('Delete', $domain)?>
                                            </a>
                                        </span>
                                    </div>
                                </td>
                                <!-- /Name -->

                                <!-- Count -->
                                <td class="attribute-terms">
                                    <?php
                                    $count = wp_count_terms($taxonomy, ['hide_empty' => false, 'parent' => $term->term_id]);

                                    echo (0 < (int) $count) ? $count : 0;
                                    ?>
                                </td>
                                <!-- /Count -->

                                <td class="attribute-actions">
                                    <a href="<?php echo esc_url(add_query_arg('id', $term->term_id, 'edit.php?post_type=' . $postType . '&amp;page=' . $taxonomy))?>" class="button alignright tips configure-terms" data-tip="<?php esc_attr_e('Configure terms', $domain)?>">
                                        <?php _e('Configure Colors', $domain)?>
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
                                    (0 == $parentId) ? 'No palettes currently exist.' : 'No color currently exist for this palette',
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
                    <h2><?php _e('Add New Palette', $domain)?></h2>
                    <p><?php _e('Palette allows you to select set of defined colors quickly while adding colors to your products.', $domain)?></p>
                    <form action="<?php echo esc_url(admin_url('admin-post.php?action=tb_wpc_add_palette'))?>" method="post">
                        <?php wp_nonce_field('tb_wpc_post_nonce', 'wb_wpc_nonce')?>
                        <div class="form-field">
                            <label for="palette_label">
                                <?php _e('Name', $domain)?>
                            </label>

                            <input name="name" id="palette_label" type="text" value="" />

                            <p class="description">
                                <?php _e('Name of your palette', $domain)?>
                            </p>
                        </div>

                        <p class="submit">
                            <input type="submit" name="add_new_attribute" id="submit" class="button button-primary" value="<?php esc_attr_e('Add Palette', $domain)?>">
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>