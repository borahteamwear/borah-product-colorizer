<?php

namespace TBProductColorizerTM\Backend\Modules\Templates;

use TBProductColorizerTM\DI;
use TBProductColorizerTM\ProductColorizer;
use TBProductColorizerTM\Utils\Loader;
use WP_Error;

/**
 * Class ColorPalettes
 * @package TBProductColorizerTM\Backend\Modules\Templates
 */
class ColorPalettes
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $path;

    /**
     * @var DI
     */
    private $di;

    /**
     * Filters constructor.
     * @param $url
     * @param $path
     * @param DI $di
     */
    public function __construct($url, $path, DI $di)
    {
        $ds         = DIRECTORY_SEPARATOR;

        $path       = $path . 'views' . $ds . 'Templates' . $ds .'color-palettes' . $ds;

        $this->url  = $url;
        $this->path = $path;
        $this->di   = $di;

        $this->defineHooks();
    }

    /**
     * Hooks
     */
    public function defineHooks()
    {
        /** @var Loader $loader */
        $loader = $this->di->get('loader');

        $loader->addAction('admin_menu', $this, 'registerToProductColorizerMenu', 99);
        $loader->addAction('admin_post_tb_wpc_add_palette', $this, 'processPaletteForm');
        $loader->addAction('admin_post_tb_wpc_edit_palette', $this, 'processPaletteForm');
        $loader->addAction('admin_post_tb_wpc_add_color', $this, 'processPaletteForm');
        $loader->addAction('admin_post_tb_wpc_edit_color', $this, 'processPaletteForm');
    }

    /**
     *
     */
    public function registerToProductColorizerMenu()
    {
        add_submenu_page(
            'edit.php?post_type=' . ProductColorizer::POST_TYPE,
            'Color Palettes',
            'Color Palettes',
            'manage_woocommerce',
            ProductColorizer::TAXONOMY_COLORS,
            [$this, 'productColorsPage']
        );
    }

    /**
     *
     */
    public function productColorsPage()
    {
        $this->deleteTerm();

        return (isset($_GET['id'])) ? $this->productColorsPaletteColors() : $this->productColorsPalettes();
    }

    /**
     *
     */
    private function productColorsPalettes()
    {
        // Edit
        if (isset($_GET['edit']) && 0 < (int) $_GET['edit'])
        {
            $term = get_term((int) $_GET['edit'], ProductColorizer::TAXONOMY_COLORS);

            require_once($this->path . 'edit.php');

            return;
        }

        $terms = get_terms([
            'hide_empty'    => false,
            'taxonomy'      => ProductColorizer::TAXONOMY_COLORS,
            'parent'        => 0
        ]);

        require_once($this->path . 'add.php');
    }

    /**
     *
     */
    private function productColorsPaletteColors()
    {
        // Edit
        if (isset($_GET['edit']) && 0 < (int) $_GET['edit'])
        {
            $term = get_term((int) $_GET['edit'], ProductColorizer::TAXONOMY_COLORS);

            $palettes = get_terms([
                'hide_empty'    => false,
                'taxonomy'      => ProductColorizer::TAXONOMY_COLORS,
                'parent'        => 0
            ]);

            require_once($this->path . 'edit-color.php');

            return;
        }

        $term = false;

        if (0 < (int) $_GET['id'])
        {
            $term = get_term((int) $_GET['id'], ProductColorizer::TAXONOMY_COLORS);
        }

        $colors = get_terms([
            'hide_empty'    => false,
            'taxonomy'      => ProductColorizer::TAXONOMY_COLORS,
            'parent'        => $term->term_id,
            'meta_key'      => 'tb_wpc_color',
        ]);

        require_once($this->path . 'add-color.php');
    }

    /**
     *
     */
    public function messageInvalidPalette()
    {
        require_once($this->path . 'messages/invalid-palette.php');
    }

    /**
     *
     */
    public function messageFailedPalette()
    {
        require_once($this->path . 'messages/failed-palette.php');
    }

    /**
     * @return array
     */
    public function getPaletteData()
    {
        return [
            'id'        => (isset($_POST['id']) && 0 < (int) $_POST['id']) ? (int) $_POST['id'] : 0,
            'parent_id' => (isset($_POST['parent_id']) && 0 < (int) $_POST['parent_id']) ? (int) $_POST['parent_id'] : 0,
            'name'      => (isset($_POST['name'])) ? $_POST['name'] : null,
            'price'     => (isset($_POST['price']) && 0 < (float) $_POST['price']) ? (float) $_POST['price'] : null,
            'color'     => (isset($_POST['color'])) ? $_POST['color'] : null
        ];
    }

    /**
     * @return bool
     */
    public function processPaletteForm()
    {
        // Verify nonce
        check_admin_referer('tb_wpc_post_nonce', 'wb_wpc_nonce');

        // Get data
        $data = $this->getPaletteData();

        if (1 > strlen($data['name']))
        {
            return add_action('admin_notices', [$this, 'messageInvalidPalette']);
        }

        // Add / Update Palette Term
        $term = $this->updatePalette($data);

        if ($term instanceof WP_Error)
        {
            $msg = implode('<br>', $term->get_error_messages());
            $ref = wp_get_referer() . '&error_msg=' . urlencode($msg);

            wp_safe_redirect($ref);
            return false;
        }

        // Failed to Add / Update Term
        if (!isset($term['term_id']) || 0 == (int) $term['term_id'])
        {
            return add_action('admin_notices', [$this, 'messageFailedPalette']);
        }

        // Term has parent
        if (0 < (int) $data['parent_id'])
        {
            update_term_meta($term['term_id'], 'tb_wpc_color', $data['color']);
            update_term_meta($term['term_id'], 'tb_wpc_price', $data['price']);
        }

        wp_safe_redirect(wp_get_referer());

        return true;
    }

    /**
     * @param $data
     * @return array|WP_Error
     */
    private function updatePalette($data)
    {
        $result = (1 < $data['id']) ? $this->updatePaletteTerm($data) : $this->insertPaletteTerm($data);

        return $result;
    }

    /**
     * @param $data
     * @return array|WP_Error
     */
    private function updatePaletteTerm($data)
    {
        return wp_update_term(
            $data['id'],
            ProductColorizer::TAXONOMY_COLORS,
            [
                'name'      => $data['name'],
                'parent'    => $data['parent_id']
            ]
        );
    }

    /**
     * @param $data
     * @return array|WP_Error
     */
    private function insertPaletteTerm($data)
    {
        return wp_insert_term(
            $data['name'],
            ProductColorizer::TAXONOMY_COLORS,
            [
                'parent' => $data['parent_id']
            ]
        );
    }

    /**
     * Delete term
     * @return bool
     */
    private function deleteTerm()
    {
        $termId =  (isset($_GET['delete'])) ? (int) $_GET['delete'] : 0;

        if ($this->isInvalidDeleteRequest($termId))
        {
            add_action('admin_notices', [$this, 'messageInvalidPalette']);
            return false;
        }

        return wp_delete_term($termId, ProductColorizer::TAXONOMY_COLORS);
    }

    /**
     * @param int $termId
     * @return bool
     */
    private function isInvalidDeleteRequest($termId)
    {
        return (
            1 > $termId ||
            !isset($_GET['_wpnonce']) ||
            !wp_verify_nonce($_GET['_wpnonce'], 'wp_tbc_delete_palette_' . $termId) ||
            !term_exists($termId, ProductColorizer::TAXONOMY_COLORS)
        );
    }

    /**
     *
     */
    public function messageInvalidDeleteRequest()
    {
        require_once($this->path . 'messages/invalid-delete-request.php');
    }

    /**
     * Ajax: Get Palette Colors
     */
    public function ajaxGetPaletteColors()
    {
        check_ajax_referer('tb_wpc_ajax_nonce', 'nonce');

        $paletteId = (isset($_POST['id'])) ? (int) $_POST['id'] : 0;

        if (1 > $paletteId || !term_exists($paletteId, ProductColorizer::TAXONOMY_COLORS))
        {
            return wp_send_json(false);
        }

        $colors     = [];

        $tempColors = get_terms([
            'hide_empty'    => false,
            'taxonomy'      => ProductColorizer::TAXONOMY_COLORS,
            'parent'        => $paletteId,
            'meta_key'      => 'tb_wpc_color',
        ]);

        foreach ($tempColors as $color)
        {
            $colors[] = [
                'name' => $color->name,
                'color'=> get_term_meta($color->term_id, 'tb_wpc_color', true),
                'price'=> get_term_meta($color->term_id, 'tb_wpc_price', true),
            ];
        }

        unset($tempColors);

        wp_send_json((empty($colors)) ? false : $colors);
    }
}