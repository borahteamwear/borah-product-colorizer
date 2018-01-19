<?php

namespace TBProductColorizerTM\Utils\Exceptions;

use Exception;

/**
 * Class CacheException
 * @package TBProductColorizerTM\Utils\Exceptions
 */
class CacheException extends Exception
{
    protected $message = 'Something went wrong with Cache!';
}