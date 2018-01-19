<?php

namespace TBProductColorizerTM\DTO\Collection;

use TBProductColorizerTM\Collection\ObjectCollection;
use TBProductColorizerTM\DTO\WCProductColorDTO;

/**
 * Class WCProductColorCollection
 * @package TBProductColorizerTM\DTO\Collection
 */
class WCProductColorCollection extends ObjectCollection
{

    /**
     * @param array $colors
     *
     * @return $this
     */
    public function hydrate(array $colors)
    {
        foreach ($colors as $color)
        {
            $this->push(
                (new WCProductColorDTO)
                    ->hydrate($color)
            );
        }

        return $this;
    }
}