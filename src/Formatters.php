<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\formatStylish;
use function Differ\Formatters\Plain\formatPlain;
use function Differ\Formatters\JSON\formatJSON;

function formatDiff(array $diff, string $formatName)
{
    if ($formatName === 'stylish') {
        return formatStylish($diff);
    }
    if ($formatName === 'plain') {
        return formatPlain($diff);
    }
    if ($formatName === 'json') {
        return formatJSON($diff);
    }
    throw new \Exception("Bad output format. You may use 'stylish', 'plain' or 'json'.");
}
