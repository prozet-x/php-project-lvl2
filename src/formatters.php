<?php

namespace Differ\Formatters;

const TAB = '    ';

function getFormattedString($key, $value, $deep, $changesSymbol)
{
    return is_array($value)
    ? "  $changesSymbol $key: " . formatStylish($value, $deep + 1)
    : "  $changesSymbol $key: $value" . PHP_EOL;
}

function formatStylish($diff, $deep = 0)
{
    $getFormattedStringFunc = function ($key, $value, $deep, $changesSymbol = ' ') {
        return getFormattedString($key, $value, $deep, $changesSymbol);
    };

    return '{' . PHP_EOL
        . array_reduce(
                $diff,
                function($acc, $elem) use ($deep, $getFormattedStringFunc) {
                    $res = str_repeat(TAB, $deep);
                    switch ($elem['changes']) {
                        case 'r': $res .= $getFormattedStringFunc($elem['key'], $elem['value'], $deep, '-'); break;
                        case 'a': $res .= $getFormattedStringFunc($elem['key'], $elem['value'], $deep, '+'); break;
                        case 'u': $res .= $getFormattedStringFunc($elem['key'], $elem['oldValue'], $deep, '-')
                             . str_repeat(TAB, $deep)
                             . $getFormattedStringFunc($elem['key'], $elem['value'], $deep, '+'); break;
                        default: $res .= $getFormattedStringFunc($elem['key'], $elem['value'], $deep);
                    }
                    return $acc . $res;
                },
                ''
        )
        . str_repeat(TAB, $deep) . '}' . PHP_EOL;
}
