<?php

function sortByName($a, $b)
{
    if ($a == $b) {
        return 0;
    }

    return $a < $b ? -1 : 1;
}

$data = ['taju', 'tega', 'abies', 'bomboy', 'sola'];
$data2 = ['taju', 'tega', 'abies', 'bomboy', 'sola'];

usort($data, 'sortByName');
uasort($data2, 'sortByName');
echo '<pre>';
print_r($data);
print_r($data2);