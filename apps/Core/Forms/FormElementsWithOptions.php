<?php

namespace TBProductColorizerTM\Forms;

use TBProductColorizerTM\Forms\Elements\Interfaces\InterfaceFormElementWithOptions;

/**
 * Class Elements
 * @package TBProductColorizerTM\Forms
 */
abstract class FormElementsWithOptions extends FormElements implements InterfaceFormElementWithOptions
{

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Text constructor.
     * @param string $name
     * @param array $options
     * @param array $attributes
     */
    public function __construct($name, $options = [], $attributes = [])
    {
        parent::__construct($name, $attributes);

        $this->addOptions($options);
    }

    /**
     * @param string $id
     * @param string $name
     *
     * @return $this
     */
    public function addOption($id, $name)
    {
        $this->options[$id] = $name;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function removeOption($id)
    {
        if (isset($this->options[$id]))
        {
            unset($this->options[$id]);
        }

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function addOptions($options)
    {
        foreach ($options as $id => $name)
        {
            $this->addOption($id, $name);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}