<?php

namespace App\Contracts;

interface FileParser
{
    public function parse(string $filename): array;
}