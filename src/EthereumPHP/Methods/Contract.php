<?php 

namespace EthereumPHP\Methods;

use EthereumPHP\Types\TransactionHash;
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

            $data = $this->encodeParam($constructor['inputs'], $params);
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
            $data = $this->encodeParam($function['inputs'], $params);
            
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

    protected function encodeParam($types, $params)
    {
    	$ps = [];
        if (empty($params)) {
            $ps[] = $this->format('');
        } else {
            foreach ($params as $key => $value) {

                if ($types[$key]['type'] == 'string') {
                    $ps[] = $this->stringFormat($value, $types[$key]['type']);
                } else {
                    if ((strpos($value, '0x') === 0)) {
                        $value = str_replace('0x', '', $value);
                    }

                    $ps[] = $this->format($value);    
                }
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
            $data = $this->encodeParam($function['inputs'], $params);
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
     * toBn
     * Change number or number string to bignumber.
     * 
     * @param BigNumber|string|int $number
     * @return array|\phpseclib\Math\BigInteger
     */
    public static function toBn($number)
    {
        if ($number instanceof BigNumber){
            $bn = $number;
        } elseif (is_int($number)) {
            $bn = new BigNumber($number);
        } elseif (is_numeric($number)) {
            $number = (string) $number;

            if (self::isNegative($number)) {
                $count = 1;
                $number = str_replace('-', '', $number, $count);
                $negative1 = new BigNumber(-1);
            }
            if (strpos($number, '.') > 0) {
                $comps = explode('.', $number);

                if (count($comps) > 2) {
                    throw new \Exception('toBn number must be a valid number.');
                }
                $whole = $comps[0];
                $fraction = $comps[1];

                return [
                    new BigNumber($whole),
                    new BigNumber($fraction),
                    isset($negative1) ? $negative1 : false
                ];
            } else {
                $bn = new BigNumber($number);
            }
            if (isset($negative1)) {
                $bn = $bn->multiply($negative1);
            }
        } elseif (is_string($number)) {
            $number = mb_strtolower($number);

            if (self::isNegative($number)) {
                $count = 1;
                $number = str_replace('-', '', $number, $count);
                $negative1 = new BigNumber(-1);
            }
            if (self::isZeroPrefixed($number) || preg_match('/[a-f]+/', $number) === 1) {
                $number = self::stripZero($number);
                $bn = new BigNumber($number, 16);
            } elseif (empty($number)) {
                $bn = new BigNumber(0);
            } else {
                throw new \Exception('toBn number must be valid hex string.');
            }
            if (isset($negative1)) {
                $bn = $bn->multiply($negative1);
            }
        } else {
            throw new \Exception('toBn number must be BigNumber, string or int.');
        }
        return $bn;
    }


    /**
     * isNegative
     * 
     * @param string
     * @return bool
     */
    public static function isNegative($value)
    {
        if (!is_string($value)) {
            throw new \Exception('The value to isNegative function must be string.');
        }
        return (strpos($value, '-') === 0);
    }

    /**
     * stripZero
     * 
     * @param string $value
     * @return string
     */
    public static function stripZero($value)
    {
        if (self::isZeroPrefixed($value)) {
            $count = 1;
            return str_replace('0x', '', $value, $count);
        }
        return $value;
    }

    /**
     * isZeroPrefixed
     * 
     * @param string
     * @return bool
     */
    public static function isZeroPrefixed($value)
    {
        if (!is_string($value)) {
            throw new \Exception('The value to isZeroPrefixed function must be string.');
        }
        return (strpos($value, '0x') === 0);
    }

    /**
     * toHex
     * Encoding string or integer or numeric string(is not zero prefixed) or big number to hex.
     * 
     * @param string|int|BigNumber $value
     * @param bool $isPrefix
     * @return string
     */
    public static function toHex($value, $isPrefix=false)
    {
        if (is_numeric($value)) {
            // turn to hex number
            $bn = self::toBn($value);
            $hex = $bn->toHex(true);
            $hex = preg_replace('/^0+(?!$)/', '', $hex);
        } elseif (is_string($value)) {
            $value = self::stripZero($value);
            $hex = implode('', unpack('H*', $value));
        } elseif ($value instanceof BigNumber) {
            $hex = $value->toHex(true);
            $hex = preg_replace('/^0+(?!$)/', '', $hex);
        } else {
            throw new \Exception('The value to toHex function is not support.');
        }
        if ($isPrefix) {
            return '0x' . $hex;
        }
        return $hex;
    }

    /**
     * stringFormat
     * 
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function stringFormat($value, $name)
    {
        $value = self::toHex($value);
        $prefix = $this->format(mb_strlen($value) / 2);
        $l = floor((mb_strlen($value) + 63) / 64);
        $padding = (($l * 64 - mb_strlen($value) + 1) >= 0) ? $l * 64 - mb_strlen($value) : 0;

        return $prefix . $value . implode('', array_fill(0, $padding, '0'));
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