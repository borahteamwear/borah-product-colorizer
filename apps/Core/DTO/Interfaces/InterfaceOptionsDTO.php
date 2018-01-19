<?php

namespace TBProductColorizerTM\DTO\Interfaces;

/**
 * Interface InterfaceOptionsDTO
 * @package TBProductColorizerTM\DTO\Interfaces
 */
interface InterfaceOptionsDTO extends InterfaceDTO
{

    /**
     * @return string
     */
    public function getOptionKey();

    /**
     * @return bool
     */
    public function save();

    /**
     * @return array
     */
    public function getRaw();

    /**
     * @param string $key
     * @return null|mixed
     */
    public function get($key);

    /**
     * @return array
     */
    public function toArray();
}