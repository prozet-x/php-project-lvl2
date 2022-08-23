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
    return json_decode(file_get_contents($pathToFile), true);
}

function parseYAML($pathToFile)
{
    return Yaml::parseFile($pathToFile) ?? [];
}
