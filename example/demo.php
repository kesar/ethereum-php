<?php

use EthereumPHP\Ethereum;

include 'vendor/autoload.php';

$config = [
    'host' => 'http://service-ytf-test.meiyuankj.com', //Ethereum node address
    'contract_address' => '0xf7d3320c4676d11d67338B766a9DF99996d19777',
    'abi' => $abi, //Contract ERC20 standard token abi
    'bytecode' => $bytecode //Contract bytecode
];

$ethereum = new Ethereum($config);

$name = $ethereum->contractCall('name');
echo $name . '<br>';
$symbol = $ethereum->contractCall('symbol');
echo $symbol . '<br>';
$total = $ethereum->contractCall('totalSupply');
echo $total . '<br>';


$value = 100; //Transfer out MKC

$from_address = '0x04091bdd5808b83229ab80fc06f2d7ef977d9e8e'; //转出账号
$to_address = '';


$balance = $ethereum->getTokenBalance($from_address);//获取用户token

//Transfer out to unlock the account first
$ethereum->unlockAccount($from_address, '密码', 1000);


$options = [
    'from' => $from_address,
    'gas' => '0x200b20', //Transaction Fees
];
//Token transfer
$ethereum->contractSend('transfer', $to_address, $value, $options);
