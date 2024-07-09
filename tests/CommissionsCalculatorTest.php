<?php

namespace App\Tests;

use App\Card;
use App\CommissionsCalculator;
use App\Contracts\CardInfoProvider;
use App\Exchanger;
use PHPUnit\Framework\TestCase;

class CommissionsCalculatorTest extends TestCase
{
    public function testCalculateBatch()
    {
        $cardInfoProviderMock = $this->createMock(CardInfoProvider::class);
        $exchangerMock = $this->createMock(Exchanger::class);

        $calculator = new CommissionsCalculator($cardInfoProviderMock, $exchangerMock);

        $transactions = [
            ['bin' => '45717360', 'amount' => 100.00, 'currency' => 'EUR'],
            ['bin' => '516793', 'amount' => 50.00, 'currency' => 'USD'],
        ];

        $cardInfoProviderMock->method('get')->willReturnOnConsecutiveCalls(
            $this->createCard(true),  // EU card
            $this->createCard(false)  // Non-EU card
        );

        $exchangerMock->method('exchange')->willReturn(4000);

        $result = $calculator->calculateBatch($transactions);

        $this->assertEquals([1.00, 0.80], $result);
    }

    public function testCalculateWithEUCurrency()
    {
        $cardInfoProviderMock = $this->createMock(CardInfoProvider::class);
        $exchangerMock = $this->createMock(Exchanger::class);

        $calculator = new CommissionsCalculator($cardInfoProviderMock, $exchangerMock);

        $transaction = ['bin' => '45717360', 'amount' => 100.00, 'currency' => 'EUR'];

        $cardInfoProviderMock->method('get')->willReturn($this->createCard(true));

        $result = $calculator->calculate($transaction);

        $this->assertEquals(1.00, $result);
    }

    public function testCalculateWithNonEUCurrency()
    {
        $cardInfoProviderMock = $this->createMock(CardInfoProvider::class);
        $exchangerMock = $this->createMock(Exchanger::class);

        $calculator = new CommissionsCalculator($cardInfoProviderMock, $exchangerMock);

        $transaction = ['bin' => '516793', 'amount' => 50.00, 'currency' => 'USD'];

        $cardInfoProviderMock->method('get')->willReturn($this->createCard(false));
        $exchangerMock->method('exchange')->willReturn(4000);  // Exchange USD to EUR

        $result = $calculator->calculate($transaction);

        $this->assertEquals(0.80, $result);
    }

    private function createCard(bool $isEu): Card
    {
        $cardMock = $this->createMock(Card::class);
        $cardMock->method('isEu')->willReturn($isEu);

        return $cardMock;
    }
}
