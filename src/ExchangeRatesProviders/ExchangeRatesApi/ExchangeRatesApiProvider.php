<?php

namespace App\ExchangeRatesProviders\ExchangeRatesApi;

use App\Contracts\ExchangeRatesProvider;

class ExchangeRatesApiProvider implements ExchangeRatesProvider
{
    protected Client $client;

    public function __construct(string $apiKey)
    {
        $this->client = new Client($apiKey);
    }

    public function get(): array
    {
        $response = $this->client->send('GET', '/latest');

        return $response['rates'];
    }
}