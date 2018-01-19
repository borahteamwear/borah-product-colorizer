<?php

namespace TBProductColorizerTM\Forms\Elements\Interfaces;

/**
 * Interface InterfaceFormElement
 * @package TBProductColorizerTM\Forms\Elements\Interfaces
 */
interface InterfaceFormElement
{

    /**
     * @param string $name
     * return $this
     */
    public function setName($name);

    /**
     * @return null|string
     */
    public function getName();

    /**
     * @return null|string
     */
    public function getPrefix();

    /**
     * @param null|string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix);

    /**
     * @return null|string
     */
    public function getFormName();

    /**
     * @param string $name
     * @param string $value
     * return $this
     */
    public function setAttribute($name, $value);

    /**
     * @param array $attributes
     * return $this
     */
    public function setAttributes($attributes);

    /**
     * @return string
     */
    public function prepareAttributes();

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param string $label
     * return $this
     */
    public function setLabel($label);

    /**
     * @return null|string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function prepareLabel();

    /**
     * @param array|string $filters
     * return $this
     */
    public function setFilters($filters);

    /**
     * @return array
     */
    public function getFilters();

    /**
     * @param string $value
     * return $this
     */
    public function setDefault($value);

    /**
     * @return null|string
     */
    public function getDefault();

    /**
     * @param object $validation
     * return $this
     */
    public function addValidation($validation);

    /**
     * @return array
     */
    public function getValidations();

    /**
     * @param string $file
     * return $this
     */
    public function setRenderFile($file);

    /**
     * @return string
     */
    public function getRenderFile();

    /**
     * @param null|string $name
     * @return string
     */
    public function getId($name = null);

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return mixed
     */
    public function render();

    /**
     * @param null|string|array|object $savedOption
     *
     * @return $this
     */
    public function setSavedOption($savedOption);

    /**
     * @return null|string|array|object
     */
    public function getSavedOption();
}