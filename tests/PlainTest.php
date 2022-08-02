<?php

namespace Hexlet\Phpunit\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\genDiff;

class PlainTest extends TestCase
{
    public function testPlain(): void
    {
        $f1Path = 'tests/fixtures/file1.json';
        $f2Path = 'tests/fixtures/file2.json';
        $f3Path = 'tests/fixtures/file3.json';

        $expected12 = "{" . PHP_EOL
                . "  - follow: false" . PHP_EOL
                . "    host: hexlet.io" . PHP_EOL
                . "  - proxy: 123.234.53.22" . PHP_EOL
                . "  - timeout: 50" . PHP_EOL
                . "  + timeout: 20" . PHP_EOL
                . "  + verbose: true" . PHP_EOL
                . "}" . PHP_EOL;

        $expected21 = "{" . PHP_EOL
                . "  + follow: false" . PHP_EOL
                . "    host: hexlet.io" . PHP_EOL
                . "  + proxy: 123.234.53.22" . PHP_EOL
                . "  - timeout: 20" . PHP_EOL
                . "  + timeout: 50" . PHP_EOL
                . "  - verbose: true" . PHP_EOL
                . "}" . PHP_EOL;

        $expected13 = "{" . PHP_EOL
                . "  - follow: false" . PHP_EOL
                . "  - host: hexlet.io" . PHP_EOL
                . "  - proxy: 123.234.53.22" . PHP_EOL
                . "  - timeout: 50" . PHP_EOL
                . "}" . PHP_EOL;

        $expected32 = "{" . PHP_EOL
                . "  + host: hexlet.io" . PHP_EOL
                . "  + timeout: 20" . PHP_EOL
                . "  + verbose: true" . PHP_EOL
                . "}" . PHP_EOL;

        $this->assertEquals($expected12, genDiff($f1Path, $f2Path));
        $this->assertEquals($expected21, genDiff($f2Path, $f1Path));
        $this->assertEquals($expected13, genDiff($f1Path, $f3Path));
        $this->assertEquals($expected32, genDiff($f3Path, $f2Path));
    }
}
