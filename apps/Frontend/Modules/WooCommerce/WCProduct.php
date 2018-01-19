<?php

namespace TBProductColorizerTM\Frontend\Modules\WooCommerce;

use TBProductColorizerTM\DI;
use TBProductColorizerTM\DTO\Collection\ImagesCollection;
use TBProductColorizerTM\DTO\Collection\WCProductTemplateCollection;
use TBProductColorizerTM\DTO\TemplatesDTO;
use TBProductColorizerTM\DTO\WCProductDTO;
use TBProductColorizerTM\DTO\WCProductTemplateDTO;
use TBProductColorizerTM\Utils\Loader;
use WC_Product;

/**
 * Class WCProduct
 * @package TBProductColorizerTM\Frontend\Modules\WooCommerce
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

        $loader->addAction('woocommerce_single_product_summary', $this, 'productImage', 20);
    }

    /**
     * Product Image
     */
    public function productImage()
    {
        $templates = $this->getTemplates();

        if (!$templates)
        {
            return '';
        }

        require_once $this->path . 'image.php';
    }

    /**
     * @return null|WC_Product
     */
    private function getProduct()
    {
        $product = wc_get_product(get_the_ID());

        if (!$product)
        {
            return null;
        }

        return $product;
    }

    /**
     * @return null|WCProductTemplateCollection
     */
    private function getTemplates()
    {
        $wcProduct = $this->getProduct();

        if (!$wcProduct)
        {
            return null;
        }

        $dtoProduct = (new WCProductDTO(get_the_ID()))
            ->hydrate()
        ;

        if (null === $dtoProduct->getTemplates() || 1 > $dtoProduct->getTemplates()->count() || !$dtoProduct->isActive())
        {
            return null;
        }

        return $dtoProduct->getTemplates();
    }

    /**
     * @param WCProductTemplateDTO $template
     * @return null|ImagesCollection
     */
    private function getTemplateImages(WCProductTemplateDTO $template)
    {
        $dtoTemplate = (new TemplatesDTO($template->getTemplateId()))
            ->hydrate()
        ;

        if (null === $dtoTemplate->getImages() || 1 > $dtoTemplate->getImages()->count())
        {
            return null;
        }

        return $dtoTemplate->getImages();
    }
}