<?php

namespace Differ;

use function Differ\Parsers\parseJSON;
use function Differ\Parsers\parseYAML;

function getFormattedString($key, $value, $changesSymbol = ' ')
{
    return "  $changesSymbol $key: " . trim(json_encode($value), '"') . PHP_EOL;
}

function makeDiff($f1data, $f2data)
{
    $totalData = array_merge($f1data, $f2data);
    ksort($totalData);

    $keys = array_unique(array_merge(array_keys($f1data), array_keys($f2data)));
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
    
    $res = array_reduce($keys,
            function ($acc, $key) use ($before, $after){
                $inBefore = array_key_exists($key, $before);
                $inAfter = array_key_exists($key, $after);
                if (!$inBefore) {
                    return [...$acc, ['key' => $key, 'value' => $after[$key], 'changes' => 'a']];
                }
                if (!$inAfter) {
                    return [...$acc, ['key' => $key, 'value' => $before[$key], 'changes' => 'r']];
                }
                if (is_array($before[$key]) xor is_array($after[$key])) {
                    return [
                        ...$acc,
                        ['key' => $key, 'value' => $before[$key], 'changes' => 'r'],
                        ['key' => $key, 'value' => $after[$key], 'changes' => 'a']
                    ];
                }
                if (!is_array($before[$key]) and !is_array($after[$key])) {
                    
                    
                }
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
    return makeDiff($f1data, $f2data);
}
