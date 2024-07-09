<?php

namespace App;

use App\Contracts\ExchangeRatesProvider;
use App\Exceptions\ExchangeException;

class Exchanger
{
    protected array|null $exchangeRates = null;

    public function __construct(
        protected ExchangeRatesProvider $exchangeRatesProvider
    ) {
    }

    public function exchange(int $amount, string $currency): int
    {
//        $rate = 1.2;
        $rate = $this->getExchangeRate($currency);

        if ($rate === 0.0) {
            return $amount;
        }

        return (int) round($amount / $rate);
    }

    private function getExchangeRate(string $currency): float
    {
        if (!$this->exchangeRates) {
            $this->exchangeRates = $this->exchangeRatesProvider->get();
        }

        return $this->exchangeRates[$currency] ?? throw new ExchangeException("Exchange rate for $currency not found");
    }
}