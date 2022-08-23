<?php

namespace Differ\Formatters\JSON;

function formatJSON(array $diff)
{
    return json_encode($diff);
}
