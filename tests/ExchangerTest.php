<?php

namespace App\Tests;

use App\Contracts\ExchangeRatesProvider;
use App\Exceptions\ExchangeException;
use App\Exchanger;
use PHPUnit\Framework\TestCase;

class ExchangerTest extends TestCase
{
    public function testExchangeWithValidRate()
    {
        $exchangeRatesProviderMock = $this->createMock(ExchangeRatesProvider::class);
        $exchangeRatesProviderMock->method('get')->willReturn([
            'USD' => 1.2,
        ]);

        $exchanger = new Exchanger($exchangeRatesProviderMock);
        $amount = 120;
        $currency = 'USD';

        $result = $exchanger->exchange($amount, $currency);

        $this->assertEquals(100, $result);
    }

    public function testExchangeWithZeroRate()
    {
        $exchangeRatesProviderMock = $this->createMock(ExchangeRatesProvider::class);
        $exchangeRatesProviderMock->method('get')->willReturn([
            'EUR' => 0.0,
        ]);

        $exchanger = new Exchanger($exchangeRatesProviderMock);
        $amount = 120;
        $currency = 'EUR';

        $result = $exchanger->exchange($amount, $currency);

        $this->assertEquals(120, $result);
    }

    public function testExchangeWithNoRate()
    {
        $exchangeRatesProviderMock = $this->createMock(ExchangeRatesProvider::class);
        $exchangeRatesProviderMock->method('get')->willReturn([]);

        $exchanger = new Exchanger($exchangeRatesProviderMock);
        $amount = 120;
        $currency = 'USD';

        $this->expectException(ExchangeException::class);
        $exchanger->exchange($amount, $currency);
    }
}
