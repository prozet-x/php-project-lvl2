<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\genDiff;

class AllTest extends TestCase
{
    private $expectedStylish12 = "{" . PHP_EOL
            . "    common: {" . PHP_EOL
            . "      + follow: false" . PHP_EOL
            . "        setting1: Value 1" . PHP_EOL
            . "      - setting2: 200" . PHP_EOL
            . "      - setting3: true" . PHP_EOL
            . "      + setting3: null" . PHP_EOL
            . "      + setting4: blah blah" . PHP_EOL
            . "      + setting5: {" . PHP_EOL
            . "            key5: value5" . PHP_EOL
            . "        }" . PHP_EOL
            . "        setting6: {" . PHP_EOL
            . "            doge: {" . PHP_EOL
            . "              - wow: " . PHP_EOL
            . "              + wow: so much" . PHP_EOL
            . "            }" . PHP_EOL
            . "            key: value" . PHP_EOL
            . "          + ops: vops" . PHP_EOL
            . "        }" . PHP_EOL
            . "    }" . PHP_EOL
            . "    group1: {" . PHP_EOL
            . "      - baz: bas" . PHP_EOL
            . "      + baz: bars" . PHP_EOL
            . "        foo: bar" . PHP_EOL
            . "      - nest: {" . PHP_EOL
            . "            key: value" . PHP_EOL
            . "        }" . PHP_EOL
            . "      + nest: str" . PHP_EOL
            . "    }" . PHP_EOL
            . "  - group2: {" . PHP_EOL
            . "        abc: 12345" . PHP_EOL
            . "        deep: {" . PHP_EOL
            . "            id: 45" . PHP_EOL
            . "        }" . PHP_EOL
            . "    }" . PHP_EOL
            . "  + group3: {" . PHP_EOL
            . "        deep: {" . PHP_EOL
            . "            id: {" . PHP_EOL
            . "                number: 45" . PHP_EOL
            . "            }" . PHP_EOL
            . "        }" . PHP_EOL
            . "        fee: 100500" . PHP_EOL
            . "    }" . PHP_EOL
            . "}" . PHP_EOL;

    private $expectedStylish13 = "{" . PHP_EOL
            . "  - common: {" . PHP_EOL
            . "        setting1: Value 1" . PHP_EOL
            . "        setting2: 200" . PHP_EOL
            . "        setting3: true" . PHP_EOL
            . "        setting6: {" . PHP_EOL
            . "            key: value" . PHP_EOL
            . "            doge: {" . PHP_EOL
            . "                wow: " . PHP_EOL
            . "            }" . PHP_EOL
            . "        }" . PHP_EOL
            . "    }" . PHP_EOL
            . "  - group1: {" . PHP_EOL
            . "        baz: bas" . PHP_EOL
            . "        foo: bar" . PHP_EOL
            . "        nest: {" . PHP_EOL
            . "            key: value" . PHP_EOL
            . "        }" . PHP_EOL
            . "    }" . PHP_EOL
            . "  - group2: {" . PHP_EOL
            . "        abc: 12345" . PHP_EOL
            . "        deep: {" . PHP_EOL
            . "            id: 45" . PHP_EOL
            . "        }" . PHP_EOL
            . "    }" . PHP_EOL
            . "}" . PHP_EOL;

    private $expectedStylish31 = "{" . PHP_EOL
            . "  + common: {" . PHP_EOL
            . "        setting1: Value 1" . PHP_EOL
            . "        setting2: 200" . PHP_EOL
            . "        setting3: true" . PHP_EOL
            . "        setting6: {" . PHP_EOL
            . "            key: value" . PHP_EOL
            . "            doge: {" . PHP_EOL
            . "                wow: " . PHP_EOL
            . "            }" . PHP_EOL
            . "        }" . PHP_EOL
            . "    }" . PHP_EOL
            . "  + group1: {" . PHP_EOL
            . "        baz: bas" . PHP_EOL
            . "        foo: bar" . PHP_EOL
            . "        nest: {" . PHP_EOL
            . "            key: value" . PHP_EOL
            . "        }" . PHP_EOL
            . "    }" . PHP_EOL
            . "  + group2: {" . PHP_EOL
            . "        abc: 12345" . PHP_EOL
            . "        deep: {" . PHP_EOL
            . "            id: 45" . PHP_EOL
            . "        }" . PHP_EOL
            . "    }" . PHP_EOL
            . "}" . PHP_EOL;

