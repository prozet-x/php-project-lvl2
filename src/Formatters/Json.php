<?php

namespace Differ\Formatters\JSON;

function formatJSON($diff)
{
    return json_encode($diff);
}
