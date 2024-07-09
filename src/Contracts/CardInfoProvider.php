<?php

namespace App\Contracts;

use App\Card;

interface CardInfoProvider
{
    public function get(int $binNumber): Card;
}