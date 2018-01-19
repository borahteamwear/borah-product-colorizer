<?php

namespace TBProductColorizerTM\DTO;

/**
 * Class WCProductTemplateDTO
 * @package TBProductColorizerTM\DTO
 */
class WCProductTemplateDTO extends BaseDTO
{

    /**
     * @var string
     */
    protected $value;

    /**
     * @var int
     */
    protected $templateId;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param int $templateId
     *
     * @return $this
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;

        return $this;
    }
}