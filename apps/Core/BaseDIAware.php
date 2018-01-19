<?php

namespace TBProductColorizerTM;

/**
 * Class BaseDIAware
 * @package TBProductColorizerTM
 */
abstract class BaseDIAware
{

    /**
     * @var DI
     */
    protected $di;

    /**
     * BaseDIAware constructor.
     * @param DI $di
     */
    public function __construct(DI $di)
    {
        $this->di = $di;

        if (method_exists($this, 'initialize'))
        {
            $this->initialize();
        }
    }

    /**
     * @return DI
     */
    public function getDI()
    {
        return $this->di;
    }
}