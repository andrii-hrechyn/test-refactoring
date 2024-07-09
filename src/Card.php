<?php

namespace App;

use App\Helpers\CountriesHelper;

class Card
{
    public function __construct(
        protected string $country
    ) {
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function isEu(): bool
    {
        return in_array($this->country, CountriesHelper::euCountries());
    }
}