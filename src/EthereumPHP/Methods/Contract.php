<?php 

namespace EthereumPHP\Methods;

use EthereumPHP\Contracts\Ethabi;
use EthereumPHP\Contracts\Types\Address;
use EthereumPHP\Contracts\Types\Boolean;
use EthereumPHP\Contracts\Types\Bytes;
use EthereumPHP\Contracts\Types\Integer;
use EthereumPHP\Contracts\Types\Str;
use EthereumPHP\Contracts\Types\Uinteger;
use EthereumPHP\Types\TransactionHash;
use EthereumPHP\Utils;
use Graze\GuzzleHttp\JsonRpc\Client;
use Graze\GuzzleHttp\JsonRpc\ClientInterface;
use kornrunner\Keccak;
use phpseclib\Math\BigInteger as BigNumber;

/**
 * Ethereum contract
 */
class Contract extends AbstractMethods
{
    protected $abi;

	protected $ethabi;

	protected $functions = [];

	protected $constructor = [];

	protected $events = [];

	protected $address;

	protected $from_address;

	const SHA3_NULL_HASH = 'c5d2460186f7233c927e7db2dcc703c0e500b653ca82273b7bfad8045d85a470';

    public function abi(array $abi)
    {
        foreach ($abi as $item) {
            if (isset($item['type'])) {
                if ($item['type'] === 'function') {
                    $this->functions[$item['name']] = $item;
                } elseif ($item['type'] === 'constructor') {
                    $this->constructor = $item;
                } elseif ($item['type'] === 'event') {
                    $this->events[$item['name']] = $item;
                }
            }
        }

        $this->abi = $abi;

        $this->ethabi = new Ethabi([
            'address' => new Address,
            'bool' => new Boolean,
            'bytes' => new Bytes,
            'int' => new Integer,
            'string' => new Str,
            'uint' => new Uinteger,
        ]);

        return $this;
    }

	public function at(string $address)
	{
		$this->address = $address;
        return $this;
	}

	public function from(string $address)
	{
		$this->from_address = $address;
        return $this;
	}

    public function bytecode(string $bytecode)
    {
        $this->bytecode = str_replace('0x', '', $bytecode);
        return $this;
    }

    public function new()
    {
        if (isset($this->constructor)) {
            $constructor = $this->constructor;
            $arguments = func_get_args();

            if (count($arguments) < count($constructor['inputs'])) {
                throw new \Exception('Please make sure you have put all constructor params and callback.');
            }
            if (!isset($this->bytecode)) {
                throw new \Exception('Please call bytecode($bytecode) before new().');
            }

            $params = array_splice($arguments, 0, count($constructor['inputs']));

            $data = $this->ethabi->encodeParameters($constructor, $params);
            $transaction = [];

            if (count($arguments) > 0) {
                $transaction = $arguments[0];
            }

            $transaction['data'] = '0x' . $this->bytecode . Utils::stripZero($data);

            $response = $this->client->send(
                $this->client->request(1, 'eth_sendTransaction', [$transaction])
            );

            if ($response->getRpcErrorCode()) {
                throw new \Exception($response->getRpcErrorMessage());
            }

            return new TransactionHash($response->getRpcResult());
        }
    }

	public function call()
    {
        if (isset($this->functions)) {
            $arguments = func_get_args();
            $method = array_splice($arguments, 0, 1)[0];

            if (!is_string($method) && !isset($this->functions[$method])) {
                throw new \Exception('Please make sure the method is existed.');
            }

            $function = $this->functions[$method];

            if (count($arguments) < count($function['inputs'])) {
                throw new \Exception('Please make sure you have put all function params and callback.');
            }

            $params = array_splice($arguments, 0, count($function['inputs']));

            $data = $this->ethabi->encodeParameters($function, $params);
            $functionName = Utils::jsonMethodToString($function);
            $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);
            
            $transaction = [];
            if (count($arguments) > 0) {
                $transaction = $arguments[0];
            }

            $transaction['to'] = strtolower($this->address);
            $transaction['data'] = $functionSignature . Utils::stripZero($data);

            $response = $this->client->send(
	            $this->client->request(1, 'eth_call', [$transaction, 'latest'])
	        );

            return $this->ethabi->decodeParameters($function, $response->getRpcResult());
        }
    }

    /**
     * send
     * Send function method.
     * 
     * @param mixed
     * @return void
     */
    public function send()
    {
        if (isset($this->functions)) {
            $arguments = func_get_args();
            $method = array_splice($arguments, 0, 1)[0];
            
            if (!is_string($method) && !isset($this->functions[$method])) {
                throw new \Exception('Please make sure the method is existed.');
            }
            $function = $this->functions[$method];

            if (count($arguments) < count($function['inputs'])) {
                throw new \Exception('Please make sure you have put all function params and callback.');
            }
            
            $params = array_splice($arguments, 0, count($function['inputs']));
            $data = $this->ethabi->encodeParameters($function, $params);
            $functionName = Utils::jsonMethodToString($function);
            $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);
            $transaction = [];

            if (count($arguments) > 0) {
                $transaction = $arguments[0];
            }
            $transaction['to'] = strtolower($this->address);
            $transaction['data'] = $functionSignature . Utils::stripZero($data);

            $response = $this->client->send(
	            $this->client->request(1, 'eth_sendTransaction', [$transaction])
	        );

            if ($response->getRpcErrorCode()) {
                throw new \Exception($response->getRpcErrorMessage());
            }

	        return new TransactionHash($response->getRpcResult());
        }
    }
}