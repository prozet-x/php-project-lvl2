<?php

namespace Differ\Formatters\Plain;

function getformattedValue($value)
{
    return str_replace('"', "'", json_encode($value));
}

function formatPlain($diff, $upLevel = '')
{
    if (count(array_filter($diff, fn ($elem) => $elem['changes'] !== 'n')) > 0) {
        usort($diff, fn ($a, $b) => strcmp($a['key'], $b['key']));
    }

    return array_reduce(
        $diff,
        function ($acc, $elem) use ($upLevel) {
            if ($elem['changes'] === 'n' and is_array($elem['value'])) {
                return $acc . formatPlain($elem['value'], $upLevel . $elem['key'] . ".");
            }
            switch ($elem['changes']) {
                case 'a':
                    return $acc . "Property '" . $upLevel . $elem['key'] . "' was added with value: "
                        . (is_array($elem['value']) ? "[complex value]" : (getformattedValue($elem['value'])))
                        . PHP_EOL;
                case 'r':
                    return $acc . "Property '" . $upLevel . $elem['key'] . "' was removed" . PHP_EOL;
                case 'u':
                    return $acc . "Property '" . $upLevel . $elem['key'] . "' was updated."
                        . " From "
                        . (is_array($elem['oldValue']) ? "[complex value]" : (getformattedValue($elem['oldValue'])))
                        . " to "
                        . (is_array($elem['value']) ? "[complex value]" : (getformattedValue($elem['value'])))
                        . PHP_EOL;
            }
            return $acc;
        },
        ''
    );
}
