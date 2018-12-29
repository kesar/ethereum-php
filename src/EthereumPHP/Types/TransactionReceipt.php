<?php

namespace EthereumPHP\Types;

class TransactionReceipt
{
    private $blockHash;
    private $blockNumber;
    private $contractAddress;
    private $cumulativeGasUsed;
    private $from;
    private $gasUsed;
    private $logs;
    private $logsBloom;
    private $status;
    private $to;
    private $transactionHash;
    private $transactionIndex;
    
    // TODO: do it
    public function __construct($response)
    {
        $this->blockHash = new BlockHash($response['blockHash']);
        $this->blockNumber = hexdec($response['blockNumber']);
        if ($response['contractAddress']) {
            $this->contractAddress = new Address($response['contractAddress']);
        }
        $this->cumulativeGasUsed = new Wei(hexdec($response['cumulativeGasUsed']));
        if (isset($response['from'])) {
            $this->from = new Address($response['from']);
        }
        $this->gasUsed = new Wei(hexdec($response['gasUsed']));
        $this->logs = $response['logs'];
        $this->logsBloom = hexdec($response['logsBloom']);
        $this->status = hexdec($response['status']);
        if (isset($response['to'])) {
            $this->to = new Address($response['to']);
        }
        $this->transactionHash = new TransactionHash($response['transactionHash']);
        $this->transactionIndex = hexdec($response['transactionIndex']);
    }
    
    public function blockHash(): BlockHash
    {
        return $this->blockHash;
    }
    
    public function blockNumber(): int
    {
        return $this->blockNumber;
    }
    
    public function contractAddress(): ?Address
    {
        return $this->contractAddress;
    }
    
    public function cumulativeGasUsed(): Wei
    {
        return $this->cumulativeGasUsed;
    }
    
    public function gasUsed(): Wei
    {
        return $this->gasUsed;
    }
    
    public function logs(): array
    {
        return $this->logs;
    }
    
    public function logsBloom(): int
    {
        return $this->logsBloom;
    }
    
    public function from(): Address
    {
        return $this->from;
    }
    
    public function to(): ?Address
    {
        return $this->to;
    }
    
    public function status(): int
    {
        return $this->status;
    }
    
    public function transactionHash(): TransactionHash
    {
        return $this->transactionHash;
    }
    
    public function transactionIndex(): int
    {
        return $this->transactionIndex;
    }
}
