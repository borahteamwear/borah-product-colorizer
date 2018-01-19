<?php

namespace TBProductColorizerTM\DTO;

/**
 * Class BaseOptionsDTO
 * @package TBProductColorizerTM\DTO
 */
abstract class BaseOptionsDTO extends BaseDTO
{

    /**
     * @var array
     */
    protected $_raw;

    /**
     * @return string
     */
    abstract public function getOptionKey();

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        $this->hydrate(get_option($this->getOptionKey(), []));
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function hydrate(array $data = [])
    {
        $this->_raw = $data;

        foreach ($data as $key => $value)
        {
            if (property_exists($this, $key))
            {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function save()
    {
        return update_option($this->getOptionKey(), $this->toArray());
    }

    /**
     * @return array
     */
    public function getRaw()
    {
        return $this->_raw;
    }

    /**
     * @param string $key
     * @return null|mixed
     */
    public function get($key)
    {
        $method = 'get' . ucfirst($key);

        if (method_exists($this, $method))
        {
            return $this->{$method}();
        }

        $method = 'is' . ucfirst($key);

        if (method_exists($this, $method))
        {
            return $this->{$method}();
        }

        return null;
    }
}