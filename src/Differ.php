<?php

namespace Differ\Differ;

use function Differ\Parsers\getParser;
use function Differ\Formatters\formatDiff;

function getFormattedValue(mixed $value)
{
    return is_array($value) ? makeDiff($value, $value) : $value;
}

function getNewDiffElem($key, $changes, $value, ...$args)
{
    $res = ['key' => $key, 'changes' => $changes, 'value' => getFormattedValue($value)];
    if (count($args) === 1) {
        $res['oldValue'] = getFormattedValue($args[0]);
    }
     return $res;
}

function makeDiff(array $before, array $after)
{
    $getNewDiffElemFunc = function ($key, $changes, $value, ...$args) {
        return getNewDiffElem($key, $changes, $value, ...$args);
    };

    $keys = array_unique(array_merge(array_keys($before), array_keys($after)));

    return array_reduce(
        $keys,
        function ($acc, $key) use ($before, $after, $getNewDiffElemFunc) {
            $inBefore = array_key_exists($key, $before);
            $inAfter = array_key_exists($key, $after);
            if (!$inBefore) {
                return [...$acc, $getNewDiffElemFunc($key, 'a', $after[$key])];
            }
            if (!$inAfter) {
                return [...$acc, $getNewDiffElemFunc($key, 'r', $before[$key])];
            }
            if (is_array($before[$key]) and is_array($after[$key])) {
                return [...$acc, ['key' => $key, 'changes' => 'n', 'value' => makeDiff($before[$key], $after[$key])]];
            }
            return $before[$key] === $after[$key]
                       ? [...$acc, $getNewDiffElemFunc($key, 'n', $after[$key])]
                       : [...$acc, $getNewDiffElemFunc($key, 'u', $after[$key], $before[$key])];
        },
        []
    );
}

function getFileDataAsArray(string $pathToFile)
{
    $parser = getParser($pathToFile);
    return $parser($pathToFile);
}

function checkfilesExisting(array ...$files)
{
    $badPaths = array_reduce(
        $files,
        function ($acc, $pathToFile) {
            return file_exists($pathToFile) ? $acc : [...$acc, $pathToFile];
        },
        []
    );
    if (count($badPaths) > 0) {
        $message = "Files are not found:" . PHP_EOL
                . implode(PHP_EOL, $badPaths) . PHP_EOL
                . "You should enter an existing files paths.";
        throw new \Exception($message);
    }
}

function genDiff(string $pathToFile1, string $pathToFile2, string $outputFormat = 'stylish')
{
    checkfilesExisting($pathToFile1, $pathToFile2);
    $f1data = getFileDataAsArray($pathToFile1);
    $f2data = getFileDataAsArray($pathToFile2);
    $res = makeDiff($f1data, $f2data);
    return formatDiff($res, $outputFormat);
}
