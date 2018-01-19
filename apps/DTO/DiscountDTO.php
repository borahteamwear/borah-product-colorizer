<?php

namespace TBProductColorizerTM\DTO;

use DateTime;

/**
 * Class DiscountDTO
 * @package TBProductColorizerTM\DTO
 */
class DiscountDTO extends BaseDTO
{

    /**
     * @var int
     */
    protected $minQuantity;

    /**
     * @var int
     */
    protected $maxQuantity;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $percentage;

    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * @param array $data
     *
     * @return $this
     */
    public function hydrate(array $data = [])
    {
        foreach ($data as $key => $value)
        {
            $method = 'set' . ucfirst($key);

            if (!method_exists($this, $method))
            {
                continue;
            }

            if (('startDate' === $key || 'endDate' === $key))
            {
                if (!$value instanceof DateTime || (is_string($value) && 0 < strlen($value)))
                {
                    $value = new DateTime($value);
                    $this->{$method}($value);
                }
            }
            else
            {
                $this->{$method}($value);
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getMinQuantity()
    {
        return $this->minQuantity;
    }

    /**
     * @param int $minQuantity
     *
     * @return $this
     */
    public function setMinQuantity($minQuantity)
    {
        $this->minQuantity = (int) $minQuantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxQuantity()
    {
        return $this->maxQuantity;
    }

    /**
     * @param int $maxQuantity
     *
     * @return $this
     */
    public function setMaxQuantity($maxQuantity)
    {
        $this->maxQuantity = (int) $maxQuantity;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = (float) $price;

        return $this;
    }

    /**
     * @return int
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param int $percentage
     *
     * @return $this
     */
    public function setPercentage($percentage)
    {
        $this->percentage = (int) $percentage;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     *
     * @return $this
     */
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param DateTime $endDate
     *
     * @return $this
     */
    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }
}