<?php

namespace Differ;

use function Differ\Parsers\parseJSON;
use function Differ\Parsers\parseYAML;
use function Differ\Formatters\formatStylish;

function getFormattedValue($value)
{
    return is_array($value) ? makeDiff($value, $value) : trim(json_encode($value), '"');
}

function getNewDiffElem($key, $changes, $value, ...$args)
{
    $res = ['key' => $key, 'changes' => $changes, 'value' => getFormattedValue($value)];
    return count($args) === 1
        ? [...$res, 'oldValue' => getFormattedValue($args[0])]
        : $res;
}

function makeDiff($before, $after)
{
    $getNewDiffElemFunc = function ($key, $changes, $value, ...$args) {
        return getNewDiffElem($key, $changes, $value, ...$args);
    };

    //$totalData = array_merge($before, $after);
    $keys = array_unique(array_merge(array_keys($before), array_keys($after)));

    return array_reduce(
        $keys,
        function ($acc, $key) use ($before, $after, $getNewDiffElemFunc) {
            print_r($before);
            print_r($after);
            print_r($key . PHP_EOL);
            $inBefore = array_key_exists($key, $before);
            $inAfter = array_key_exists($key, $after);
            if (!$inBefore) {
                return [...$acc, $getNewDiffElemFunc($key, 'a', $after[$key])];
            }
            if (!$inAfter) {
                return [...$acc, $getNewDiffElemFunc($key, 'r', $before[$key])];
            }
            if (is_array($before[$key]) xor is_array($after[$key])) {
                return [...$acc, $getNewDiffElemFunc($key, 'u', $after[$key], $before[$key])];
            }
            if (!is_array($before[$key]) and !is_array($after[$key])) {
                print_r('_________________' . PHP_EOL);
                print_r($before[$key]);
                print_r(PHP_EOL);
                print_r($after[$key]);
                print_r(PHP_EOL);
                print_r($before[$key] === $after[$key]);
                print_r(PHP_EOL);
                print_r('_________________' . PHP_EOL);
                return $before[$key] === $after[$key]
                       ? [...$acc, $getNewDiffElemFunc($key, 'n', $after[$key])]
                       : [...$acc, $getNewDiffElemFunc($key, 'u', $after[$key], $before[$key])];
            }
            return [...$acc, $getNewDiffElemFunc($key, 'n', makeDiff($before[$key], $after[$key]))];
        },
        []
    );
}

function getParser($pathToFile)
{
    $extension = strtolower(pathinfo($pathToFile, PATHINFO_EXTENSION));
    if ($extension === 'json') {
        return function ($pathToFile) {
            return parseJSON($pathToFile);
        };
    } elseif ($extension === 'yml' || $extension === 'yaml') {
        return function ($pathToFile) {
            return parseYAML($pathToFile);
        };
    }
}

function getFileDataAsArray($pathToFile)
{
    $parser = getParser($pathToFile);
    return $parser($pathToFile);
}

function genDiff($pathToFile1, $pathToFile2)
{
    $f1data = getFileDataAsArray($pathToFile1);
    $f2data = getFileDataAsArray($pathToFile2);
    $res = makeDiff($f1data, $f2data);
    return formatStylish($res);
}
