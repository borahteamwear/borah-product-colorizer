<?php

namespace TBProductColorizerTM\Exception;

use Exception;
use Throwable;

/**
 * Class ViewFileNotFoundException
 * @package TBAdditionalFields\Backend\Modules\Exception
 */
class ViewFileNotFoundException extends Exception
{

    /**
     * @var string
     */
    protected $msg = 'View file %s not found!';

    /**
     * ViewFileNotFoundException constructor.
     *
     * @param string $file
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($file = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf($this->msg, $file);

        parent::__construct($message, $code, $previous);
    }
}