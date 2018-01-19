<?php

namespace TBProductColorizerTM\Forms;

use TBProductColorizerTM\DTO\BaseOptionsDTO;
use TBProductColorizerTM\Forms\Elements\Interfaces\InterfaceFormElement;

/**
 * Class Form
 * @package TBProductColorizerTM\Forms
 */
class Form
{

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @var BaseOptionsDTO
     */
    protected $options;

    /**
     * Form constructor.
     * @param BaseOptionsDTO $options
     */
    public function __construct(BaseOptionsDTO $options)
    {
        $this->options      = $options;
    }

    /**
     * @param InterfaceFormElement $element
     */
    public function add(InterfaceFormElement $element)
    {
        $element->setSavedOption(
            $this->options->get($element->getName())
        );

        $element->setPrefix($this->options->getOptionKey());

        $this->elements[$element->getName()] = $element;
    }

    /**
     * @return string
     */
    public function getOptionsGroup()
    {
        return $this->options->getOptionKey();
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function render($name)
    {
        if (!isset($this->elements[$name]))
        {
            return false;
        }

        return $this->elements[$name]->render();
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function label($name)
    {
        if (!isset($this->elements[$name]))
        {
            return false;
        }

        return $this->elements[$name]->prepareLabel();
    }
}