<?php

namespace Contracts;

class AbstractContractItem
{
    protected $model;

    protected $term;

    public const FIXED_DISCOUNT = 'fixed-discount';

    public const FIXED_RATE = 'fixed-rate';
}
