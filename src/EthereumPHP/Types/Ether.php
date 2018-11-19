<?php

namespace EthereumPHP\Types;

class Ether
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

    public function toWei(): Wei
    {
        return new Wei(bcmul($this->amount, "1000000000000000000"));
    }

    public function __toString()
    {
        return $this->amount;
    }
}
