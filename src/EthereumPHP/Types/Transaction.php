<?php

namespace EthereumPHP\Types;

class Transaction
{
    private $from;
    private $to;
    private $data;
    private $gas;
    private $gasPrice;
    private $value;
    private $nonce;

    /**
     * Transaction constructor.
     * @param Address $from
     * @param Address $to
     * @param string|null $data
     * @param int|null $gas
     * @param Wei|null $gasPrice
     * @param int|string|null $value
     * @param int|null $nonce
     */
    public function __construct(
        Address $from,
        Address $to,
        string $data = null,
        int $gas = null,
        Wei $gasPrice = null,
        $value = null,
        int $nonce = null
    ) {
        $this->from = $from;
        $this->to = $to;
        $this->data = $data;
        $this->gas = $gas;
        $this->gasPrice = $gasPrice;
        $this->value = $value;
        $this->nonce = $nonce;
    }

    public function toArray(): array
    {
        $transaction = [
            'from' => $this->from->toString(),
            'to' => $this->to->toString(),
        ];

        if (!is_null($this->data)) {
            $transaction['data'] = '0x'.dechex($this->data);
        }

        if (!is_null($this->gas)) {
            $transaction['gas'] = '0x'.dechex($this->gas);
        }

        if (!is_null($this->gasPrice)) {
            $transaction['gasPrice'] = '0x'.dechex($this->gasPrice->amount());
        }

        if (!is_null($this->value)) {
            $transaction['value'] = '0x'.\Phlib\base_convert($this->value, 10, 16);
        }

        if (!is_null($this->nonce)) {
            $transaction['nonce'] = '0x'.dechex($this->nonce);
        }

        return $transaction;
    }
}
