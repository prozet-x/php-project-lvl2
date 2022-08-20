<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\genDiff;

class AllTest extends TestCase
{
    protected static $expectedStylish12;
    protected static $expectedStylish13;
    protected static $expectedStylish31;

    public static function setUpBeforeClass(): void
    {
        self :: $expectedStylish12 = file_get_contents('tests/fixtures/expected12stylish.txt');
        self :: $expectedStylish13 = file_get_contents('tests/fixtures/expected13stylish.txt');
        self :: $expectedStylish31 = file_get_contents('tests/fixtures/expected31stylish.txt');
    }

    private $jsonPath1 = 'tests/fixtures/tree1.json';
    private $jsonPath2 = 'tests/fixtures/tree2.json';
    private $jsonPath3 = 'tests/fixtures/tree3.json';
    private $yamlPath1 = 'tests/fixtures/tree1.yml';
    private $yamlPath2 = 'tests/fixtures/tree2.yml';
    private $yamlPath3 = 'tests/fixtures/tree3.yml';

    public function testStylish(): void
    {
        $this->makeStylishTest($this -> jsonPath1, $this -> jsonPath2, $this -> jsonPath3);
        $this->makeStylishTest($this -> yamlPath1, $this -> yamlPath2, $this -> yamlPath3);
    }

    private function makeStylishTest($f1Path, $f2Path, $f3Path)
    {
        $this->assertEquals(self :: $expectedStylish12, genDiff($f1Path, $f2Path));
        $this->assertEquals(self :: $expectedStylish13, genDiff($f1Path, $f3Path));
        $this->assertEquals(self :: $expectedStylish31, genDiff($f3Path, $f1Path));
    }

    public function testPlain(): void
    {
        $expectedPlain12 = file_get_contents('tests/fixtures/expected12plain.txt');
        $this->assertEquals($expectedPlain12, genDiff($this -> yamlPath1, $this -> jsonPath2, 'plain'));
    }

    public function testJSON(): void
    {
        $expectedJSON12 = file_get_contents('tests/fixtures/expected12json.txt');
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
