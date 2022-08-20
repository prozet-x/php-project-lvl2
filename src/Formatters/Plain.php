<?php

namespace Differ\Formatters\Plain;

function getformattedValue($value)
{
    return str_replace('"', "'", json_encode($value));
}

function getUpdateString($upLevel, $currentLevel, $value, $oldValue)
{
    return "Property '" . $upLevel . $currentLevel . "' was updated."
        . " From " . (is_array($oldValue) ? "[complex value]" : (getformattedValue($oldValue)))
        . " to " . (is_array($value) ? "[complex value]" : (getformattedValue($value)));
}

function getAddString($upLevel, $currentLevel, $value)
{
    return "Property '" . $upLevel . $currentLevel . "' was added with value: "
        . (is_array($value) ? "[complex value]" : getformattedValue($value));
}

function getDeleteString($upLevel, $currentLevel)
{
    return "Property '" . $upLevel . $currentLevel . "' was removed";
}

function formatPlain($diff, $upLevel = '')
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
            switch ($elem['changes']) {
                case 'a':
                    return [...$acc, getAddString($upLevel, $elem['key'], $elem['value'])];
                case 'r':
                    return [...$acc, getDeleteString($upLevel, $elem['key'])];
                case 'u':
                    return [...$acc, getUpdateString($upLevel, $elem['key'], $elem['value'], $elem['oldValue'])];
            }
            return $acc;
        },
        []
    );
    return implode(PHP_EOL, $res);
}
