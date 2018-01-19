<?php

namespace TBProductColorizerTM\Frontend\Modules\WooCommerce;

use TBProductColorizerTM\DI;
use TBProductColorizerTM\DTO\Collection\ImagesCollection;
use TBProductColorizerTM\DTO\Collection\WCProductTemplateCollection;
use TBProductColorizerTM\DTO\DiscountDTO;
use TBProductColorizerTM\DTO\TemplatesDTO;
use TBProductColorizerTM\DTO\WCProductDTO;
use TBProductColorizerTM\DTO\WCProductTemplateDTO;
use TBProductColorizerTM\Frontend\Service\WooCommerce\Product;
use TBProductColorizerTM\ProductColorizer;
use TBProductColorizerTM\Utils\Loader;
use WC_Cart;
use WC_Product;
use WC_Product_Variable;
use WC_Product_Variation;
use WP_Term;

/**
 * Class WCButtons
 * @package TBProductColorizerTM\Frontend\Modules\WooCommerce
 */
class WCButtons
{

    const PRODUCT_CATEGORY_CUSTOM_FOR_TEAMS_SLUG = 'custom-for-teams';

    /**
     * @var DI
     */
    private $di;

    /**
     * @var WC_Product
     */
    private $product;

    /**
     * WCCart constructor.
     *
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
    public function defineHooks()
    {
        /** @var Loader $loader */
        $loader = $this->di->get('loader');

        $loader->addFilter('woocommerce_product_single_add_to_cart_text', $this, 'addToCart', 10, 2);
        $loader->addFilter('gettext', $this, 'commonTexts', 10, 3);
    }

    /**
     * @param string $text
     * @param WC_Product $product
     *
     * @return string
     */
    public function addToCart($text, WC_Product $product)
    {
        if ($this->isCustomTeams($product))
        {
            return 'SUBMIT DESIGN';
        }

        return $text;
    }

    /**
     * @param string $translatedText
     * @param string $text
     * @param string $domain
     *
     * @return string
     */
    public function commonTexts($translatedText, $text, $domain)
    {

        $tmpTranslatedText  = strtolower($translatedText);

        if ('view cart' === $tmpTranslatedText && $this->isCustomTeams())
        {
            $translatedText = __('VIEW MY DESIGNS', 'woocommerce');
        }
        elseif ('update cart' === $tmpTranslatedText && $this->isCartHasCustomTeamItem())
        {
            $translatedText = __('UPDATE DESIGNS', 'woocommerce');
        }
        elseif ('proceed to checkout' === $tmpTranslatedText && $this->isCartHasCustomTeamItem())
        {
            $translatedText = __('GET A CONFIRMED QUOTE', 'woocommerce');
        }

        return $translatedText;
    }

    /**
     * @return WC_Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param WC_Product $product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @param WC_Product|null $product
     *
     * @return bool
     */
    private function isCustomTeams(WC_Product $product = null)
    {
        // Super quick hotfix
        if (null === $product)
        {
            $product = $this->getProduct();
        }

        if (!$product)
        {
            $id = get_the_ID() ? :(int) url_to_postid($_SERVER['REQUEST_URI']);
            $product = wc_get_product($id);
            $this->setProduct($product);
        }

        return (new Product)
            ->setProduct($product)
            ->isProductCustomTeams()
        ;
    }

    /**
     * @return bool
     */
    private function isCartHasCustomTeamItem()
    {
        $cartItems = WC()->cart->get_cart();

        /** @var array $cartItem */
        foreach ($cartItems as $cartItem)
        {
            $product = wc_get_product($cartItem['data']->get_id());

            if ($this->isCustomTeams($product))
            {
                return true;
            }
        }

        return false;
    }
}