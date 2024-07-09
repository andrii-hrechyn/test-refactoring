<?php

namespace App\Tests;

use App\Parsers\TxtParser;
use PHPUnit\Framework\TestCase;

class TxtParserTest extends TestCase
{
    private string $testFile;
    private string $invalidFile;
    private string $emptyFile;

    protected function setUp(): void
    {
        $this->testFile = tempnam(sys_get_temp_dir(), 'test');
        $this->invalidFile = tempnam(sys_get_temp_dir(), 'invalid');
        $this->emptyFile = tempnam(sys_get_temp_dir(), 'empty');

        $testContent = <<<EOT
{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
EOT;
        file_put_contents($this->testFile, $testContent);

        $invalidContent = <<<EOT
{"bin":"45717360","amount":"100.00","currency":"EUR"}
{invalid json}
EOT;
        file_put_contents($this->invalidFile, $invalidContent);

        file_put_contents($this->emptyFile, '');
    }

    protected function tearDown(): void
    {
        unlink($this->testFile);
        unlink($this->invalidFile);
        unlink($this->emptyFile);
    }

    public function testParseValidFile()
    {
        $parser = new TxtParser();
        $result = $parser->parse($this->testFile);

        $expected = [
            ['bin' => '45717360', 'amount' => '100.00', 'currency' => 'EUR'],
            ['bin' => '516793', 'amount' => '50.00', 'currency' => 'USD'],
        ];

        $this->assertCount(2, $result);
        $this->assertEquals($expected, $result);
    }

    public function testParseInvalidJson()
    {
        $parser = new TxtParser();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid JSON data');

        $parser->parse($this->invalidFile);
    }

    public function testParseEmptyFile()
    {
        $parser = new TxtParser();
        $result = $parser->parse($this->emptyFile);

        $this->assertCount(0, $result);
    }
}
