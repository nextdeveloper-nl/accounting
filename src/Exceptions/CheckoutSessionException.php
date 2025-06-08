<?php

namespace NextDeveloper\Accounting\Exceptions;

use NextDeveloper\Commons\Exceptions\AbstractCommonsException;

class CheckoutSessionException extends AbstractCommonsException
{
    protected $defaultMessage = 'We cannot complete the checkout session process.';

    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
