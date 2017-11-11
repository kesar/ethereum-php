<?php

namespace EthereumPHP\Types;

class Wei
{
    private $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function toEther(): float
    {
        return $this->amount / 1000000000000000000;
    }

    public function __toString()
    {
        return (string)$this->amount;
    }
}
