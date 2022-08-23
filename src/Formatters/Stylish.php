<?php

namespace Differ\Formatters\Stylish;

const TAB = '    ';

function getformattedValue(mixed $value)
{
    $valueAsStinrg = json_encode($value);
    if ($valueAsStinrg === false) {
        throw new \Exception("Can not convert value " . $value . " to JSON!");
    }
    return trim($valueAsStinrg, '"');
}

function getFormattedString(string $key, mixed $value, int $deep, string $changesSymbol = ' ')
{
    return is_array($value)
    ? "  $changesSymbol $key: " . formatStylish($value, $deep + 1)
    : "  $changesSymbol $key: " . getformattedValue($value);
}

function formatStylish(array $diff, int $deep = 0)
{
    if (count(array_filter($diff, fn ($elem) => $elem['changes'] !== 'n')) > 0) {
        $sortedDiff = \Functional\sort($diff, fn ($a, $b) => strcmp($a['key'], $b['key']));
    }

    $offset = str_repeat(TAB, $deep);
    $result = array_reduce(
        $sortedDiff ?? $diff,
        function ($acc, $elem) use ($deep, $offset) {
            switch ($elem['changes']) {
                case 'r':
                    return [...$acc, $offset . getFormattedString($elem['key'], $elem['value'], $deep, '-')];
                case 'a':
                    return [...$acc, $offset . getFormattedString($elem['key'], $elem['value'], $deep, '+')];
                case 'u':
                    return [
                            ...$acc,
                            $offset . getFormattedString($elem['key'], $elem['oldValue'], $deep, '-'),
                            $offset . getFormattedString($elem['key'], $elem['value'], $deep, '+')
                        ];
            }
            return [...$acc, $offset . getFormattedString($elem['key'], $elem['value'], $deep)];
        },
        ['{']
    );
    return implode(PHP_EOL, $result) . PHP_EOL . $offset . '}';
}
