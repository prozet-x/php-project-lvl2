<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\formatStylish;
use function Differ\Formatters\Plain\formatPlain;

function formatDiff($diff, $formatName)
{
    if ($formatName === 'stylish') {
        return formatStylish($diff);
    }
    if ($formatName === 'plain') {
        return formatPlain($diff);
    }
    throw new \Exception("Bad output format. You may use 'stylish', 'plain' or 'json'.");
}
