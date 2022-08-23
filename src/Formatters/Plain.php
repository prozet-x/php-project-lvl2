<?php

namespace Differ\Formatters\Plain;

function getformattedValue(mixed $value)
{
    $valueAsStinrg = json_encode($value);
    if ($valueAsStinrg === false) {
        throw new \Exception("Can not convert value " . $value . " to JSON!");
    }
    return str_replace('"', "'", $valueAsStinrg);
}

function getUpdateString(string $upLevel, string $currentLevel, mixed $value, mixed $oldValue)
{
    return "Property '" . $upLevel . $currentLevel . "' was updated."
        . " From " . (is_array($oldValue) ? "[complex value]" : (getformattedValue($oldValue)))
        . " to " . (is_array($value) ? "[complex value]" : (getformattedValue($value)));
}

function getAddString(string $upLevel, string $currentLevel, mixed $value)
{
    return "Property '" . $upLevel . $currentLevel . "' was added with value: "
        . (is_array($value) ? "[complex value]" : getformattedValue($value));
}

function getDeleteString(string $upLevel, string $currentLevel)
{
    return "Property '" . $upLevel . $currentLevel . "' was removed";
}

function formatPlain(array $diff, string $upLevel = '')
{
    if (count(array_filter($diff, fn ($elem) => $elem['changes'] !== 'n')) > 0) {
        usort($diff, fn ($a, $b) => strcmp($a['key'], $b['key']));
    }

    $res = array_reduce(
        $diff,
        function ($acc, $elem) use ($upLevel) {
            if ($elem['changes'] === 'n' and is_array($elem['value'])) {
                return [...$acc, formatPlain($elem['value'], $upLevel . $elem['key'] . ".")];
            }
            //$res;
            switch ($elem['changes']) {
                case 'a':
                    $res = [...$acc, getAddString($upLevel, $elem['key'], $elem['value'])];
                    break;
                case 'r':
                    $res = [...$acc, getDeleteString($upLevel, $elem['key'])];
                    break;
                case 'u':
                    $res = [...$acc, getUpdateString($upLevel, $elem['key'], $elem['value'], $elem['oldValue'])];
            }
            return $res ?? $acc;
        },
        []
    );
    return implode(PHP_EOL, $res);
}
