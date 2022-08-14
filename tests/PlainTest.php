<?php

namespace Hexlet\Phpunit\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\genDiff;

class PlainTest extends TestCase
{
    private $expectedPlain12 = "{" . PHP_EOL
                . "  - follow: false" . PHP_EOL
                . "    host: hexlet.io" . PHP_EOL
                . "  - proxy: 123.234.53.22" . PHP_EOL
                . "  - timeout: 50" . PHP_EOL
                . "  + timeout: 20" . PHP_EOL
                . "  + verbose: true" . PHP_EOL
                . "}" . PHP_EOL;

    private $expectedPlain21 = "{" . PHP_EOL
            . "  + follow: false" . PHP_EOL
            . "    host: hexlet.io" . PHP_EOL
            . "  + proxy: 123.234.53.22" . PHP_EOL
            . "  - timeout: 20" . PHP_EOL
            . "  + timeout: 50" . PHP_EOL
            . "  - verbose: true" . PHP_EOL
            . "}" . PHP_EOL;

    private $expectedPlain13 = "{" . PHP_EOL
            . "  - follow: false" . PHP_EOL
            . "  - host: hexlet.io" . PHP_EOL
            . "  - proxy: 123.234.53.22" . PHP_EOL
            . "  - timeout: 50" . PHP_EOL
            . "}" . PHP_EOL;

    private $expectedPlain32 = "{" . PHP_EOL
            . "  + host: hexlet.io" . PHP_EOL
            . "  + timeout: 20" . PHP_EOL
            . "  + verbose: true" . PHP_EOL
            . "}" . PHP_EOL;

    private $expectedPlain33 = "{" . PHP_EOL
            . "}" . PHP_EOL;
    
    private $expectedTree12 = "{" . PHP_EOL
            ."    common: {" . PHP_EOL
            ."      + follow: false" . PHP_EOL
            ."        setting1: Value 1" . PHP_EOL
            ."      - setting2: 200" . PHP_EOL
            ."      - setting3: true" . PHP_EOL
            ."      + setting3: null" . PHP_EOL
            ."      + setting4: blah blah" . PHP_EOL
            ."      + setting5: {" . PHP_EOL
            ."            key5: value5" . PHP_EOL
            ."        }" . PHP_EOL
            ."        setting6: {" . PHP_EOL
            ."            doge: {" . PHP_EOL
            ."              - wow: " . PHP_EOL
            ."              + wow: so much" . PHP_EOL
            ."            }" . PHP_EOL
            ."            key: value" . PHP_EOL
            ."          + ops: vops" . PHP_EOL
            ."        }" . PHP_EOL
            ."    }" . PHP_EOL
            ."    group1: {" . PHP_EOL
            ."      - baz: bas" . PHP_EOL
            ."      + baz: bars" . PHP_EOL
            ."        foo: bar" . PHP_EOL
            ."      - nest: {" . PHP_EOL
            ."            key: value" . PHP_EOL
            ."        }" . PHP_EOL
            ."      + nest: str" . PHP_EOL
            ."    }" . PHP_EOL
            ."  - group2: {" . PHP_EOL
            ."        abc: 12345" . PHP_EOL
            ."        deep: {" . PHP_EOL
            ."            id: 45" . PHP_EOL
            ."        }" . PHP_EOL
            ."    }" . PHP_EOL
            ."  + group3: {" . PHP_EOL
            ."        deep: {" . PHP_EOL
            ."            id: {" . PHP_EOL
            ."                number: 45" . PHP_EOL
            ."            }" . PHP_EOL
            ."        }" . PHP_EOL
            ."        fee: 100500" . PHP_EOL
            ."    }" . PHP_EOL
            ."}" . PHP_EOL;

    public function testJSONPlain(): void
    {
        $f1Path = 'tests/fixtures/plain1.json';
        $f2Path = 'tests/fixtures/plain2.json';
        $f3Path = 'tests/fixtures/plain3.json';

        $this->makePlainTest($f1Path, $f2Path, $f3Path);
    }

    public function testYAMLPlain(): void
    {
        $f1Path = 'tests/fixtures/plain1.yml';
        $f2Path = 'tests/fixtures/plain2.yml';
        $f3Path = 'tests/fixtures/plain3.yml';

        $this->makePlainTest($f1Path, $f2Path, $f3Path);
    }
    
    public function testJSONTree(): void
    {
        $f1Path = 'tests/fixtures/tree1.json';
        $f2Path = 'tests/fixtures/tree2.json';
        $f3Path = 'tests/fixtures/tree3.json';

        $this->makeTreeTest($f1Path, $f2Path, $f3Path);
    }

    /*public function testYAMLTree(): void
    {
        $f1Path = 'tests/fixtures/tree1.yml';
        $f2Path = 'tests/fixtures/tree2.yml';
        $f3Path = 'tests/fixtures/tree3.yml';

        $this->makePlainTest($f1Path, $f2Path, $f3Path);
    }*/

    private function makePlainTest($f1Path, $f2Path, $f3Path)
    {
        $this->assertEquals($this->expectedPlain12, genDiff($f1Path, $f2Path));
        $this->assertEquals($this->expectedPlain21, genDiff($f2Path, $f1Path));
        $this->assertEquals($this->expectedPlain13, genDiff($f1Path, $f3Path));
        $this->assertEquals($this->expectedPlain32, genDiff($f3Path, $f2Path));
        $this->assertEquals($this->expectedPlain33, genDiff($f3Path, $f3Path));
    }

    private function makeTreeTest($f1Path, $f2Path, $f3Path)
    {
        $this->assertEquals($this->expectedTree12, genDiff($f1Path, $f2Path));
    }
}
