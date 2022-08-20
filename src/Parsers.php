<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

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
    throw new \Exception("Bad file format. You should pass JSON or YAML files only.");
}

function parseJSON($pathToFile)
{
    $handler = fopen($pathToFile, 'r');
    $res = json_decode(fread($handler, filesize($pathToFile)), true);
    fclose($handler);
    return $res;
}

function parseYAML($pathToFile)
{
    return Yaml::parseFile($pathToFile) ?? [];
}
