<?php

namespace TBProductColorizerTM\Forms\Elements\Interfaces;

/**
 * Interface InterfaceElementWithOptions
 * @package TBProductColorizerTM\Forms\Elements\Interfaces
 */
interface InterfaceFormElementWithOptions
{

    /**
     * @param string $id
     * @param string $name
     * @return void
     */
    public function addOption($id, $name);

    /**
     * @param string $id
     * @return void
     */
    public function removeOption($id);

    /**
     * @param array $options
     * @return void
     */
    public function addOptions($options);

    /**
     * @return array
     */
    public function getOptions();
}