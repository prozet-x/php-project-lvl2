<?php

namespace Differ;

function makeDiff($pathToFile1, $pathToFile2)
{
    $f1handler = fopen($pathToFile1, 'r');
    $f2handler = fopen($pathToFile2, 'r');

    $f1data = json_decode(fread($f1handler, filesize($pathToFile1)), true);
    $f2data = json_decode(fread($f2handler, filesize($pathToFile2)), true);
    $totalData = array_merge($f1data, $f2data);
    ksort($totalData);

    $res = "{" . PHP_EOL;
    foreach ($totalData as $k => $v) {
        $in1 = array_key_exists($k, $f1data);
        $in2 = array_key_exists($k, $f2data);
        if ($in1 && $in2) {
            $res .= $f1data[$k] !== $f2data[$k] ? "  - "  : "  + ";
            $res .= $k . ': ' . trim(json_encode($f2data[$k]), '"') . PHP_EOL;
        } elseif ($in1) {
            $res .= "  - " . $k . ': ' . trim(json_encode($v), '"') . PHP_EOL;
        } else {
            $res .= "  + " . $k . ': ' . trim(json_encode($v), '"') . PHP_EOL;
        }
    }

    $res .= "}" . PHP_EOL;

    fclose($f1handler);
    fclose($f2handler);

    echo($res);

    return $res;
}

function genDiff()
{
    $doc = <<<DOC
    Generate diff

    Usage:
      gendiff (-h|--help)
      gendiff (-v|--version)
      gendiff [--format <fmt>] <firstFile> <secondFile>

    Options:
      -h --help                     Show this screen
      -v --version                  Show version
      --format <fmt>                Report format [default: stylish]

    DOC;

    $args = \Docopt::handle($doc, array('version' => '1.0'));

    $f1Path = $args['<firstFile>'];
    $f2Path = $args['<secondFile>'];

    if (!file_exists($f1Path)) {
        echo("Error! File {$f1Path} is not exists." . PHP_EOL);
        return false;
    }

    if (!file_exists($f2Path)) {
        echo("Error! File {$f2Path} is not exists." . PHP_EOL);
        return false;
    }

    return makeDiff($f1Path, $f2Path);
}
