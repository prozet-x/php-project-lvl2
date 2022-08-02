<?php

namespace Differ;

function getFormattedString($changesSymbol, $key, $value)
{
    return "  " . $changesSymbol . " " . $key . ': ' . trim(json_encode($value), '"') . PHP_EOL;
}

function makeDiff($f1data, $f2data)
{
    //echo ($f1data);
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

    /*foreach ($totalData as $k => $v) {
        $in1 = array_key_exists($k, $f1data);
        $in2 = array_key_exists($k, $f2data);

        if (!$in1) {
            $res .= "  + " . $k . ': ' . trim(json_encode($v), '"') . PHP_EOL;
            continue;
        } elseif (!$in2) {
            $res .= "  - " . $k . ': ' . trim(json_encode($v), '"') . PHP_EOL;
            continue;
        }
        if ($f1data[$k] !== $f2data[$k]) {
                $res .= "  - " . $k . ': ' . trim(json_encode($f1data[$k]), '"') . PHP_EOL;
                $res .= "  + " . $k . ': ' . trim(json_encode($f2data[$k]), '"') . PHP_EOL;
        } else {
            $res .= "    " . $k . ': ' . trim(json_encode($v), '"') . PHP_EOL;
        }
    }

    $res .= "}" . PHP_EOL;

    //echo($res);

    return $res;*/
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
