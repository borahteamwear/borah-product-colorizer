<?php

namespace TBProductColorizerTM\Frontend\Service\WooCommerce;

use WC_Product;
use WP_Term;

/**
 * Class Product
 * @package TBProductColorizerTM\Frontend\Service\WooCommerce
 */
class Product
{

    const PRODUCT_CATEGORY_CUSTOM_FOR_TEAMS_SLUG = 'custom-for-teams';

    /**
     * @var WC_Product
     */
    private $product;

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
     * @return bool
     */
    public function isProductCustomTeams()
    {
        if (!$this->getProduct())
        {
            return false;
        }

        foreach ($this->getProduct()->get_category_ids() as $id)
        {
            $term = get_term_by('id', (int) $id, 'product_cat');

            if ($term instanceof WP_Term && self::PRODUCT_CATEGORY_CUSTOM_FOR_TEAMS_SLUG === $term->slug)
            {
                return true;
            }
        }

        return false;
    }
}