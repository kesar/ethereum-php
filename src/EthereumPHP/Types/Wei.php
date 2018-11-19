<?php

namespace EthereumPHP\Types;

class Wei
{
    private $amount;

    public function __construct($amount)
    {
        $this->amount = (string)$amount;
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function toEther(): string
    {
        return bcdiv($this->amount, "1000000000000000000", 18);
    }

    public function __toString()
    {
        return $this->amount;
    }
}
