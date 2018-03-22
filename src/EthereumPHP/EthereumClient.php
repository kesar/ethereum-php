<?php

namespace EthereumPHP;

use EthereumPHP\Methods\Contract;
use EthereumPHP\Methods\Eth;
use EthereumPHP\Methods\Net;
use EthereumPHP\Methods\Personal;
use EthereumPHP\Methods\Shh;
use EthereumPHP\Methods\Web3;
use Graze\GuzzleHttp\JsonRpc\Client;

class EthereumClient
{
    private $client;
    private $methods = [];

    public function __construct(string $url, array $config = [])
    {
        $this->client = Client::factory($url, $config);
        $this->methods = [
            'net' => new Net($this->client),
            'eth' => new Eth($this->client),
            'shh' => new Shh($this->client),
            'web3' => new Web3($this->client),
            'personal' => new Personal($this->client),
            'contract' => new Contract($this->client),
        ];
    }

    public function net(): Net
    {
        return $this->methods['net'];
    }

    public function web3(): Web3
    {
        return $this->methods['web3'];
    }

    public function shh(): Shh
    {
        return $this->methods['shh'];
    }

    public function eth(): Eth
    {
        return $this->methods['eth'];
    }

    public function personal(): Personal
    {
        return $this->methods['personal'];
    }

    public function contract(array $abi): Contract
    {
        return $this->methods['contract']->abi($abi);
    }
}
