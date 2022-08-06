<?php

namespace Differ;

function getFormattedString($changesSymbol, $key, $value)
{
    return "  " . $changesSymbol . " " . $key . ': ' . trim(json_encode($value), '"') . PHP_EOL;
}

function makeDiff($f1data, $f2data)
{
    $totalData = array_merge($f1data, $f2data);
    ksort($totalData);

    $keys = array_unique(array_merge(array_keys($f1data), array_keys($f2data)));
    asort($keys);
    $res = array_reduce(
        $keys,
        function ($acc, $key) use ($f1data, $f2data) {
            $in1 = array_key_exists($key, $f1data);
            $in2 = array_key_exists($key, $f2data);
            if (!$in1) {
                return $acc . getFormattedString("+", $key, $f2data[$key]);
            }
            if (!$in2) {
                return $acc . getFormattedString("-", $key, $f1data[$key]);
            }
            if ($f1data[$key] !== $f2data[$key]) {
                return $acc . getFormattedString("-", $key, $f1data[$key])
                    . getFormattedString("+", $key, $f2data[$key]);
            }
            return $acc . getFormattedString(" ", $key, $f1data[$key]);
        },
        "{" . PHP_EOL
    );
    return $res . "}" . PHP_EOL;
}

function parseJSON($pathToFile)
{
    $handler = fopen($pathToFile, 'r');
    return json_decode(fread($handler, filesize($pathToFile)), true);
}

function getParser($pathToFile)
{
    $extention = pathinfo($info->getFilename(), PATHINFO_EXTENSION);
    if ()
}

function genDiff($pathToFile1, $pathToFile2)
{
    $f1handler = fopen($pathToFile1, 'r');
    $f2handler = fopen($pathToFile2, 'r');

    $f1data = json_decode(fread($f1handler, filesize($pathToFile1)), true);
    $f2data = json_decode(fread($f2handler, filesize($pathToFile2)), true);

    fclose($f1handler);
    fclose($f2handler);

    return makeDiff($f1data, $f2data);
}
