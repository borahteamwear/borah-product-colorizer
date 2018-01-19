<?php

namespace TBProductColorizerTM\DTO;

use TBProductColorizerTM\DTO\Collection\WCProductColorCollection;
use TBProductColorizerTM\DTO\Collection\WCProductDiscountCollection;
use TBProductColorizerTM\DTO\Collection\WCProductTemplateCollection;

/**
 * Class WCProductDTO
 * @package TBProductColorizerTM\DTO
 */
class WCProductDTO extends BasePostMetaDTO
{

    const META_KEY = 'tb_wpc_extended';

    /**
     * @var bool
     */
    protected $isActive;

    /**
     * @var string
     */
    protected $templateSelector;

    /**
     * @var string
     */
    protected $productSelector;

    /**
     * @var string
     */
    protected $productSelectSelector;

    /**
     * @var WCProductColorCollection
     */
    protected $colors;

    /**
     * @var WCProductTemplateCollection
     */
    protected $templates;

    /**
     * @var WCProductDiscountCollection
     */
    protected $discounts;

    /**
     * @return string
     */
    public function getMetaKey()
    {
        return self::META_KEY;
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

            if (empty($data))
            {
                return $this;
            }
        }

        foreach ($data as $key => $value)
        {
            $method = 'set' . ucfirst($key);

            if (!method_exists($this, $method))
            {
                continue;
            }

            if ('templates' === $key && is_array($value))
            {
                $this->setTemplates(
                    (new WCProductTemplateCollection)
                        ->hydrate($data['templates'])
                );
            }
            elseif ('colors' === $key && is_array($value))
            {
                $this->setColors(
                    (new WCProductColorCollection)
                        ->hydrate($data['colors'])
                );
            }
            elseif ('discounts' === $key && is_array($value))
            {
                $this->setDiscounts(
                    (new WCProductDiscountCollection)
                        ->hydrate($data['discounts'])
                );
            }
            else
            {
                $this->{$method}($value);
            }
        }

        return $this;
    }

    /**
     * HOTFIX
     * @return bool
     */
    public function isActive()
    {
        return 'yes' === $this->isActive;
    }

    /**
     * HOTFIX
     * @param string $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateSelector()
    {
        return $this->templateSelector;
    }

    /**
     * @param string $templateSelector
     *
     * @return $this
     */
    public function setTemplateSelector($templateSelector)
    {
        $this->templateSelector = $templateSelector;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductSelector()
    {
        return $this->productSelector;
    }

    /**
     * @param string $productSelector
     *
     * @return $this
     */
    public function setProductSelector($productSelector)
    {
        $this->productSelector = $productSelector;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductSelectSelector()
    {
        return $this->productSelectSelector;
    }

    /**
     * @param string $productSelectSelector
     *
     * @return $this
     */
    public function setProductSelectSelector($productSelectSelector)
    {
        $this->productSelectSelector = $productSelectSelector;

        return $this;
    }

    /**
     * @return WCProductColorCollection
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * @param WCProductColorCollection $colors
     *
     * @return $this
     */
    public function setColors(WCProductColorCollection $colors)
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * @return WCProductTemplateCollection
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param WCProductTemplateCollection $templates
     *
     * @return $this
     */
    public function setTemplates(WCProductTemplateCollection $templates)
    {
        $this->templates = $templates;

        return $this;
    }

    /**
     * @return WCProductDiscountCollection
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @param WCProductDiscountCollection $discounts
     *
     * @return $this
     */
    public function setDiscounts(WCProductDiscountCollection $discounts)
    {
        $this->discounts = $discounts;

        return $this;
    }
}