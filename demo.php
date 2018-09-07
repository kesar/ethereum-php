<?php 

use EthereumPHP\Ethereum;

include 'vendor/autoload.php';

$config = [
	'host' => 'http://service-ytf-test.meiyuankj.com', //以太坊节点地址
	'contract_address' => '0xf7d3320c4676d11d67338B766a9DF99996d19777',
	'abi' => $abi, //合约ERC20标准代币abi
	'bytecode' => $bytecode //合约bytecode
];

$ethereum = new Ethereum($config);

$name = $ethereum->contract_call('name');
echo $name . '<br>';
$symbol = $ethereum->contract_call('symbol');
echo $symbol . '<br>';
$total = $ethereum->contract_call('totalSupply');	
echo $total . '<br>';


$value = 100; //转出MKC

$from_address = '0x04091bdd5808b83229ab80fc06f2d7ef977d9e8e'; //转出账号
$to_address = '';


$balance = $ethereum->getTokenBalance($from_address);//获取用户token

//转出先解锁账号
$ethereum->unlockAccount($from_address, '密码', 1000);


$options = [
	'from' => $from_address,
	'gas' => '0x200b20', //交易手续费
];
//代币转账
$ethereum->contract_send('transfer', $to_address, $value, $options);
