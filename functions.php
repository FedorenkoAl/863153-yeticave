<?php
function money ($amount){
    return number_format(ceil($amount),0," "," ") . ' ₽';
}

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function time_end($time_current,$ts_midnight) {
    date_default_timezone_set("Europe/Moscow");
    $secs_to_midnight = $ts_midnight - $time_current;
    $hours = floor($secs_to_midnight / 3600);
    $minutes = floor(($secs_to_midnight % 3600) / 60);

    return "$hours : $minutes";
}
