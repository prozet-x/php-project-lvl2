<?php

namespace Differ;

use function Differ\Parsers\parseJSON;
use function Differ\Parsers\parseYAML;
use function Differ\Formatters\formatStylish;

function getFormattedValue($value)
{
    return is_array($value) ? makeDiff($value, $value) : trim(json_encode($value), '"');
}

function makeDiff($before, $after)
{
    $totalData = array_merge($before, $after);
    //ksort($totalData);

    $keys = array_unique(array_merge(array_keys($before), array_keys($after)));

    return array_reduce(
        $keys,
        function ($acc, $key) use ($before, $after) {
            $inBefore = array_key_exists($key, $before);
            $inAfter = array_key_exists($key, $after);
            if (!$inBefore) {
                return [...$acc, ['key' => $key, 'changes' => 'a', 'value' => getFormattedValue($after[$key])]];
            }
            if (!$inAfter) {
                return [...$acc, ['key' => $key, 'changes' => 'r', 'value' => getFormattedValue($before[$key])]];
            }
            if (is_array($before[$key]) xor is_array($after[$key])) {
                return [
                            ...$acc,
                            [
                                'key' => $key,
                                'changes' => 'u',
                                'value' => getFormattedValue($after[$key]),
                                'oldValue' => getFormattedValue($before[$key])
                            ]
                    ]   ;
            }
            if (!is_array($before[$key]) and !is_array($after[$key])) {
                return $before[$key] === $after[$key]
                       ? [...$acc, ['key' => $key, 'changes' => 'n', 'value' => getFormattedValue($after[$key])]]
                       : [
                            ...$acc,
                           [
                                'key' => $key,
                                'changes' => 'u',
                                'value' => getFormattedValue($after[$key]),
                                'oldValue' => getFormattedValue($before[$key])
                            ]
                         ];
            }
            return [...$acc, ['key' => $key, 'changes' => 'n', 'value' => makeDiff($before[$key], $after[$key])]];
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
