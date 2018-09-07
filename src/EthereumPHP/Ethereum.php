<?php 

namespace EthereumPHP;

use EthereumPHP\EthereumClient;
use EthereumPHP\Types\Address;
use EthereumPHP\Types\BlockHash;
use EthereumPHP\Types\BlockNumber;
use EthereumPHP\Types\Transaction;
use EthereumPHP\Types\TransactionHash;

/**
* 以太坊模型
*/
class Ethereum
{
	protected $client;

	//以太坊RPC地址
	protected $ethereum_host;

	//代币拿给地址
	protected $contract_address;

	//abi
	protected $abi;

	//bytecode
	protected $bytecode;

	/**
	 * 
	 * 
	 * 
	 */ 
	public function __construct($ethereum_config = [])
	{
		if (empty($ethereum_config)) {
			throw new \Exception('配置不能为空');
		}

		$client_config = [
			'timeout' => 3.0
		];

		$this->client = new EthereumClient($ethereum_config['host'], $client_config);

		if (isset($ethereum_config['contract_address'])) {
			$this->contract_address = $ethereum_config['contract_address'];	
		}

		if (isset($ethereum_config['abi'])) {
			$this->abi = $ethereum_config['abi'];
		}

		if (isset($ethereum_config['bytecode'])) {
			$this->bytecode = $ethereum_config['bytecode'];	
		}

		$this->ethereum_host = $ethereum_config['host'];
	}

	/**
	 * [getEth 获取eth方法]
	 * @Author   Jason
	 * @DateTime 2018-03-22T14:55:35+0800
	 * @return   [type]                   [description]
	 */
	public function getEth()
	{
		return $this->client->eth();
	}

	/**
	 * [getPersonal 获取personal方法]
	 * @Author   Jason
	 * @DateTime 2018-03-22T14:56:01+0800
	 * @return   [type]                   [description]
	 */
	public function getPersonal()
	{
		return $this->client->personal();
	}

	/**
	 * [addAccount 创建以太坊账户]
	 * @Author   Jason
	 * @DateTime 2018-03-21T15:04:53+0800
	 * @param    string                   $account_key [description]
	 */
	public function addAccount($account_key = '')
	{
		$account = $this->client->personal()->newAccount($account_key); 

		return $account->toString();
	}

	/**
	 * [unlockAccount 解锁账户]
	 * @Author   Jason
	 * @DateTime 2018-03-21T15:20:07+0800
	 * @param    [type]                   $account     [description]
	 * @param    [type]                   $account_key [description]
	 * @param    [type]                   $time        [description]
	 * @return   [type]                                [description]
	 */
	public function unlockAccount($account, $account_key, $time)
	{
		$account = new Address($account);
		return $this->client->personal()->unlockAccount($account, $account_key, $time);
	}

	/**
	 * [getBalance 获取账户余额]
	 * @Author   Jason
	 * @DateTime 2018-03-21T15:43:37+0800
	 * @param    [type]                   $account [description]
	 * @return   [type]                            [description]
	 */
	public function getBalance($account)
	{
		$account = new Address($account);
		return $this->client->eth()->getBalance($account, new BlockNumber());
	}

	/**
	 * [sendTransaction 发送交易]
	 * @Author   Jason
	 * @DateTime 2018-03-21T23:31:22+0800
	 * @param    [type]                   $from  [description]
	 * @param    [type]                   $to    [description]
	 * @param    [type]                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function sendTransaction($from, $to, $value, $data = null)
	{
		$from = new Address($from);
		$to = new Address($to);
		$transaction = new Transaction($from, $to, $data, null, null, $value);

		return $this->client->eth()->sendTransaction($transaction);
	}

	/**
	 * [getTransactionByHash 获取块信息]
	 * @Author   Jason
	 * @DateTime 2018-03-21T23:16:59+0800
	 * @param    [type]                   $hash [description]
	 * @return   [type]                         [description]
	 */
	public function getTransactionByHash($hash)
	{
		$hash = new TransactionHash($hash);

		return $this->client->eth()->getTransactionByHash($hash);
	}

	/**
	 * [contract_call 调用智能合约的方法]
	 * @Author   Jason
	 * @DateTime 2018-03-21T21:22:56+0800
	 * @param    [type]                   $abi              [description]
	 * @param    [type]                   $contract_address [description]
	 * @param    [type]                   $method           [description]
	 * @param    [type]                   $params           [description]
	 * @return   [type]                                     [description]
	 */
	public function contract_call($method, ...$params)
	{
		$contract = $this->client->contract($this->abi);
		
		return $contract->at($this->contract_address)->call($method, ...$params);
	}

	/**
	 * [contract_send 发送合约代币交易]
	 * @Author   Jason
	 * @DateTime 2018-03-21T22:15:55+0800
	 * @param    [type]                   $abi              [description]
	 * @param    [type]                   $contract_address [description]
	 * @param    [type]                   $method           [description]
	 * @param    [type]                   $params           [description]
	 * @return   [type]                                     [description]
	 */
	public function contract_send($method, ...$params)
	{
		$contract = $this->client->contract($this->abi);
		
		return $contract->at($this->contract_address)->send($method, ...$params);
	}

	/**
	 * [deploy_contract 部署合约]
	 * @Author   Jason
	 * @DateTime 2018-03-22T15:59:10+0800
	 * @return   [type]                   [description]
	 */
	public function deploy_contract($deploy_account, ...$params)
	{
		$contract = $this->client->contract($this->abi);

		return $contract->from($deploy_account)->bytecode($this->bytecode)->new(...$params);
	}

	/**
	 * [getTokenBalance 获取代币余额]
	 * @Author   Jason
	 * @DateTime 2018-03-21T22:16:31+0800
	 * @param    [type]                   $account [description]
	 * @return   [type]                            [description]
	 */
	public function getTokenBalance($account)
	{
		return $this->contract_call('balanceOf', $account);
	}
}