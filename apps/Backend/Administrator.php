<?php

namespace TBProductColorizerTM\Backend;

use TBProductColorizerTM\Backend\Modules\Templates\Ajax;
use TBProductColorizerTM\Backend\Modules\Templates\ColorPalettes;
use TBProductColorizerTM\Backend\Modules\Templates\FiltersAndActions;
use TBProductColorizerTM\Backend\Modules\Templates\MetaBoxes;
use TBProductColorizerTM\Backend\Modules\WooCommerce\WCProduct;
use TBProductColorizerTM\BaseDIAware;
use TBProductColorizerTM\ProductColorizer;
use WP_Post;
use WP_Screen;

/**
 * Class Administrator
 * @package TBProductColorizerTM\Backend
 */
class Administrator extends BaseDIAware
{

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $url;

    /**
     * Initialize class
     */
    public function initialize()
    {
        $this->path = plugin_dir_path(__FILE__);
        $this->url  = plugin_dir_url(__FILE__) . 'public/';

        $this->defineHooks();
    }

    /**
     * Define Hooks
     */
    private function defineHooks()
    {
        // Get loader
        $loader = $this->getDI()->get('loader');

        $loader->addAction('admin_enqueue_scripts', $this, 'enqueueElements', 999);

        new FiltersAndActions($this->url, $this->getDI());
        new MetaBoxes($this->url, $this->path, $this->getDI());
        new Ajax($this->url, $this->path, $this->getDI());
        new WCProduct($this->url, $this->path, $this->getDI());
        new ColorPalettes($this->url, $this->path, $this->getDI());
    }

    /**
     * Scripts and Styles
     */
    public function enqueueElements()
    {

        if (!$this->isIncludeAssets())
        {
            return;
        }

        wp_enqueue_style(
            ProductColorizer::SLUG . '-admin-color-picker',
            $this->url . 'vendor/colorpicker/css/colorpicker.css',
            [],
            ProductColorizer::VERSION,
            false
        );

        wp_enqueue_style(
            ProductColorizer::SLUG . '-admin',
            $this->url . 'css/core.min.css',
            ProductColorizer::VERSION
        );

        wp_enqueue_script(
            ProductColorizer::SLUG . '-admin-color-picker',
            $this->url . 'vendor/colorpicker/js/colorpicker.js',
            [],
            ProductColorizer::VERSION,
            false
        );

        wp_enqueue_media();

        wp_enqueue_script(
            ProductColorizer::SLUG . '-admin',
//            $this->url . 'js/core.min.js',
            $this->url . 'js/core.js',
            ['jquery'],
            ProductColorizer::VERSION,
            true
        );

        wp_localize_script(
            ProductColorizer::SLUG . '-admin',
            'tb_wpc',
            [
                'nonce'     => wp_create_nonce(ProductColorizer::SLUG . '-ajax_nonce'),
                'postId'    => get_the_ID(),
            ]
        );
    }

    /**
     * @return bool
     */
    private function isIncludeAssets()
    {

        /** @var WP_Post $post */
        $post = get_post();

        /** @var WP_Screen $currentScreen */
        $currentScreen = get_current_screen();

        return (
            ($post && in_array($post->post_type, [ProductColorizer::POST_TYPE, 'product', 'shop_order'], true)) ||
            ($currentScreen && 'tb_pc_svg_templates_page_tb-pc-svg-colors' === $currentScreen->id)
        );
    }
}