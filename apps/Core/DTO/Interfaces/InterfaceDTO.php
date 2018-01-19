<?php

namespace TBProductColorizerTM\DTO\Interfaces;

/**
 * Interface InterfaceDTO
 * @package TBProductColorizerTM\DTO\Interfaces
 */
interface InterfaceDTO
{
    /**
     * @param array $data
     *
     * @return $this
     */
    public function hydrate(array $data = []);
}