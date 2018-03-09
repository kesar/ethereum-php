# Ethereum Client in PHP

Examples:

```php
<?php
use EthereumPHP\EthereumClient;
use EthereumPHP\Types\BlockHash;
use EthereumPHP\Types\BlockNumber;

include 'vendor/autoload.php';

$randomAddress = new \EthereumPHP\Types\Address('0x7eff122b94897ea5b0e2a9abf47b86337fafebdc');
$randomHash = '0xb903239f8543d04b5dc1ba6579132b143087c68db1b2168786408fcbce568238';

$client = new EthereumClient('http://localhost:8545');

// net
echo $client->net()->version() , PHP_EOL;
echo $client->net()->listening() , PHP_EOL;
echo $client->net()->peerCount() , PHP_EOL;


// web3
echo $client->web3()->clientVersion() , PHP_EOL;
echo $client->web3()->sha3('0x68656c6c6f20776f726c64') , PHP_EOL;
echo $client->eth()->protocolVersion() , PHP_EOL;
echo $client->eth()->syncing() , PHP_EOL;

// eth
$coinbase = $client->eth()->coinbase();
if ($coinbase) {
    echo $coinbase->toString() , PHP_EOL;
}
echo $client->eth()->mining() , PHP_EOL;
echo $client->eth()->hashRate() , PHP_EOL;
echo $client->eth()->gasPrice()->toEther() , PHP_EOL;
foreach ($client->eth()->accounts() as $account) {
    echo $account->toString() , PHP_EOL;
}

echo $client->eth()->blockNumber() , PHP_EOL;
echo $client->eth()->getBalance($randomAddress, new BlockNumber())->toEther() , PHP_EOL;
echo $client->eth()->getTransactionCount($randomAddress, new BlockNumber()) , PHP_EOL;
echo $client->eth()->getBlockTransactionCountByHash(new BlockHash($randomHash)) , PHP_EOL;
echo $client->eth()->getUncleCountByBlockHash(new BlockHash($randomHash)) , PHP_EOL;
echo $client->eth()->getUncleCountByBlockNumber(new BlockNumber()) , PHP_EOL;
echo $client->eth()->getCode($randomAddress, new BlockNumber()) , PHP_EOL;
echo $client->eth()->sign($randomAddress, '0xdeadbeaf') , PHP_EOL;
foreach ($client->eth()->getCompilers() as $compiler) {
    echo $compiler , PHP_EOL;
}
print_r($client->eth()->compileSolidity('contract test { function multiply(uint a) returns(uint d) {   return a * 7;   } }"'));


// management: personal
foreach ($client->personal()->listAccounts() as $account) {
    echo $account->toString() , PHP_EOL;
}
$account = $client->personal()->newAccount('test');
echo $account->toString() , PHP_EOL;
echo $client->personal()->unlockAccount($account, 'test', 20) , PHP_EOL;
```
