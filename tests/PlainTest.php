<?php

namespace Hexlet\Phpunit\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\genDiff;

class PlainTest extends TestCase
{
    private $expected12 = "{" . PHP_EOL
                . "  - follow: false" . PHP_EOL
                . "    host: hexlet.io" . PHP_EOL
                . "  - proxy: 123.234.53.22" . PHP_EOL
                . "  - timeout: 50" . PHP_EOL
                . "  + timeout: 20" . PHP_EOL
                . "  + verbose: true" . PHP_EOL
                . "}" . PHP_EOL;

    private $expected21 = "{" . PHP_EOL
            . "  + follow: false" . PHP_EOL
            . "    host: hexlet.io" . PHP_EOL
            . "  + proxy: 123.234.53.22" . PHP_EOL
            . "  - timeout: 20" . PHP_EOL
            . "  + timeout: 50" . PHP_EOL
            . "  - verbose: true" . PHP_EOL
            . "}" . PHP_EOL;

    private $expected13 = "{" . PHP_EOL
            . "  - follow: false" . PHP_EOL
            . "  - host: hexlet.io" . PHP_EOL
            . "  - proxy: 123.234.53.22" . PHP_EOL
            . "  - timeout: 50" . PHP_EOL
            . "}" . PHP_EOL;

    private $expected32 = "{" . PHP_EOL
            . "  + host: hexlet.io" . PHP_EOL
            . "  + timeout: 20" . PHP_EOL
            . "  + verbose: true" . PHP_EOL
            . "}" . PHP_EOL;

    private $expected33 = "{" . PHP_EOL
                . "}" . PHP_EOL;

    public function testJSONPlain(): void
    {
        $f1Path = 'tests/fixtures/file1.json';
        $f2Path = 'tests/fixtures/file2.json';
        $f3Path = 'tests/fixtures/file3.json';

        $this->makeTest($f1Path, $f2Path, $f3Path);
    }

    public function testYAMLPlain(): void
    {
        $f1Path = 'tests/fixtures/file1.yml';
        $f2Path = 'tests/fixtures/file2.yml';
        $f3Path = 'tests/fixtures/file3.yml';

        $this->makeTest($f1Path, $f2Path, $f3Path);
    }

    private function makeTest($f1Path, $f2Path, $f3Path)
    {
        $this->assertEquals($this->expected12, genDiff($f1Path, $f2Path));
        $this->assertEquals($this->expected21, genDiff($f2Path, $f1Path));
        $this->assertEquals($this->expected13, genDiff($f1Path, $f3Path));
        $this->assertEquals($this->expected32, genDiff($f3Path, $f2Path));
        $this->assertEquals($this->expected33, genDiff($f3Path, $f3Path));
    }
}
