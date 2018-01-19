<?php

namespace TBProductColorizerTM\Backend\Modules\WooCommerce;

use TBProductColorizerTM\Backend\Modules\WooCommerce\Ajax\WCPaletteColors;
use TBProductColorizerTM\Backend\Modules\WooCommerce\Ajax\WCProductAjax;
use TBProductColorizerTM\Backend\Modules\WooCommerce\Ajax\WCProductDiscountAjax;
use TBProductColorizerTM\DI;
use TBProductColorizerTM\DTO\WCProductDTO;
use TBProductColorizerTM\Exception\ViewFileNotFoundException;
use TBProductColorizerTM\ProductColorizer;
use TBProductColorizerTM\Utils\Loader;

/**
 * Class WCProduct
 * @package TBProductColorizerTM\Backend\Modules\WooCommerce
 */
class WCProduct
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
     * @var DI
     */
    private $di;

    /**
     * WCProduct constructor.
     * @param string $url
     * @param string $path
     * @param DI $di
     */
    public function __construct($url, $path, DI $di)
    {
        $ds         = DIRECTORY_SEPARATOR;

        $this->path = $path . 'views' . $ds . 'WooCommerce' . $ds . 'product' . $ds;
        $this->url  = $url;
        $this->di   = $di;

        $this->defineHooks();
    }

    /**
     * Actions & Filters
     */
    public function defineHooks()
    {
        /** @var Loader $loader */
        $loader = $this->di->get('loader');

        $loader->addFilter('woocommerce_product_data_tabs', $this, 'tab', 100, 1);
        $loader->addAction('woocommerce_product_data_panels', $this, 'productColorizer', 100);
        $loader->addAction('woocommerce_product_data_panels', $this, 'discounts', 100);

        (new WCProductAjax($this->url, $this->path, $this->di));
        (new WCProductDiscountAjax($this->url, $this->path, $this->di));
        (new WCPaletteColors($this->di));
    }

    /**
     * Add a new tab to the product data tabs of WooCommerce
     *
     * @param array $tabs
     *
     * @return array
     */
    public function tab(array $tabs)
    {
        $tabs['additionalFields'] = [
            'label'     => __('Product Colorizer', ProductColorizer::SLUG),
            'target'    => 'product_colorizer_tab',
            'class'     => 'productColorizer',
        ];

        $tabs['tb_wc_discounts'] = [
            'label'     => __('Discounts', ProductColorizer::SLUG),
            'target'    => 'tb_wpc_discounts_tab',
            'class'     => 'tb_wpc_discounts',
        ];

        return $tabs;
    }

    /**
     * @throws ViewFileNotFoundException
     */
    public function productColorizer()
    {
        $id = get_the_ID();

        if (!$id)
        {
            return;
        }

        $metaData   = (new WCProductDTO($id))->hydrate();

        $file = $this->path . 'product-colorizer-tab.php';

        if (!is_file($file))
        {
            throw new ViewFileNotFoundException($file);
        }

        require_once $file;
    }

    /**
     * @throws ViewFileNotFoundException
     */
    public function discounts()
    {
        $id = get_the_ID();

        if (!$id)
        {
            return;
        }

        $metaData   = (new WCProductDTO($id))->hydrate();

        $file = $this->path . 'discounts-tab.php';

        if (!is_file($file))
        {
            throw new ViewFileNotFoundException($file);
        }

        require_once $file;
    }
}