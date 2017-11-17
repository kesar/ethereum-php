<?php

use EthereumPHP\EthereumClient;

include 'vendor/autoload.php';

$myAddress = new \EthereumPHP\Types\Address('0x2e94757df1267f244f4b9ef049416c6794a60552');
$otherAddress = new \EthereumPHP\Types\Address('0x22c5071a37432ac845a57c4a69339d161b6baa22');

$client = new EthereumClient('http://localhost:8545');

$ether = new \EthereumPHP\Types\Ether(1);
$ether2 = new \EthereumPHP\Types\Ether(1);
echo $ether->toWei()->amount() - $ether2->toWei()->amount();
/*
$lastBlock = $client->eth()->blockNumber();
for ($i = 0; $i <= $lastBlock; $i++) {
    echo 'checking block: '.$i."\n";
    $block = $client->eth()->getBlockByNumber(new \EthereumPHP\Types\BlockNumber($i));
    if (count($block->transactions()) > 0) {
        foreach ($block->transactions() as $transaction) {
            echo $transaction."\n";
        }
    }
}
*/
//$transaction = $client->eth()->getTransactionByHash(new \EthereumPHP\Types\TransactionHash('0xba045379c4bae068f56ae656d8f54ad53d303588307bd272a969d7479794a61a'));
//print_r($transaction);
/*
echo 'My address: '. $client->eth()->getBalance($myAddress, new BlockNumber())->toEther()."\n";
echo 'Other address: '. $client->eth()->getBalance($otherAddress, new BlockNumber())->toEther()."\n";
echo 'Unlocking my account: '. $client->personal()->unlockAccount($myAddress, 'test', 20)."\n";

$transaction = new \EthereumPHP\Types\Transaction(
    $myAddress,
    $otherAddress,
    null,
    null,
    null,
    (new \EthereumPHP\Types\Ether(5))->toWei()->amount()
);
echo 'Sending transaction tx: '. $client->eth()->sendTransaction($transaction)->toString();
*/

/*
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
*/