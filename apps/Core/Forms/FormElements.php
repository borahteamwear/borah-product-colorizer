<?php

namespace TBProductColorizerTM\Forms;

use TBProductColorizerTM\Forms\Elements\Interfaces\InterfaceFormElement;

/**
 * Class Elements
 * @package TBProductColorizerTM\Forms
 */
abstract class FormElements implements InterfaceFormElement
{

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var null|string
     */
    protected $label;

    /**
     * @var null|string|array
     */
    protected $default;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $validations = [];

    /**
     * @var string
     */
    protected $renderFile;

    /**
     * @var null|string|array|object
     */
    protected $savedOption;

    /**
     * @return string
     */
    abstract protected function prepareOutput();

    /**
     * @return string
     */
    abstract public function render();

    /**
     * Text constructor.
     * @param string $name
     * @param array $attributes
     */
    public function __construct($name, array $attributes = [])
    {
        $this->setName($name);
        $this->setAttributes($attributes);
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
       return $this->name;
    }

    /**
     * @return null|string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param null|string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFormName()
    {
        if ($this->getPrefix())
        {
            return $this->getPrefix() . '[' . $this->getName() . ']';
        }

        return $this->getName();
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $name => $value)
        {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function prepareAttributes()
    {
        $attributes = '';
        foreach ($this->attributes as $name => $value)
        {
            $attributes .= sprintf('%s="%s"', $name, $value);
        }

        return rtrim($attributes, ' ');
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function prepareLabel()
    {
        return "<label for='{$this->getId()}'>{$this->label}</label>";
    }

    /**
     * @param array|string $filters
     *
     * @return $this
     */
    public function setFilters($filters)
    {
        if (is_string($filters))
        {
            $this->filters[] = $filters;
        }
        else
        {
            array_merge($this->filters, $filters);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param string|array $value
     *
     * @return $this
     */
    public function setDefault($value)
    {
        $this->default = $value;

        return $this;
    }

    /**
     * @return null|string|array
     */
    public function getDefault()
    {
        if ($this->getSavedOption())
        {
            return $this->getSavedOption();
        }

        return $this->default;
    }

    /**
     * @param object $validation
     *
     * @return $this
     */
    public function addValidation($validation)
    {
        $this->validations[] = $validation;

        return $this;
    }

    /**
     * @return array
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setRenderFile($file)
    {
        if (file_exists($file) && is_readable($file))
        {
            $this->renderFile = $file;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRenderFile()
    {
        return $this->renderFile;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param null|string $name
     *
     * @return string
     */
    public function getId($name = null)
    {
        if (null === $name)
        {
            $name = $this->getFormName();
        }

        if (!$name)
        {
            return '';
        }

        return str_replace(' ', '_', $name);
    }

    /**
     * @return array|null|object|string
     */
    public function getSavedOption()
    {
        return $this->savedOption;
    }

    /**
     * @param array|null|object|string $savedOption
     *
     * @return $this
     */
    public function setSavedOption($savedOption)
    {
        $this->savedOption = $savedOption;

        return $this;
    }
}