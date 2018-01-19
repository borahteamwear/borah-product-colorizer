<?php

namespace TBProductColorizerTM\DTO;

/**
 * Class WCProductColorDTO
 * @package TBProductColorizerTM\DTO
 */
class WCProductColorDTO extends BaseDTO
{

    /**
     * @var string
     */
    protected $selector;

    /**
     * @var int
     */
    protected $paletteId;

    /**
     * @var string
     */
    protected $defaultColor;

    /**
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * @param string $selector
     *
     * @return $this
     */
    public function setSelector($selector)
    {
        $this->selector = $selector;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaletteId()
    {
        return $this->paletteId;
    }

    /**
     * @param int $paletteId
     *
     * @return $this
     */
    public function setPaletteId($paletteId)
    {
        $this->paletteId = (int) $paletteId;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultColor()
    {
        return $this->defaultColor;
    }

    /**
     * @param string $defaultColor
     *
     * @return $this
     */
    public function setDefaultColor($defaultColor)
    {
        $this->defaultColor = $defaultColor;

        return $this;
    }
}