<?php

namespace TBProductColorizerTM\Backend\Modules\WooCommerce\Ajax;

use TBProductColorizerTM\DI;
use TBProductColorizerTM\DTO\Collection\WCProductColorCollection;
use TBProductColorizerTM\DTO\Collection\WCProductTemplateCollection;
use TBProductColorizerTM\DTO\WCProductColorDTO;
use TBProductColorizerTM\DTO\WCProductDTO;
use TBProductColorizerTM\DTO\WCProductTemplateDTO;
use TBProductColorizerTM\Exception\ViewFileNotFoundException;
use TBProductColorizerTM\ProductColorizer;
use TBProductColorizerTM\Utils\Loader;
use WP_Post;
use WP_Query;
use WP_Term;

/**
 * Class WCPaletteColors
 * @package TBProductColorizerTM\Backend\Modules\WooCommerce\Ajax
 */
class WCPaletteColors
{

    /**
     * @var DI
     */
    private $di;

    /**
     * WCProductAjax constructor.
     * @param string $url
     * @param DI $di
     */
    public function __construct(DI $di)
    {
        $this->di   = $di;

        $this->defineHooks();
    }

    /**
     * Actions & Filters
     */
    private function defineHooks()
    {
        /** @var Loader $loader */
        $loader = $this->di->get('loader');

        $loader->addAction('wp_ajax_tb_wpc_palette_colors', $this, 'getColors');
    }

    /**
     * Validate Ajax Requests
     */
    private function validate()
    {
        check_ajax_referer(ProductColorizer::SLUG . '-ajax_nonce', 'nonce');
    }

    /**
     * Get colors for given palette
     */
    public function getColors()
    {
        $this->validate();

        $id     = (int) $_POST['paletteId'];

        parse_str($_POST['data'], $data);

        $terms = get_terms([
            'hide_empty'    => false,
            'taxonomy'      => ProductColorizer::TAXONOMY_COLORS,
            'parent'        => $id,
            'meta_key'      => 'tb_wpc_color',
        ]);

        if (!is_array($terms) || empty($terms))
        {
            return wp_send_json(false);
        }

        $colors = [];

        foreach ($terms as $term)
        {
            $hexCode = get_term_meta($term->term_id, 'tb_wpc_color', true);

            if (!$hexCode)
            {
                continue;
            }

            $colors[$hexCode] = $term->name;
        }

        wp_send_json($colors);
    }
}