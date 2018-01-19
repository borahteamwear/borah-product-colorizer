<?php

namespace TBProductColorizerTM\DTO\Collection;

use TBProductColorizerTM\Collection\ObjectCollection;
use TBProductColorizerTM\DTO\DiscountDTO;

/**
 * Class WCProductDiscountCollection
 * @package TBProductColorizerTM\DTO\Collection
 */
class WCProductDiscountCollection extends ObjectCollection
{

    /**
     * @param array $discounts
     *
     * @return $this
     */
    public function hydrate(array $discounts)
    {
        foreach ($discounts as $discount)
        {
            $this->push(
                (new DiscountDTO)
                    ->hydrate($discount)
            );
        }

        return $this;
    }
}