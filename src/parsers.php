<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

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