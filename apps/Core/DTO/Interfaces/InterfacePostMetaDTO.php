<?php

namespace TBProductColorizerTM\DTO\Interfaces;

/**
 * Interface InterfacePostMetaDTO
 * @package TBProductColorizerTM\DTO\Interfaces
 */
interface InterfacePostMetaDTO extends InterfaceDTO
{

    /**
     * @return string
     */
    public function getMetaKey();

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return bool
     */
    public function save();

    /**
     * @param string $key
     *
     * @return null|mixed
     */
    public function get($key);

    /**
     * @return array
     */
    public function toArray();
}