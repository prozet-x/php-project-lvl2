<?php

namespace Differ\Differ;

use function Differ\Parsers\getParser;
use function Differ\Formatters\formatDiff;

function getFormattedValue(mixed $value)
{
    return is_array($value) ? makeDiff($value, $value) : $value;
}

function getNewDiffElem(string $key, string $changes, mixed $value, mixed ...$args)
{
    $res = ['key' => $key, 'changes' => $changes, 'value' => getFormattedValue($value)];
    if (count($args) === 1) {
        return array_merge($res, ['oldValue' => getFormattedValue($args[0])]);
    }
     return $res;
}

function makeDiff(array $before, array $after)
{
    $keys = array_unique(array_merge(array_keys($before), array_keys($after)));

    return array_reduce(
        $keys,
        function ($acc, $key) use ($before, $after) {
            $inBefore = array_key_exists($key, $before);
            $inAfter = array_key_exists($key, $after);
            if (!$inBefore) {
                return [...$acc, getNewDiffElem($key, 'a', $after[$key])];
            }
            if (!$inAfter) {
                return [...$acc, getNewDiffElem($key, 'r', $before[$key])];
            }
            if (is_array($before[$key]) and is_array($after[$key])) {
                return [...$acc, ['key' => $key, 'changes' => 'n', 'value' => makeDiff($before[$key], $after[$key])]];
            }
            return $before[$key] === $after[$key]
                       ? [...$acc, getNewDiffElem($key, 'n', $after[$key])]
                       : [...$acc, getNewDiffElem($key, 'u', $after[$key], $before[$key])];
        },
        []
    );
}

function getFileDataAsArray(string $pathToFile)
{
    $parser = getParser($pathToFile);
    return $parser($pathToFile);
}

function getNotExistingFiles(string ...$files)
{
    $badPaths = array_reduce(
        $files,
        fn ($acc, $pathToFile) => file_exists($pathToFile) ? $acc : [...$acc, $pathToFile],
        []
    );
    return $badPaths;
}

function genDiff(string $pathToFile1, string $pathToFile2, string $outputFormat = 'stylish')
{
    $notExistingFiles = getNotExistingFiles($pathToFile1, $pathToFile2);
    if (count($notExistingFiles) > 0) {
        $message = "Files are not found:" . PHP_EOL
                . implode(PHP_EOL, $notExistingFiles) . PHP_EOL
                . "You should enter an existing files paths.";
        throw new \Exception($message);
    }
    $f1data = getFileDataAsArray($pathToFile1);
    $f2data = getFileDataAsArray($pathToFile2);
    $res = makeDiff($f1data, $f2data);
    return formatDiff($res, $outputFormat);
}
