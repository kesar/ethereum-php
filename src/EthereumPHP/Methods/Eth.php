<?php

namespace EthereumPHP\Methods;

use EthereumPHP\Types\Address;
use EthereumPHP\Types\Block;
use EthereumPHP\Types\BlockHash;
use EthereumPHP\Types\BlockNumber;
use EthereumPHP\Types\Transaction;
use EthereumPHP\Types\TransactionHash;
use EthereumPHP\Types\TransactionInfo;
use EthereumPHP\Types\TransactionReceipt;
use EthereumPHP\Types\Wei;

class Eth extends AbstractMethods
{
    public function protocolVersion(): string
    {
        $response = $this->client->send(
            $this->client->request(67, 'eth_protocolVersion', [])
        );

        return hexdec($response->getRpcResult());
    }

    public function syncing()
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_syncing', [])
        );

        $result = $response->getRpcResult();
        if ($result === false) {
            return $result;
        }

        return $result; // TODO: test this
    }

    public function coinbase(): ?Address
    {
        $response = $this->client->send(
            $this->client->request(64, 'eth_coinbase', [])
        );

        return ($response->getRpcResult()) ? new Address($response->getRpcResult()) : null;
    }

    public function mining(): bool
    {
        $response = $this->client->send(
            $this->client->request(71, 'eth_mining', [])
        );

        return (bool)$response->getRpcResult();

    }

    public function hashRate(): int
    {
        $response = $this->client->send(
            $this->client->request(71, 'eth_hashrate', [])
        );

        return hexdec($response->getRpcResult());
    }

    public function gasPrice(): Wei
    {
        $response = $this->client->send(
            $this->client->request(73, 'eth_gasPrice', [])
        );

        return new Wei(hexdec($response->getRpcResult()));
    }

    /**
     * @return Address[]
     */
    public function accounts(): array
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_accounts', [])
        );
        $addresses = [];
        foreach ($response->getRpcResult() as $address) {
            $addresses[] = new Address($address);
        }

        return $addresses;

    }

    public function blockNumber(): int
    {
        $response = $this->client->send(
            $this->client->request(83, 'eth_blockNumber', [])
        );

        return hexdec($response->getRpcResult());
    }

    public function getBalance(Address $address, BlockNumber $blockNumber): Wei
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getBalance', [$address->toString(), $blockNumber->toString()])
        );

        return new Wei(\Phlib\base_convert($response->getRpcResult(), 16, 10));

    }

    public function getStorageAt(Address $address, int $quantity, BlockNumber $blockNumber): string
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getStorageAt', [$address->toString(), $quantity, $blockNumber->toString()])
        );

        return $response->getRpcResult();
    }

    public function getTransactionCount(Address $address, BlockNumber $blockNumber): int
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_blockNumber', [$address->toString(), $blockNumber->toString()])
        );

        return hexdec($response->getRpcResult());
    }

    public function getBlockTransactionCountByHash(BlockHash $hash): int
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getBlockTransactionCountByHash', [$hash->toString()])
        );

        return hexdec($response->getRpcResult());

    }

    public function getBlockTransactionCountByNumber(BlockNumber $blockNumber): int
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getBlockTransactionCountByNumber', [$blockNumber->toString()])
        );

        return hexdec($response->getRpcResult());

    }

    public function getUncleCountByBlockHash(BlockHash $hash): int
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getUncleCountByBlockHash', [$hash->toString()])
        );

        return hexdec($response->getRpcResult());

    }

    public function getUncleCountByBlockNumber(BlockNumber $blockNumber): int
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getUncleCountByBlockNumber', [$blockNumber->toString()])
        );

        return hexdec($response->getRpcResult());

    }

    public function getCode(Address $address, BlockNumber $blockNumber)
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getCode', [$address->toString(), $blockNumber->toString()])
        );

        return $response->getRpcResult();
    }

    // the address to sign with must be unlocked
    public function sign(Address $address, string $msgToSign)
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_sign', [$address->toString(), $msgToSign])
        );

        return $response->getRpcResult();
    }

    public function sendTransaction(Transaction $transaction): TransactionHash
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_sendTransaction', [$transaction->toArray()])
        );

        return new TransactionHash($response->getRpcResult());

    }

    public function sendRawTransaction(string $data): TransactionHash
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_sendRawTransaction', [$data])
        );

        return $response->getRpcResult();

    }

    public function call(Transaction $transaction, BlockNumber $blockNumber): string
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_call', [$transaction->toArray(), $blockNumber->toString()])
        );

        return $response->getRpcResult();
    }

    public function estimateGas(Transaction $transaction, BlockNumber $blockNumber): int
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_estimateGas', [$transaction->toArray(), $blockNumber->toString()])
        );

        return hexdec($response->getRpcResult());

    }

    public function getBlockByHash(BlockHash $hash, bool $expandTransactions = false): ?Block
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getBlockByHash', [$hash->toString(), $expandTransactions])
        );

        return ($response->getRpcResult()) ? new Block($response->getRpcResult()) : null;

    }

    public function getBlockByNumber(BlockNumber $blockNumber, bool $expandTransactions = false): ?Block
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getBlockByNumber', [$blockNumber->toString(), $expandTransactions])
        );

        return ($response->getRpcResult()) ? new Block($response->getRpcResult()) : null;

    }

    public function getTransactionByHash(TransactionHash $hash): ?TransactionInfo
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getTransactionByHash', [$hash->toString()])
        );

        return ($response->getRpcResult()) ? new TransactionInfo($response->getRpcResult()) : null;
    }

    public function getTransactionByBlockHashAndIndex(BlockHash $hash, int $index): ?TransactionInfo
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getTransactionByBlockHashAndIndex', [$hash->toString(), '0x'.dechex($index)])
        );

        return ($response->getRpcResult()) ? new TransactionInfo($response->getRpcResult()) : null;
    }

    public function getTransactionByBlockNumberAndIndex(BlockNumber $blockNumber, int $index): ?TransactionInfo
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getTransactionByBlockNumberAndIndex',
                [$blockNumber->toString(), '0x'.dechex($index)])
        );

        return ($response->getRpcResult()) ? new TransactionInfo($response->getRpcResult()) : null;

    }

    public function getTransactionReceipt(TransactionHash $hash): ?TransactionReceipt
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getTransactionReceipt', [$hash->toString()])
        );

        return ($response->getRpcResult()) ? new TransactionReceipt($response->getRpcResult()) : null;

    }

    public function getUncleByBlockHashAndIndex(BlockHash $hash, int $index): ?Block
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getUncleByBlockHashAndIndex', [$hash->toString(), $index])
        );

        return ($response->getRpcResult()) ? new Block($response->getRpcResult()) : null;

    }

    public function getUncleByBlockNumberAndIndex(BlockNumber $blockNumber, int $index): ?Block
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getUncleByBlockNumberAndIndex', [$blockNumber->toString(), $index])
        );

        return ($response->getRpcResult()) ? new Block($response->getRpcResult()) : null;

    }

    public function getCompilers(): array
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_getCompilers', [])
        );

        return ($response->getRpcResult()) ? $response->getRpcResult() : [];

    }

    public function compileSolidity(string $code): array
    {
        $response = $this->client->send(
            $this->client->request(1, 'eth_compileSolidity', [$code])
        );

        return ($response->getRpcResult()) ? $response->getRpcResult() : [];
    }

    // TODO: missing filter methods
}
