<?php

namespace TBProductColorizerTM\Collection;

use Countable;
use Iterator;
use TBProductColorizerTM\DTO\BaseDTO;
use TBProductColorizerTM\DTO\Interfaces\InterfaceToArrayDTO;

/**
 * Class ObjectCollection
 * @package TBProductColorizerTM\Collection
 */
abstract class ObjectCollection implements Iterator, Countable
{

    /**
     * @var int
     */
    protected $_index  = 0;

    /**
     * @var array
     */
    protected $_data = [];

    /**
     * ObjectCollection constructor.
     */
    public function __construct()
    {
        $this->_index = 0;
    }

    /**
     * Sets $this->_index to 0
     */
    public function rewind()
    {
        $this->_index = 0;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->_data[$this->_index];
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->_index;
    }

    /**
     * @return void
     */
    public function next()
    {
        ++$this->_index;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->_data[$this->_index]);
    }

    /**
     * @param mixed $data
     */
    public function add($data)
    {
        $this->_data[] = $data;
    }

    /**
     * @param $data
     */
    public function prepend($data)
    {
        array_unshift($this->_data, $data);
    }

    /**
     * @param mixed $data
     */
    public function push($data)
    {
        array_push($this->_data, $data);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];

        foreach ($this->_data as $value)
        {
            if ($value instanceof InterfaceToArrayDTO || $value instanceof ObjectCollection)
            {
                $data[] = $value->toArray();
            }
            else
            {
                $data[] = $value;
            }
        }

        return $data;
    }

    /**
     * @param $index
     * @return BaseDTO|null
     */
    public function get($index)
    {
        if (!isset($this->_data[$index]))
        {
            return null;
        }

        return $this->_data[$index];
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }
}