    private $jsonPath1 = 'tests/fixtures/tree1.json';
    private $jsonPath2 = 'tests/fixtures/tree2.json';
    private $jsonPath3 = 'tests/fixtures/tree3.json';
    private $yamlPath1 = 'tests/fixtures/tree1.yml';
    private $yamlPath2 = 'tests/fixtures/tree2.yml';
    private $yamlPath3 = 'tests/fixtures/tree3.yml';

    public function testStylishJSON(): void
    {
        $this->makeStylishTest($this -> jsonPath1, $this -> jsonPath2, $this -> jsonPath3);
    }

    public function testStylishYAML(): void
    {
        $this->makeStylishTest($this -> yamlPath1, $this -> yamlPath2, $this -> yamlPath3);
    }

    private function makeStylishTest($f1Path, $f2Path, $f3Path)
    {
        $this->assertEquals($this->expectedStylish12, genDiff($f1Path, $f2Path));
        $this->assertEquals($this->expectedStylish13, genDiff($f1Path, $f3Path));
        $this->assertEquals($this->expectedStylish31, genDiff($f3Path, $f1Path));
    }

    public function testPlain(): void
    {
        $expectedPlain12 = "Property 'common.follow' was added with value: false" . PHP_EOL
        . "Property 'common.setting2' was removed" . PHP_EOL
        . "Property 'common.setting3' was updated. From true to null" . PHP_EOL
        . "Property 'common.setting4' was added with value: 'blah blah'" . PHP_EOL
        . "Property 'common.setting5' was added with value: [complex value]" . PHP_EOL
        . "Property 'common.setting6.doge.wow' was updated. From '' to 'so much'" . PHP_EOL
        . "Property 'common.setting6.ops' was added with value: 'vops'" . PHP_EOL
        . "Property 'group1.baz' was updated. From 'bas' to 'bars'" . PHP_EOL
        . "Property 'group1.nest' was updated. From [complex value] to 'str'" . PHP_EOL
        . "Property 'group2' was removed" . PHP_EOL
        . "Property 'group3' was added with value: [complex value]" . PHP_EOL;

        $this->assertEquals($expectedPlain12, genDiff($this -> yamlPath1, $this -> jsonPath2, 'plain'));
    }

    public function testJSON(): void
    {
        $pathToFileWithJSONExpected = 'tests/fixtures/expected12.json';
        $handler = fopen($pathToFileWithJSONExpected, 'r');
        $expectedJSON12 = fread($handler, filesize($pathToFileWithJSONExpected));
        fclose($handler);
        $this->assertEquals($expectedJSON12, genDiff($this -> jsonPath1, $this -> yamlPath2, 'json'));
    }

    public function testBadFilePath(): void
    {
        $f1Path = 'tests/fixtures/badPath1.json';
        $f2Path = 'tests/fixtures/badPath2.yml';
        $expectedBadFilesPaths = "Files are not found:" . PHP_EOL
                . $f1Path . PHP_EOL
                . $f2Path . PHP_EOL
                . "You should enter an existing files paths.";
        $this -> expectExceptionMessage($expectedBadFilesPaths);
        genDiff($f1Path, $f2Path);
    }

    public function testBadOutputFormat(): void
    {
        $expectedBadOutputFormat = "Bad output format. You may use 'stylish', 'plain' or 'json'.";
        $this -> expectExceptionMessage($expectedBadOutputFormat);
        genDiff($this -> jsonPath1, $this -> yamlPath2, 'badFormat');
    }

    public function testBadFileFormat(): void
    {
        $expectedBadOutputFormat = "Bad file format. You should pass JSON or YAML files only.";
        $this -> expectExceptionMessage($expectedBadOutputFormat);
        genDiff('tests/fixtures/badExtention.bef', $this -> yamlPath2);
    }
}
