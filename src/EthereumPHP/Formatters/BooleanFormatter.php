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

class BooleanFormatter implements IFormatter
{
    /**
     * format
     * 
     * @param mixed $value
     * @return bool
     */
    public static function format($value)
    {
        return (bool) $value;
    }
}