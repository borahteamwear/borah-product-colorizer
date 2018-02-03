<?php

namespace TBProductColorizerTM\Frontend;

use TBProductColorizerTM\BaseDIAware;
use TBProductColorizerTM\DTO\DiscountDTO;
use TBProductColorizerTM\DTO\WCProductColorDTO;
use TBProductColorizerTM\DTO\WCProductDTO;
use TBProductColorizerTM\DTO\WCProductTemplateDTO;
use TBProductColorizerTM\Frontend\Modules\WooCommerce\WCButtons;
use TBProductColorizerTM\Frontend\Modules\WooCommerce\WCProduct;
use TBProductColorizerTM\Frontend\Modules\WooCommerce\WCCart;
use TBProductColorizerTM\Frontend\Service\WooCommerce\Product;
use TBProductColorizerTM\ProductColorizer;
use TBProductColorizerTM\Utils\Loader;
use WC_Product;
use WP_Post;
use WP_Term;

/**
 * Class Frontend
 * @package TBProductColorizerTM\Frontend
 */
class Frontend extends BaseDIAware
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
        /** @var Loader $loader */
        $loader = $this->getDI()->get('loader');

        $loader->addAction('wp_enqueue_scripts', $this, 'enqueueElements', 100);

        new WCProduct($this->url, $this->path, $this->getDI());
        new WCCart($this->getDI());
        new WCButtons($this->getDI());
    }

    /**
     * Add JS & CSS to the product page
     */
    public function enqueueElements()
    {
        /** @var WP_POST $page */
        $page = get_post();

        // Only on products and posts
        if ($this->isPageInvalid($page))
        {
            return;
        }

        $metaData = (new WCProductDTO(get_the_ID()))
            ->hydrate()
        ;

        if (true !== $metaData->isActive())
        {
            return;
        }

        wp_enqueue_style(
            ProductColorizer::SLUG,
            $this->url . 'css/core.min.css',
            ProductColorizer::VERSION
        );

        if ('product' !== $page->post_type)
        {
            return;
        }

        wp_enqueue_script(
            ProductColorizer::SLUG,
            $this->url . 'js/core.min.js',
            ['jquery'],
            ProductColorizer::VERSION,
            true
        );

        $product = new WC_Product(get_the_ID());

        wp_localize_script(
            ProductColorizer::SLUG,
            'tb_wpc',
            [
                'nonce'                 => wp_create_nonce(ProductColorizer::SLUG . '-ajax_nonce'),
                'productId'             => get_the_ID(),
                'productPrice'          => (float) $product->get_price(),
                'isCustomTeamProduct'   => (new Product)
                    ->setProduct($product)
                    ->isProductCustomTeams(),
                'ajaxurl'               => admin_url('admin-ajax.php'),
                'templates'             => $this->getProductTemplateData($metaData),
                'colors'                => $this->getColorData($metaData),
                'colorNames'            => $this->getColorNames($metaData),
                'colorPrices'           => $this->getColorPrices($metaData),
                'clickAbleTemplates'    => [
                    'select'    => $metaData->getProductSelectSelector(),
                    'products'  => $metaData->getProductSelector(),
                ],
                'discounts'             => $this->getDiscounts($metaData),
                // Hard-coded to get the requirements done fast so we
                'fileUploadInput'       => 'input[type="file"].tm-epo-field.tmcp-upload',
                'allowedFile'           => 'zip',
            ]
        );
    }

    /**
     * @param WP_Post|null $page
     *
     * @return bool
     */
    private function isPageInvalid(WP_Post $page = null)
    {
        return (
            null === $page ||
            !in_array($page->post_type, ['product', 'page'], true) ||
            ('page' === $page->post_type && !in_array($page->post_name, ['checkout', 'cart'], true))
        );
    }

    /**
     * @param WCProductDTO $metaData
     *
     * @return array|null
     */
    private function getProductTemplateData(WCProductDTO $metaData)
    {
        if (!$metaData->getTemplateSelector() || 1 > $metaData->getTemplates()->count())
        {
            return null;
        }

        $data = [
            'selector'  => $metaData->getTemplateSelector(),
            'values'    => [],
        ];

        /** @var WCProductTemplateDTO $template */
        foreach ($metaData->getTemplates() as $template)
        {
            $data['values'][$template->getValue()] = $template->getTemplateId();
        }

        return $data;
    }

    /**
     * @param WCProductDTO $metaData
     *
     * @return array|null
     */
    private function getColorData(WCProductDTO $metaData)
    {

        if ($metaData->getColors() && 1 > $metaData->getColors()->count())
        {
            return null;
        }

        $data = [];

        /** @var WCProductColorDTO $color */
        foreach ($metaData->getColors() as $color)
        {
            $palette = $this->getPalette($color);

            if (!$palette)
            {
                continue;
            }

            $data[] = [
                'selector'      => $color->getSelector(),
                'palette'       => $palette,
                'defaultColor'  => $color->getDefaultColor(),
            ];
        }

        return $data;
    }

    /**
     * @param WCProductColorDTO $color
     *
     * @return array|null
     */
    private function getPalette(WCProductColorDTO $color)
    {

        $palettes = get_terms([
            'hide_empty'    => false,
            'taxonomy'      => ProductColorizer::TAXONOMY_COLORS,
            'parent'        => $color->getPaletteId(),
            'meta_key'      => 'tb_wpc_color',
        ]);

        if (!is_array($palettes) || empty($palettes))
        {
            return null;
        }

        $data = [];

        /** @var WP_Term $palette */
        foreach ($palettes as $palette)
        {
            $hexCode = get_term_meta($palette->term_id, 'tb_wpc_color', true);

            if (!$hexCode)
            {
                continue;
            }

            $data[] = $hexCode;
        }

        return array_chunk($data, 4);
    }

    /**
     * @param WCProductDTO $metaData
     *
     * @return array|null
     */
    private function getColorNames(WCProductDTO $metaData)
    {

        if ($metaData->getColors() && 1 > $metaData->getColors()->count())
        {
            return null;
        }

        $data = [];

        /** @var WCProductColorDTO $color */
        foreach ($metaData->getColors() as $color)
        {
            $palette = $this->getColorName($color);

            if (!$palette)
            {
                continue;
            }

            $data = array_merge($data, $palette);
        }

        return $data;
    }

    /**
     * @param WCProductColorDTO $color
     *
     * @return array|null
     */
    private function getColorName(WCProductColorDTO $color)
    {

        $palettes = get_terms([
            'hide_empty'    => false,
            'taxonomy'      => ProductColorizer::TAXONOMY_COLORS,
            'parent'        => $color->getPaletteId(),
            'meta_key'      => 'tb_wpc_color',
        ]);

        if (!is_array($palettes) || empty($palettes))
        {
            return null;
        }

        $data = [];

        /** @var WP_Term $palette */
        foreach ($palettes as $palette)
        {
            $hexCode = get_term_meta($palette->term_id, 'tb_wpc_color', true);

            if (!$hexCode)
            {
                continue;
            }

            $data['#' . $hexCode] = $palette->name;
        }

        return $data;
    }

    /**
     * @param WCProductDTO $metaData
     *
     * @return array|null
     */
    private function getColorPrices(WCProductDTO $metaData)
    {

        if ($metaData->getColors() && 1 > $metaData->getColors()->count())
        {
            return null;
        }

        $data = [];

        /** @var WCProductColorDTO $color */
        foreach ($metaData->getColors() as $color)
        {
            $palette = $this->getColorPrice($color);

            if (!$palette)
            {
                continue;
            }

            $data = array_merge($data, $palette);
        }

        return $data;
    }

    /**
     * @param WCProductDTO $metaData
     *
     * @return array|null
     */
    private function getColorPrice(WCProductColorDTO $color)
    {

        $palettes = get_terms([
            'hide_empty'    => false,
            'taxonomy'      => ProductColorizer::TAXONOMY_COLORS,
            'parent'        => $color->getPaletteId(),
            'meta_key'      => 'tb_wpc_price',
        ]);

        if (!is_array($palettes) || empty($palettes))
        {
            return null;
        }

        $data = [];

        /** @var WP_Term $palette */
        foreach ($palettes as $palette)
        {
            $price      = get_term_meta($palette->term_id, 'tb_wpc_price', true);
            $hexCode    = get_term_meta($palette->term_id, 'tb_wpc_color', true);

            if (!$price || !$hexCode)
            {
                continue;
            }

            $data[$hexCode] = (float) $price;
        }

        return $data;
    }

    /**
     * @param WCProductDTO $metaData
     *
     * @return array
     */
    private function getDiscounts(WCProductDTO $metaData)
    {
        $discounts = [];

        /** @var DiscountDTO $discount */
        foreach ($metaData->getDiscounts() as $discount)
        {
            $discounts[] = [
                'minQuantity'   => $discount->getMinQuantity(),
                'maxQuantity'   => $discount->getMaxQuantity(),
                'price'         => $discount->getPrice(),
                'percentage'    => $discount->getPercentage(),
            ];
        }

        return $discounts;
    }
}