<?php

namespace TBProductColorizerTM\DTO;

/**
 * Class BaseOptionsDTO
 * @package TBProductColorizerTM\DTO
 */
abstract class BasePostMetaDTO extends BaseDTO
{

    /**
     * @var int
     */
    protected $_id;

    /**
     * @return string
     */
    abstract public function getMetaKey();

    /**
     * Settings constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        if (null === $id)
        {
            return;
        }

        $this->setId($id);
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function hydrate(array $data = [])
    {
        if (empty($data))
        {
            $data = $this->getDataFromDatabase();
        }

        if (!is_array($data) || empty($data))
        {
            return $this;
        }

        foreach ($data as $key => $value)
        {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method))
            {
                $this->{$method}($value);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getDataFromDatabase()
    {
        return get_post_meta($this->getId(), $this->getMetaKey(), true);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->_id = (int) $id;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $data   = $this->toArray();

        if ($this->isChanged($data))
        {
            return true;
        }

        $result = update_post_meta($this->getId(), $this->getMetaKey(), $data);

        return (true === $result || 0 < $result);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private function isChanged(array $data)
    {
        $result = get_post_meta($this->getId(), $this->getMetaKey(), true);

        return ($result === $data);
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