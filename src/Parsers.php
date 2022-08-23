<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getParser(string $pathToFile)
{
    $extension = strtolower(pathinfo($pathToFile, PATHINFO_EXTENSION));
    if ($extension === 'json') {
        return fn ($pathToFile) => parseJSON($pathToFile);
    } elseif ($extension === 'yml' || $extension === 'yaml') {
        return fn ($pathToFile) => parseYAML($pathToFile);
    }
    throw new \Exception("Bad file format. You should pass JSON or YAML files only.");
}

function parseJSON(string $pathToFile)
{
    $fileContent = file_get_contents($pathToFile);
    if ($fileContent === false) {
        throw new \Exception('File content is not in JSON format!');
    }
    return json_decode($fileContent, true);
}

function parseYAML(string $pathToFile)
{
    return Yaml::parseFile($pathToFile) ?? [];
}
