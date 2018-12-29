<?php

/**
 * This file is part of web3.php package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author  Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace EthereumPHP\Formatters;

interface IFormatter
{
    /**
     * format
     *
     * @param mixed $value
     *
     * @return string
     */
    public static function format($value);
}