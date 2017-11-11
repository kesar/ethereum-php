<?php

namespace EthereumPHP\Types;

class Address
{
    private $address;

    public function __construct(string $address)
    {
        if (strlen($address) !== 42) {
            throw new \LengthException($address.' is not valid.');
        }
        $this->address = $address;
    }

    public function __toString()
    {
        return $this->address;
    }

    public function toString()
    {
        return $this->address;
    }
}
