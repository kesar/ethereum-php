<?php

namespace EthereumPHP\Types;

class Block
{
    private $difficulty;
    private $extraData;
    private $gasLimit;
    private $gasUsed;
    private $hash;
    private $logsBloom;
    private $miner;
    private $mixHash;
    private $nonce;
    private $number;
    private $parentHash;
    private $receiptsRoot;
    private $sha3Uncles;
    private $size;
    private $stateRoot;
    private $timestamp;
    private $totalDifficulty;
    private $transactionsRoot;
    private $transactions;
    private $uncles;

    public function __construct(array $response)
    {
        $this->difficulty = hexdec($response['difficulty']);
        $this->extraData = $response['extraData'];
        $this->gasLimit = hexdec($response['gasLimit']);
        $this->gasUsed = new Wei(hexdec($response['gasUsed']));
        $this->hash = new BlockHash($response['hash']);
        $this->logsBloom = $response['logsBloom'];
        $this->miner = new Address($response['miner']);
        $this->mixHash = new Hash($response['mixHash']);
        $this->nonce = $response['nonce'];
        $this->number = hexdec($response['number']);
        $this->parentHash = new BlockHash($response['parentHash']);
        $this->receiptsRoot = new Hash($response['receiptsRoot']);
        $this->sha3Uncles = new Hash($response['sha3Uncles']);
        $this->size = hexdec($response['size']);
        $this->stateRoot = new Hash($response['stateRoot']);
        $this->timestamp = hexdec($response['timestamp']);
        $this->totalDifficulty = hexdec($response['totalDifficulty']);
        $this->transactionsRoot = new TransactionHash($response['transactionsRoot']);
        $this->transactions = [];
        foreach ($response['transactions'] as $transaction) {
            $this->transactions[] = new TransactionHash($transaction);
        }
        $this->uncles = [];
        foreach ($response['uncles'] as $uncle) {
            $this->uncles[] = new BlockHash($uncle);
        }
    }

    public function difficulty(): float
    {
        return $this->difficulty;
    }

    public function extraData(): string
    {
        return $this->extraData;
    }

    public function gasLimit(): int
    {
        return $this->gasLimit;
    }

    public function gasUsed(): Wei
    {
        return $this->gasUsed;
    }

    public function hash(): BlockHash
    {
        return $this->hash;
    }

    public function logsBloom(): string
    {
        return $this->logsBloom;
    }

    public function miner(): Address
    {
        return $this->miner;
    }

    public function mixHash(): Hash
    {
        return $this->mixHash;
    }

    public function nonce(): string
    {
        return $this->nonce;
    }

    public function number(): int
    {
        return $this->number;
    }

    public function parentHash(): BlockHash
    {
        return $this->parentHash;
    }

    public function receiptsRoot(): Hash
    {
        return $this->receiptsRoot;
    }

    public function sha3Uncles(): Hash
    {
        return $this->sha3Uncles;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function stateRoot(): Hash
    {
        return $this->stateRoot;
    }

    public function timestamp(): int
    {
        return $this->timestamp;
    }

    public function totalDifficulty(): float
    {
        return $this->totalDifficulty;
    }

    public function transactionsRoot(): TransactionHash
    {
        return $this->transactionsRoot;
    }

    public function transactions(): array
    {
        return $this->transactions;
    }

    public function uncles(): array
    {
        return $this->uncles;
    }
}
