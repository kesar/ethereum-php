<?php

function ethereum_php_bcdechex($dec) {
    $hex = '';
    do {
        $last = bcmod($dec, 16);
        $hex = dechex($last).$hex;
        $dec = bcdiv(bcsub($dec, $last), 16);
    } while ($dec > 0);

    return $hex;
}