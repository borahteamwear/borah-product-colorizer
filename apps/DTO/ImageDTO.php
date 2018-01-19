<?php

namespace TBProductColorizerTM\DTO;

/**
 * Class ImageDTO
 * @package TBProductColorizerTM\DTO
 */
class ImageDTO extends BaseDTO
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $targetInput;

    /**
     * @param array $data
     */
    public function afterHydrate(array $data = [])
    {
        $url = wp_get_attachment_url($this->getId());

        if (!$url)
        {
            return;
        }

        $this->setUrl($url);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetInput()
    {
        return $this->targetInput;
    }

    /**
     * @param string $targetInput
     *
     * @return $this
     */
    public function setTargetInput($targetInput)
    {
        $this->targetInput = (string) $targetInput;

        return $this;
    }
}