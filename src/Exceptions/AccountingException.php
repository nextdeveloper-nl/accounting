<?php

namespace NextDeveloper\Accounting\Exceptions;

use NextDeveloper\Commons\Exceptions\AbstractCommonsException;

class AccountingException extends AbstractCommonsException
{
    protected $defaultMessage = 'We have a problem with the accounting service.';

    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
