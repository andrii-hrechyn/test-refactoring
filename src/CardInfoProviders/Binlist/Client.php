<?php

namespace App\CardInfoProviders\Binlist;

use App\CardInfoProviders\Binlist\Exceptions\BinlistException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Client
{
    protected string $url = 'https://lookup.binlist.net/';
    protected GuzzleClient $client;

    public function __construct()
    {
        $this->client = new GuzzleClient(['base_uri' => $this->url]);
    }

    public function send(string $method, string $path): array
    {
        try {
            return json_decode($this->client->request($method, $path)->getBody(), true);
        } catch (ClientException $exception) {
            throw new BinlistException($exception->getMessage());
        } catch (ServerException $exception) {
            throw new BinlistException('Service BinList unavailable!', previous: $exception);
        }
    }
}