<?php

namespace TBProductColorizerTM\DTO\Collection;

use TBProductColorizerTM\Collection\ObjectCollection;
use TBProductColorizerTM\DTO\WCProductTemplateDTO;

/**
 * Class WCProductTemplateCollection
 * @package TBProductColorizerTM\DTO\Collection
 */
class WCProductTemplateCollection extends ObjectCollection
{

    /**
     * @param array $templates
     *
     * @return $this
     */
    public function hydrate(array $templates)
    {
        foreach ($templates as $template)
        {
            $this->push(
                (new WCProductTemplateDTO)
                    ->hydrate($template)
            );
        }

        return $this;
    }
}