<?php

namespace Differ\Formatters\Stylish;

const TAB = '    ';

function getformattedValue($value)
{
    return trim(json_encode($value), '"');
}

function getFormattedString($key, $value, $deep, $changesSymbol)
{
    return is_array($value)
    ? "  $changesSymbol $key: " . formatStylish($value, $deep + 1)
    : "  $changesSymbol $key: " . getformattedValue($value) . PHP_EOL;
}

function formatStylish($diff, $deep = 0)
{
    $getFormattedStringFunc = function ($key, $value, $deep, $changesSymbol = ' ') {
        return getFormattedString($key, $value, $deep, $changesSymbol);
    };

    if (count(array_filter($diff, fn ($elem) => $elem['changes'] !== 'n')) > 0) {
        usort($diff, fn ($a, $b) => strcmp($a['key'], $b['key']));
    }

    return '{' . PHP_EOL
        . array_reduce(
            $diff,
            function ($acc, $elem) use ($deep, $getFormattedStringFunc) {
                $res = str_repeat(TAB, $deep);
                switch ($elem['changes']) {
                    case 'r':
                        $res .= $getFormattedStringFunc($elem['key'], $elem['value'], $deep, '-');
                        break;
                    case 'a':
                        $res .= $getFormattedStringFunc($elem['key'], $elem['value'], $deep, '+');
                        break;
                    case 'u':
                        $res .= $getFormattedStringFunc($elem['key'], $elem['oldValue'], $deep, '-')
                        . str_repeat(TAB, $deep)
                        . $getFormattedStringFunc($elem['key'], $elem['value'], $deep, '+');
                        break;
                    default:
                        $res .= $getFormattedStringFunc($elem['key'], $elem['value'], $deep);
                }
                return $acc . $res;
            },
            ''
        )
        . str_repeat(TAB, $deep) . '}' . PHP_EOL;
}
