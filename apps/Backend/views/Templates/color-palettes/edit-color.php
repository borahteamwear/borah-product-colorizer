<?php
$domain     = \TBProductColorizerTM\ProductColorizer::SLUG;
$postType   = \TBProductColorizerTM\ProductColorizer::POST_TYPE;
$taxonomy   = \TBProductColorizerTM\ProductColorizer::TAXONOMY_COLORS;

/** @var array $palettes  */
?>
<div class="wrap woocommerce">
    <h1>
        <?php _e('Edit Color : ' . $term->name, $domain)?>
    </h1>

    <form action="<?php echo esc_url(admin_url('admin-post.php?action=tb_wpc_edit_color'))?>" method="post">
        <?php wp_nonce_field('tb_wpc_post_nonce', 'wb_wpc_nonce')?>
        <input name="id" value="<?php echo $term->term_id?>" type="hidden">
        <input name="_wp_http_referer" value="<?php esc_url(add_query_arg(['id' => $term->parent, 'edit' => $term->term_id], 'edit.php?post_type=' . $postType . '&amp;page=' . $taxonomy))?>" type="hidden">

        <table class="form-table">
            <tbody>
                <tr class="form-field form-required">
                    <th scope="row" valign="top">
                        <label for="palette_name">Palette</label>
                    </th>
                    <td>
                        <select id="palette_name" name="parent_id">
                            <?php
                            foreach ($palettes as $palette)
                            {
                                echo '<option value ="' . $palette->term_id . '"';

                                if ($palette->id == $term->parent)
                                {
                                    echo ' selected="selected"';
                                }

                                echo '>' . $palette->name . '</option>';
                            }
                            ?>
                        </select>
                        <p class="description">Name of the palette</p>
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th scope="row" valign="top">
                        <label for="color_name">Name</label>
                    </th>
                    <td>
                        <input name="name" id="color_name" value="<?php echo $term->name?>" type="text">
                        <p class="description">Name of the palette</p>
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th scope="row" valign="top">
                        <label for="color_price">Price</label>
                    </th>
                    <td>
                        <input name="price" id="color_price" value="<?php echo get_term_meta($term->term_id, 'tb_wpc_price', true)?>" type="text">
                        <p class="description">Additional price for this color, leave empty for none!</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row" valign="top">
                        <label for="color_name">Color</label>
                    </th>
                    <td rowspan="2" class="form-field">
                        <input type="hidden" name="color" id="tb_wpc_color" value="<?php echo get_term_meta($term->term_id, 'tb_wpc_color', true)?>" />
                        <div class="tb_wpc_colorPicker tb_wpc_padding" data-target="<?php echo 'tb_wpc_color'?>"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <button type="submit" class="button-primary">
                <?php echo __('Update', $domain)?>
            </button>
        </p>
    </form>
</div>