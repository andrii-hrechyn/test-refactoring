<?php

namespace App\Parsers;

use App\Contracts\FileParser;
use Exception;

class TxtParser implements FileParser
{
    public function parse(string $filename): array
    {
        return array_filter(array_map($this->parseLine(...), explode("\n", file_get_contents($filename))));
    }

    private function parseLine(string $line): array
    {
        if (empty($line)) {
            return [];
        }

        $data = json_decode($line, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data: '.json_last_error_msg());
        }

        return $data;
    }
}