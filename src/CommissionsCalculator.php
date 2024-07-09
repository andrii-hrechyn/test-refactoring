<?php

namespace App;

use App\Contracts\CardInfoProvider;

class CommissionsCalculator
{
    private const DEFAULT_CURRENCY = 'EUR';
    // Commission rate can be easily moved to config
    private const COMMISSION_RATE_FOR_EU = 0.01;
    private const COMMISSION_RATE = 0.02;

    public function __construct(
        protected CardInfoProvider $cardInfoProvider,
        protected Exchanger $exchanger
    ) {
    }

    public function calculateBatch(array $transactions): array
    {
        foreach ($transactions as $transaction) {
            $commissions[] = $this->calculate($transaction);
        }

        return $commissions ?? [];
    }

    public function calculate(array $transaction): float
    {
        $amount = $this->exchange($this->normalizeAmount($transaction['amount']), $transaction['currency']);
        $card = $this->cardInfoProvider->get($transaction['bin']);

        return $this->calcCommission($amount, $this->getCommissionRate($card));
    }

    private function exchange(int $amount, string $currency): int
    {
        if ($this->isDefaultCurrency($currency)) {
            return $amount;
        }

        return $this->exchanger->exchange($amount, $currency);
    }

    private function isDefaultCurrency(string $currency): bool
    {
        return $currency === self::DEFAULT_CURRENCY;
    }

    private function getCommissionRate(Card $card): float
    {
        return $card->isEu() ? self::COMMISSION_RATE_FOR_EU : self::COMMISSION_RATE;
    }

    private function calcCommission(int $amount, float $commissionRate): float
    {
        return ((int) ceil($amount * $commissionRate)) / 100;
    }

    private function normalizeAmount(float $amount): int
    {
        return (int) ($amount * 100);
    }
}