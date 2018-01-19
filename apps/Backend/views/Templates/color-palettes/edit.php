<?php
$domain     = \TBProductColorizerTM\ProductColorizer::SLUG;
$postType   = \TBProductColorizerTM\ProductColorizer::POST_TYPE;
$taxonomy   = \TBProductColorizerTM\ProductColorizer::TAXONOMY_COLORS;
?>
<div class="wrap woocommerce">
    <h1>
        <?php _e('Edit Palette : ' . $term->name, $domain)?>
    </h1>

    <form action="<?php echo esc_url(admin_url('admin-post.php?action=tb_wpc_edit_palette'))?>" method="post">
        <?php wp_nonce_field('tb_wpc_post_nonce', 'wb_wpc_nonce')?>
        <input name="id" value="<?php echo $term->term_id?>" type="hidden">
        <input name="_wp_http_referer" value="<?php echo esc_url(add_query_arg('edit', $term->term_id, 'edit.php?post_type=' . $postType . '&amp;page=' . $taxonomy))?>" type="hidden">

        <table class="form-table">
            <tbody>
            <tr class="form-field form-required">
                <th scope="row" valign="top">
                    <label for="palette_name">Name</label>
                </th>
                <td>
                    <input name="name" id="palette_name" value="<?php echo $term->name?>" type="text">
                    <p class="description">Name of the palette</p>
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