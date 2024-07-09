<?php

namespace App\CardInfoProviders\Binlist;

use App\Card;
use App\CardInfoProviders\Binlist\Exceptions\BinlistException;
use App\Contracts\CardInfoProvider;

class BinlistCardInfoProvider implements CardInfoProvider
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function get(int $binNumber): Card
    {
        if (strlen((string) $binNumber) < 6) {
            throw new BinlistException('BIN number is invalid');
        }

        $response = $this->client->send('GET', $binNumber);

        return new Card($response['country']['alpha2']);
    }
}