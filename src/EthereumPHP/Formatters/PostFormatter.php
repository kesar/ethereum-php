<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace EthereumPHP\Formatters;

use InvalidArgumentException;
use EthereumPHP\Utils;
use EthereumPHP\Formatters\IFormatter;
use EthereumPHP\Formatters\QuantityFormatter;

class PostFormatter implements IFormatter
{
    /**
     * format
     * 
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        if (isset($value['priority'])) {
            $value['priority'] = QuantityFormatter::format($value['priority']);
        }
        if (isset($value['ttl'])) {
            $value['ttl'] = QuantityFormatter::format($value['ttl']);
        }
        return $value;
    }
}