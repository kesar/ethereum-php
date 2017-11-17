<?php

namespace EthereumPHP\Types;

class TransactionInfo
{
    private $blockHash;
    private $blockNumber;
    private $from;
    private $to;
    private $gas;
    private $gasPrice;
    private $hash;
    private $input;
    private $nonce;
    private $transactionIndex;
    private $value;
    private $v;
    private $r;
    private $s;

    public function __construct(array $response)
    {
        $this->blockHash = new BlockHash($response['blockHash']);
        $this->blockNumber = hexdec($response['blockNumber']);
        $this->from = new Address($response['from']);
        if ($response['to']) {
            $this->to = new Address($response['to']);
        }
        $this->gas = hexdec($response['gas']);
        $this->gasPrice = new Wei(hexdec($response['gasPrice']));
        $this->hash = new TransactionHash($response['hash']);
        $this->input = $response['input'];
        $this->nonce = hexdec($response['nonce']);
        $this->transactionIndex = hexdec($response['transactionIndex']);
        $this->value = new Wei(hexdec($response['value']));
        $this->v = $response['v'];
        $this->r = $response['r'];
        $this->s = $response['s'];
    }

    public function blockHash(): BlockHash
    {
        return $this->blockHash;
    }

    public function blockNumber(): int
    {
        return $this->blockNumber;
    }

    public function from(): Address
    {
        return $this->from;
    }

    public function to(): ?Address
    {
        return $this->to;
    }

    public function gas(): int
    {
        return $this->gas;
    }

    public function gasPrice(): Wei
    {
        return $this->gasPrice;
    }

    public function hash(): TransactionHash
    {
        return $this->hash;
    }

    public function input(): string
    {
        return $this->input;
    }

    public function nonce(): int
    {
        return $this->nonce;
    }

    public function transactionIndex(): int
    {
        return $this->transactionIndex;
    }

    public function value(): Wei
    {
        return $this->value;
    }

    public function v(): string
    {
        return $this->v;
    }

    public function r(): string
    {
        return $this->r;
    }

    public function s(): string
    {
        return $this->s;
    }
}
