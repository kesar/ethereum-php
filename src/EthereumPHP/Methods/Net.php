<?php

namespace EthereumPHP\Methods;

class Net extends AbstractMethods
{
    public function version(): string
    {
        $response = $this->client->send(
            $this->client->request(67, 'net_version', [])
        );

        return $response->getRpcResult();
    }

    public function listening(): bool
    {
        $response = $this->client->send(
            $this->client->request(67, 'net_listening', [])
        );

        return (bool)$response->getRpcResult();
    }

    public function peerCount(): int
    {
        $response = $this->client->send(
            $this->client->request(67, 'net_listening', [])
        );

        return hexdec($response->getRpcResult());
    }
}
