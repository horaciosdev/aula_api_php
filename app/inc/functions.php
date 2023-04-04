<?php

function printData($data, $die = true)
{
    if (is_object($data) || is_array($data)) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    } else {
        echo $data;
    }

    if ($die) {
        die(PHP_EOL . 'printData TERMINADO' . PHP_EOL);
    }
}
