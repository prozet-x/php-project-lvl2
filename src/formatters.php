<?php

namespace Differ\Formatters;

const TAB = '    ';
const MAP = [
    ''
];

function getFormattedString($key, $value, $changesSymbol)
{
    return "  $changesSymbol $key: $value" . PHP_EOL;
}

function formatStylish($diff, $deep = 0)
{
    $getFormattedStringFunc = function ($key, $value, $changesSymbol = ' ') {
        return getFormattedString($key, $value, $changesSymbol);
    };

    return '{' . PHP_EOL
        . array_reduce(
                $diff,
                function($acc, $elem) use ($deep, $getFormattedStringFunc) {
                    $res = str_repeat(TAB, $deep);
                    if (is_array($elem['value'])) {
                        $res .= ' ' . $elem['key'] . ': ' . formatStylish($elem['value'], $deep + 1);
                    } else {
                        switch ($elem['changes']) {
                            case 'r': $res .= $getFormattedStringFunc($elem['key'], $elem['value'], '-'); break;
                            case 'a': $res .= $getFormattedStringFunc($elem['key'], $elem['value'], '+'); break;
                            case 'u': $res .= $getFormattedStringFunc($elem['key'], $elem['oldValue'], '-');
                                $res .= $getFormattedStringFunc($elem['key'], $elem['value'], '+'); break;
                            default: $res .= $getFormattedStringFunc($elem['key'], $elem['value']);
                        }
                    }
                    return $acc . $res;
                },
                ''
        )
             . str_repeat(TAB, $deep) . '}' . PHP_EOL;
}
