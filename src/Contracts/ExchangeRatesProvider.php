<?php

namespace App\Contracts;

interface ExchangeRatesProvider
{
    public function get(): array;
}