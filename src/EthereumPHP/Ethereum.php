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
    
    //Ethereum RPC Address
    protected $ethereum_host;
    
    //Tokens are given to the address
    protected $contract_address;
    
    //abi
    protected $abi;
    
    //bytecode
    protected $bytecode;
    
    /**
     * Ethereum constructor.
     *
     * @param array $ethereum_config
     *
     * @throws \Exception
     */
    public function __construct($ethereum_config = [])
    {
        if (empty($ethereum_config)) {
            throw new \Exception('Configuration cannot be empty');
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
     * @return Methods\Eth
     */
    public function getEth()
    {
        return $this->client->eth();
    }
    
    /**
     * @return Methods\Personal
     */
    public function getPersonal()
    {
        return $this->client->personal();
    }
    
    /**
     * @param string $account_key
     *
     * @return string
     */
    public function addAccount($account_key = '')
    {
        $account = $this->client->personal()->newAccount($account_key);
        
        return $account->toString();
    }
    
    /**
     * @param $account
     * @param $account_key
     * @param $time
     *
     * @return bool
     */
    public function unlockAccount($account, $account_key, $time)
    {
        $account = new Address($account);
        
        return $this->client->personal()->unlockAccount($account, $account_key, $time);
    }
    
    /**
     * @param $account
     *
     * @return Types\Wei
     * @throws \ErrorException
     */
    public function getBalance($account)
    {
        $account = new Address($account);
        
        return $this->client->eth()->getBalance($account, new BlockNumber());
    }
    
    /**
     * @param      $from
     * @param      $to
     * @param      $value
     * @param null $data
     *
     * @return TransactionHash
     * @throws \ErrorException
     */
    public function sendTransaction($from, $to, $value, $data = null)
    {
        $from = new Address($from);
        $to = new Address($to);
        $transaction = new Transaction($from, $to, $data, null, null, $value);
        
        return $this->client->eth()->sendTransaction($transaction);
    }
    
    /**
     * @param $hash
     *
     * @return Types\TransactionInfo|null
     */
    public function getTransactionByHash($hash)
    {
        $hash = new TransactionHash($hash);
        
        return $this->client->eth()->getTransactionByHash($hash);
    }
    
    /**
     * @param       $method
     * @param mixed ...$params
     *
     * @return mixed
     * @throws \Exception
     */
    public function contractCall($method, ...$params)
    {
        $contract = $this->client->contract($this->abi);
        
        return $contract->at($this->contract_address)->call($method, ...$params);
    }
    
    /**
     * @param       $method
     * @param mixed ...$params
     *
     * @throws \Exception
     */
    public function contractSend($method, ...$params)
    {
        $contract = $this->client->contract($this->abi);
        
        return $contract->at($this->contract_address)->send($method, ...$params);
    }
    
    /**
     * @param       $deploy_account
     * @param mixed ...$params
     *
     * @return TransactionHash
     * @throws \Exception
     */
    public function deployContract($deploy_account, ...$params)
    {
        $contract = $this->client->contract($this->abi);
        
        return $contract->from($deploy_account)->bytecode($this->bytecode)->new(...$params);
    }
    
    /**
     * @param $account
     *
     * @return mixed
     * @throws \Exception
     */
    public function getTokenBalance($account)
    {
        return $this->contract_call('balanceOf', $account);
    }
}