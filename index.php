<?php

use EthereumPHP\EthereumClient;
use EthereumPHP\Types\BlockHash;
use EthereumPHP\Types\BlockNumber;

include 'vendor/autoload.php';

$randomAddress = new \EthereumPHP\Types\Address('0x7eff122b94897ea5b0e2a9abf47b86337fafebdc');
$randomHash = '0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238';

$client = new EthereumClient('http://localhost:8545');
echo $client->net()->version()."\n";
echo $client->net()->listening()."\n";
echo $client->net()->peerCount()."\n";
echo $client->web3()->clientVersion()."\n";
echo $client->web3()->sha3('0x68656c6c6f20776f726c64')."\n";
echo $client->eth()->protocolVersion()."\n";
echo $client->eth()->syncing()."\n";
$coinbase = $client->eth()->coinbase();
if ($coinbase) {
    echo $coinbase->toString()."\n";
}
echo $client->eth()->mining()."\n";
echo $client->eth()->hashRate()."\n";
echo $client->eth()->gasPrice()->toEther()."\n";
foreach ($client->eth()->accounts() as $account) {
    echo $account->toString()."\n";
}
echo $client->eth()->blockNumber()."\n";
echo $client->eth()->getBalance($randomAddress, new BlockNumber())->toEther()."\n";
echo $client->eth()->getTransactionCount($randomAddress, new BlockNumber())."\n";
echo $client->eth()->getBlockTransactionCountByHash(new BlockHash($randomHash))."\n";
echo $client->eth()->getUncleCountByBlockHash(new BlockHash($randomHash))."\n";
echo $client->eth()->getUncleCountByBlockNumber(new BlockNumber())."\n";
echo $client->eth()->getCode($randomAddress, new BlockNumber())."\n";
echo $client->eth()->sign($randomAddress, '0xdeadbeaf')."\n";
foreach ($client->eth()->getCompilers() as $compiler) {
    echo $compiler."\n";
}
print_r($client->eth()->compileSolidity('contract test { function multiply(uint a) returns(uint d) {   return a * 7;   } }"'));

foreach ($client->personal()->listAccounts() as $account) {
    echo $account->toString()."\n";
}

$account = $client->personal()->newAccount('test');
echo $account->toString()."\n";
echo $client->personal()->unlockAccount($account, 'test', 20)."\n";
