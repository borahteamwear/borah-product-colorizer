<?php

namespace TBProductColorizerTM\DTO\Collection;

use TBProductColorizerTM\Collection\ObjectCollection;
use TBProductColorizerTM\DTO\ImageDTO;

/**
 * Class ImagesCollection
 * @package TBProductColorizerTM\DTO\Collection
 */
class ImagesCollection extends ObjectCollection
{

    /**
     * @param array $images
     *
     * @return $this
     */
    public function hydrate(array $images)
    {
        foreach ($images as $image)
        {
            $this->push(
                (new ImageDTO)
                    ->hydrate($image)
            );
        }

        return $this;
    }
}