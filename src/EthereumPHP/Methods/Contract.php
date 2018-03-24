<?php 

namespace EthereumPHP\Methods;

use EthereumPHP\Types\TransactionHash;
use Graze\GuzzleHttp\JsonRpc\Client;
use Graze\GuzzleHttp\JsonRpc\ClientInterface;
use kornrunner\Keccak;

/**
 * Ethereum contract
 */
class Contract extends AbstractMethods
{
	protected $abi;

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
            $data = $this->encodeParam($params);
            $transaction = [];

            if (count($arguments) > 0) {
                $transaction = $arguments[0];
            }
            $transaction['data'] = '0x' . $this->bytecode . $data;

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

            $functionSignature = $this->encodeFunctionSignature($function);
            $data = $this->encodeParam($params);

            $transaction = [];
            if (count($arguments) > 0) {
                $transaction = $arguments[0];
            }

            $transaction['to'] = strtolower($this->address);
            $transaction['data'] = $functionSignature . $data;

            $response = $this->client->send(
	            $this->client->request(1, 'eth_call', [$transaction, 'latest'])
	        );

	        return $response->getRpcResult();
        }
    }

    protected function encodeFunctionSignature($function)
    {
    	$functionName = $function['name'];
        $functionInputTypes = [];
        foreach ($function['inputs'] as $key => $p) {
        	$functionInputTypes[] = $p['type'];
        }

        $functionName = $functionName . '('. implode(',', $functionInputTypes) .')';

        return mb_substr(self::sha3($functionName), 0, 10);
    }

    protected function encodeParam($params)
    {
    	$ps = [];
        if (empty($params)) {
            $ps[] = $this->format('');
        } else {
            foreach ($params as $key => $value) {
                if ((strpos($value, '0x') === 0)) {
                    $value = str_replace('0x', '', $value);
                }

                $ps[] = $this->format($value);
            }
        }

        return implode('', $ps);
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
            $functionSignature = $this->encodeFunctionSignature($function);
            $data = $this->encodeParam($params);
            $transaction = [];

            if (count($arguments) > 0) {
                $transaction = $arguments[0];
            }
            $transaction['to'] = strtolower($this->address);
            $transaction['data'] = $functionSignature . $data;

            $response = $this->client->send(
	            $this->client->request(1, 'eth_sendTransaction', [$transaction])
	        );

            if ($response->getRpcErrorCode()) {
                throw new \Exception($response->getRpcErrorMessage());
            }

	        return new TransactionHash($response->getRpcResult());
        }
    }

    /**
     * sha3
     * keccak256
     * 
     * @param string $value
     * @return string
     */
    public static function sha3($value)
    {
        if (!is_string($value)) {
            throw new \Exception('The value to sha3 function must be string.');
        }
        if (strpos($value, '0x') === 0) {
            $value = self::hexToBin($value);
        }
        $hash = Keccak::hash($value, 256);

        if ($hash === self::SHA3_NULL_HASH) {
            return null;
        }
        return '0x' . $hash;
    }

    /**
     * hexToBin
     * 
     * @param string
     * @return string
     */
    public static function hexToBin($value)
    {
        if (!is_string($value)) {
            throw new \Exception('The value to hexToBin function must be string.');
        }
        if ((strpos($value, '0x') === 0)) {
            $count = 1;
            $value = str_replace('0x', '', $value, $count);
        }
        return pack('H*', $value);
    }

    public function format($value)
    {
        $value = (string) $value;
        $digit = 64;

        $bnHex = $value;
        $padded = mb_substr($bnHex, 0, 1);

        if ($padded !== 'f') {
            $padded = '0';
        }        
        return implode('', array_fill(0, $digit-mb_strlen($bnHex), $padded)) . $bnHex;
    }
}