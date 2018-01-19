<?php

namespace TBProductColorizerTM;

/**
 * Class DI
 * @package TBProductColorizerTM
 */
class DI
{

    /**
     * @var array
     */
    private $container = [];

    /**
     * @param string $name
     * @param mixed $variable
     *
     * @return $this
     */
    public function set($name, $variable)
    {
        // It is a function
        if (is_callable($variable)) $variable = $variable();

        // Add it to services
        $this->container[$name] = $variable;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get($name)
    {
        return (isset($this->container[$name])) ? $this->container[$name] : null;
    }
}