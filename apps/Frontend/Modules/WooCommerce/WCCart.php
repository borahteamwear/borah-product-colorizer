<?php

namespace TBProductColorizerTM\Frontend\Modules\WooCommerce;

use TBProductColorizerTM\DI;
use TBProductColorizerTM\DTO\Collection\ImagesCollection;
use TBProductColorizerTM\DTO\Collection\WCProductTemplateCollection;
use TBProductColorizerTM\DTO\DiscountDTO;
use TBProductColorizerTM\DTO\TemplatesDTO;
use TBProductColorizerTM\DTO\WCProductDTO;
use TBProductColorizerTM\DTO\WCProductTemplateDTO;
use TBProductColorizerTM\ProductColorizer;
use TBProductColorizerTM\Utils\Loader;
use WC_Cart;
use WC_Product;
use WC_Product_Variation;
use WP_Term;

/**
 * Class WCCart
 * @package TBProductColorizerTM\Frontend\Modules\WooCommerce
 */
class WCCart
{
    /**
     * @var DI
     */
    private $di;

    /**
     * WCCart constructor.
     *
     * @param DI $di
     */
    public function __construct(DI $di)
    {
        $ds         = DIRECTORY_SEPARATOR;
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

        $loader->addAction('woocommerce_before_calculate_totals', $this, 'reCalculateItemsPrice', 10, 1);
        $loader->addAction('woocommerce_cart_calculate_fees', $this, 'reCalculateCartTotals', 10, 1);
        $loader->addFilter('woocommerce_get_item_data', $this, 'modifyItemData', 999999, 2);
        remove_action('woocommerce_cart_calculate_fees', 'tm_calculate_cart_fee');
    }

    /**
     * @return array
     */
    private function getColorsWithPrices()
    {
        $terms = get_terms([
            'hide_empty'    => false,
            'taxonomy'      => ProductColorizer::TAXONOMY_COLORS,
        ]);

        $colors = [];

        /** @var WP_Term $term */
        foreach ($terms as $term)
        {
            $price = (float) get_term_meta($term->term_id, 'tb_wpc_price', true);
            $color = get_term_meta($term->term_id, 'tb_wpc_color', true);

            if (!$price || 0 >= $price || !$color)
            {
                continue;
            }

            $colors[$term->name] = $price;
        }

        return $colors;
    }

    /**
     * @param WC_Cart $cartObject
     */
    public function reCalculateItemsPrice(WC_Cart $cartObject)
    {
        foreach ($cartObject->cart_contents as $key => $item)
        {

            $optionsFee     = (isset($item['tm_epo_options_prices'])) ? (float) $item['tm_epo_options_prices'] : 0;
            $productPrice   = $this->getProductPrice($item) + $optionsFee;

            $item['data']->set_price($productPrice);
        }
    }

    /**
     * @param array $item
     *
     * @return float|int
     */
    private function reCalculateTMPrices(array $item)
    {
        $cartFee    = $this->reCalculateTMCartFee($item);
        $optionsFee = (isset($item['tm_epo_options_prices'])) ? (float) $item['tm_epo_options_prices'] : 0;

        return $cartFee + $optionsFee;
    }

    /**
     * @param array $item
     *
     * @return float|int
     */
    private function reCalculateTMCartFee(array $item)
    {
        // Builder
        if (!isset($item['tmcartfee']))
        {
            return 0;
        }

        $price = 0;

        foreach ($item['tmcartfee'] as $cartFee)
        {
            $price += (float) $cartFee['price'];
        }

        return $price;
    }

    /**
     * @param WC_Cart $cartObject
     */
    public function reCalculateCartTotals(WC_Cart $cartObject)
    {
        $colorsWithPrices   = $this->getColorsWithPrices();
        $totalColorPrices   = 0;

        foreach ($cartObject->cart_contents as $key => $item)
        {
            $totalColorPrices += $this->getColorPrices($item, $colorsWithPrices);
        }

        if (0 >= $totalColorPrices)
        {
            return;
        }

        $cartObject->add_fee(__('Florescent Color Fee', ProductColorizer::SLUG) , $totalColorPrices);
    }

    /**
     * @param array $item
     *
     * @return float
     */
    private function getProductPrice(array $item)
    {
        $productPrice = (float) (new WC_Product($item['product_id']))->get_price();

        $discountedPrice = $this->getDiscountedPrice($item);

        if (null === $discountedPrice)
        {
            return $productPrice;
        }

        if ($discountedPrice->getPrice() > 0)
        {
            return $discountedPrice->getPrice();
        }
        elseif ($discountedPrice->getPercentage() > 0)
        {
            return (float) $productPrice - (($productPrice / 100) * $discountedPrice->getPercentage());
        }

        return $productPrice;
    }

    /**
     * @param array $item
     *
     * @return DiscountDTO|null
     */
    private function getDiscountedPrice(array $item)
    {
        $metaData = (new WCProductDTO($item['product_id']))
            ->hydrate()
        ;

        if (!$metaData || null === $metaData->getDiscounts())
        {
            return null;
        }

        foreach ($metaData->getDiscounts() as $discount)
        {
            if ($this->isDiscountMatching((int) $item['quantity'], $discount))
            {
                return $discount;
            }
        }

        return null;
    }

    private function isDiscountMatching($quantity, DiscountDTO $discountDTO)
    {
        return (
            $quantity >= $discountDTO->getMinQuantity() &&
            ($discountDTO->getMaxQuantity() <= 0 || $quantity <= $discountDTO->getMaxQuantity() )
        );
    }

    /**
     * @param array $item
     *
     * @param array $colors
     * @return int|null
     */
    private function getColorPrices(array $item, array $colors)
    {
        if (!isset($item['tmdata']) || !isset($item['tmdata']['tmcartepo_data']))
        {
            return null;
        }

        $additionalPrice = 0;

        foreach ($item['tmdata']['tmcartepo_data'] as $itemCartData)
        {
            if (0 !== strpos($itemCartData['attribute'], 'tmcp_color_') || !isset($colors[$itemCartData['key']]))
            {
                continue;
            }

            $additionalPrice += $colors[$itemCartData['key']];
            break;
        }

        $qty = (int) $item['quantity'];

        if ($qty < 1)
        {
            $qty = 1;
        }

        return $additionalPrice * $qty;
    }

    /**
     * @param array $otherData
     * @param WC_Cart $cartItem
     *
     * @return mixed
     */
    public function modifyItemData($otherData, $cartItem)
    {
        foreach ($otherData as $key => $data)
        {
            if ($data['value'] === '0' || empty($data['value']))
            {
                unset($otherData[$key]);
            }
        }

        return $otherData;
    }
}