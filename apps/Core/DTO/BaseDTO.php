<?php

namespace TBProductColorizerTM\DTO;

use TBProductColorizerTM\Collection\ObjectCollection;
use TBProductColorizerTM\DTO\Interfaces\InterfaceDTO;
use TBProductColorizerTM\DTO\Interfaces\InterfaceToArrayDTO;

/**
 * Class BaseDTO
 * @package TBProductColorizerTM\DTO
 */
abstract class BaseDTO implements InterfaceDTO, InterfaceToArrayDTO
{

    /**
     * @param array $data
     *
     * @return $this
     */
    public function hydrate(array $data = [])
    {
        if (method_exists($this, 'beforeHydrate'))
        {
            $this->beforeHydrate($data);
        }

        foreach ($data as $key => $value)
        {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method))
            {
                $this->{$method}($value);
            }
        }

        if (method_exists($this, 'afterHydrate'))
        {
            $this->afterHydrate($data);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];

        $properties = get_object_vars($this);

        foreach ($properties as $key => $value)
        {
            if (0 === strpos($key, '_'))
            {
                continue;
            }

            if ($value instanceof InterfaceToArrayDTO || $value instanceof ObjectCollection)
            {
                $data[$key] = $value->toArray();
            }
            else
            {
                $data[$key] = $value;
            }

        }

        return $data;
    }
}