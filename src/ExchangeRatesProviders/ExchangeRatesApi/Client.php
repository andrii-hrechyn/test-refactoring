<?php

namespace App\ExchangeRatesProviders\ExchangeRatesApi;

use App\ExchangeRatesProviders\ExchangeRatesApi\Exceptions\ExchangeRatesApiException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Client
{
    protected string $url = 'https://api.exchangeratesapi.io/';
    protected GuzzleClient $client;

    public function __construct(string $apiKey)
    {
        $this->client = new GuzzleClient([
            'base_uri' => $this->url,
            'query'    => [
                'access_key' => $apiKey,
            ],
        ]);
    }

    public function send(string $method, string $path): array
    {
        try {
            $response = json_decode($this->client->request($method, $path)->getBody(), true);

            if (!$response['success']) {
                throw new ExchangeRatesApiException($response['error']['info']);
            }

            return $response;
        } catch (ClientException $exception) {
            throw new ExchangeRatesApiException($exception->getMessage(), previous: $exception);
        } catch (ServerException $exception) {
            throw new ExchangeRatesApiException('Service ExchangeRatesApi unavailable!', previous: $exception);
        }
    }
}