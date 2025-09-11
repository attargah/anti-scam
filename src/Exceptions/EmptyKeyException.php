<?php

namespace Attargah\AntiScam\Exceptions;

use Exception;

class EmptyKeyException extends Exception
{
    public function __construct($message = "Key not found. Please update the Key value in your config file (anti-scam.php).", $code = 400)
    {
        parent::__construct($message, $code);
    }
}
