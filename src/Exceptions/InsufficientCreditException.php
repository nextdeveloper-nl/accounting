<?php

namespace NextDeveloper\Accounting\Exceptions;

class InsufficientCreditException extends AccountingException
{
    public function __construct(string $reason, float $required, float $available)
    {
        parent::__construct(
            sprintf(
                'Insufficient credit for "%s". Required: %.6f USD, available: %.6f USD.',
                $reason,
                $required,
                $available
            )
        );
    }
}
