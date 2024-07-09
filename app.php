<?php

use App\CardInfoProviders\Binlist\BinlistCardInfoProvider;
use App\CommissionsCalculator;
use App\Exchanger;
use App\ExchangeRatesProviders\ExchangeRatesApi\ExchangeRatesApiProvider;
use App\Parsers\TxtParser;

require __DIR__.'/vendor/autoload.php';

if (!isset($argv[1])) {
    throw new Exception('Please provide the filename of the input file');
}

$filename = $argv[1];

if (!file_exists($filename)) {
    throw new Exception('Input file does not exist');
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$exchangeRatesProvider = new ExchangeRatesApiProvider($_ENV['EXCHANGE_RATES_API_KEY']);
$cardInfoProvider = new BinlistCardInfoProvider();

$calculator = new CommissionsCalculator($cardInfoProvider, new Exchanger($exchangeRatesProvider));
$parser = new TxtParser();

$commissions = $calculator->calculateBatch($parser->parse($filename));

foreach ($commissions as $commission) {
    echo $commission.PHP_EOL;
}

