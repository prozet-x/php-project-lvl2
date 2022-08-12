<?php

namespace Differ;

use function Differ\Parsers\parseJSON;
use function Differ\Parsers\parseYAML;

function getFormattedValue($value)
{
    return trim(json_encode($value), '"');
}

function getFormattedString($key, $value, $changesSymbol = ' ')
{
    return "  $changesSymbol $key: " . getFormattedValue($value) . PHP_EOL;
}

function makeDiff($before, $after)
{
    $totalData = array_merge($before, $after);
    ksort($totalData);

    $keys = array_unique(array_merge(array_keys($before), array_keys($after)));
    asort($keys);
    /*$res = array_reduce(
        $keys,
        function ($acc, $key) use ($f1data, $f2data) {
            $in1 = array_key_exists($key, $f1data);
            $in2 = array_key_exists($key, $f2data);
            if (!$in1) {
                return $acc . getFormattedString($key, $f2data[$key], "+");
            }
            if (!$in2) {
                return $acc . getFormattedString($key, $f1data[$key], "-");
            }
            if ($f1data[$key] !== $f2data[$key]) {
                return $acc . getFormattedString($key, $f1data[$key], "-")
                    . getFormattedString($key, $f2data[$key], "+");
            }
            return $acc . getFormattedString($key, $f1data[$key]);
        },
        "{" . PHP_EOL
    );
    return $res . "}" . PHP_EOL;*/
    
    return array_reduce($keys,
            function ($acc, $key) use ($before, $after){
                $inBefore = array_key_exists($key, $before);
                $inAfter = array_key_exists($key, $after);
                if (!$inBefore) {
                    return [...$acc, ['key' => $key, 'value' => getFormattedValue($after[$key]), 'changes' => 'a']];
                }
                if (!$inAfter) {
                    return [...$acc, ['key' => $key, 'value' => getFormattedValue($before[$key]), 'changes' => 'r']];
                }
                if (is_array($before[$key]) xor is_array($after[$key])) {
                    return [...$acc, ['key' => $key, 'value' => getFormattedValue($after[$key]), 'changes' => 'u', 'oldValue' => $before[$key]]];
                }
                if (!is_array($before[$key]) and !is_array($after[$key])) {
                    return $before[$key] === $after[$key]
                           ? [...$acc, ['key' => $key, 'value' => getFormattedValue($after[$key]), 'changes' => 'n']]
                           : [...$acc, ['key' => $key, 'value' => getFormattedValue($after[$key]), 'changes' => 'u', 'oldValue' => $before[$key]]];
                }
                return [...$acc, ['key' => $key, 'value' => makeDiff($before[$key], $after[$key]), 'changes' => 'n']];
            },
            []);
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
    print_r($res);
    return $res;
}
