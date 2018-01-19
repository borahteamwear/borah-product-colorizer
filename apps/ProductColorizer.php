<?php

namespace TBProductColorizerTM;

// No direct access
use TBProductColorizerTM\Backend\Administrator;
use TBProductColorizerTM\Frontend\Frontend;
use TBProductColorizerTM\Utils\Loader;

defined('ABSPATH') or die;

// Ensure autoloader & DI class is included
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Application.php';

/**
 * Class AdditionalFields
 * @package AdditionalFields
 */
final class ProductColorizer extends Application
{

    const VERSION           = '1.0.0';

    const NAME              = 'Triple Bits - Product Colorizer TM';

    const SLUG              = 'product-colorizer-tm';

    const TAXONOMY_COLORS   = 'tb-pc-svg-colors';

    const POST_TYPE         = 'tb_pc_svg_templates';

    // TODO add default options when plugin is activated
    /**
     * Method to be executed upon activation of the plugin
     */
    public function onActivation()
    {

    }

    /**
     * @return string
     */
    protected function getSlug()
    {
        return self::SLUG;
    }

    /**
     * @return void
     */
    protected function pluginDependencies()
    {
        /** @var DI $di */
        $di = $this->getDI();

        /** @var Loader $loader */
        $loader = $di->get('loader');

        $loader->addAction('init', $this, 'init');

        // Set Administrator
        if (is_admin())
        {
            new Administrator($di);

            if (defined('DOING_AJAX') && true === DOING_AJAX)
            {
                new Frontend($di);
            }
        }
        else
        {
            new Frontend($di);
        }
    }

    /**
     * Add Taxonomies for Product Colorizer
     *
     * @return void
     */
    public function init()
    {
        $this->registerTaxonomy();

        $this->registerPostType();
    }

    /**
     * Register Taxonomy for Color Palettes
     */
    private function registerTaxonomy()
    {
        if (taxonomy_exists(self::TAXONOMY_COLORS))
        {
            return;
        }

        register_taxonomy(self::TAXONOMY_COLORS, [self::POST_TYPE], [
            'hierarchical'  => true,
            'query_var'     => true,
            'rewrite'       => [
                'slug' => self::TAXONOMY_COLORS,
            ],
            'show_in_menu'  => false,
            'labels'        => [
                'name' => __('Color Palettes', self::SLUG),
            ],
        ]);
    }

    /**
     * Register Post Type for Templates
     */
    private function registerPostType()
    {
        if (post_type_exists(self::POST_TYPE))
        {
            return;
        }

        register_post_type(
            self::POST_TYPE,
            [
                'labels'    => [
                    'name'                  => __('Product Colorizer Templates', self::SLUG),
                    'singular_name'         => __('Product Colorizer Template', self::SLUG),
                    'add_new'               => __('Add New', self::SLUG),
                    'add_new_item'          => __('Add New Template', self::SLUG),
                    'edit'                  => __('Edit', self::SLUG),
                    'edit_item'             => __('Edit Template', self::SLUG),
                    'new_item'              => __('New Template', self::SLUG),
                    'view'                  => __('View', self::SLUG),
                    'view_item'             => __('View Template', self::SLUG),
                    'search_items'          => __('Search Templates', self::SLUG),
                    'not_found'             => __('No Product Colorizer Template found', self::SLUG),
                    'not_found_in_trash'    => __('No Product Colorizer Template found in Trash', self::SLUG),
                    'parent'                => __('Parent Product Colorizer Template', self::SLUG),
                ],
                'public'                => false,
                'publicly_queryable'    => false, // Remove preview
                'show_ui'               => true,
                'menu_position'         => 58,
                'rewrite'               => false,
                'supports'              => [
                    'title',
                ],
                'taxonomies'            => [],
                'menu_icon'             => 'dashicons-star-filled',
                'has_archive'           => true,
            ]
        );
    }

    /**
     * De-Activate PLugin
     */
    public function deactivate()
    {

    }
}