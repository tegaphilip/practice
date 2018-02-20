<?php

$string = 'aaaaaabbbbbbbbcccccccpppqtuv';

print_r(compress($string));

function compress($string)
{
    $array = [];
    $length = strlen($string);

    $previous = substr($string, 0, 1);
    $array[$previous] = 1;
    for ($i = 1; $i < $length; $i++) {
        $next = substr($string, $i, 1);

        if ($next == $previous){
            $array[$next] = $array[$next] + 1;
        } else{
            $array[$next] = 1;
        }

        $previous = $next;
    }

    $result = '';

    foreach ($array as $key => $value) {
        $result .= $key;
        if ($value > 1) {
            $result .= $value;
        }
    }

    return $result;
}