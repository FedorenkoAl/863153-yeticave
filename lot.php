<?php
require_once('functions.php');

$link = mysqli_connect('localhost', 'root', '', 'YetiCave');
mysqli_set_charset($link, "utf8");
    if (!$link) {
       print('Ошибка подключения:' . mysqli_connect_error());
       die();
    }


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $lot_id = mysqli_real_escape_string($link, $_GET['id']);
}

else {
   http_response_code(404);
    $content = include_template('404.php', ['error' => '404 Страница не найдена']);
    print($content);
    die();
}

$sql_id = "SELECT l.name, l.image, l.price, c.name c, r.price r, l.data_end, l.author FROM lots l
    LEFT JOIN category c
    ON l.lots_category = c.id
    LEFT JOIN rate r
    ON r.rate_lots = l.id
    WHERE l.id = $lot_id
    ORDER BY r.date_create DESC LIMIT 1";

$result_id = mysqli_query($link, $sql_id);
$lots_id = mysqli_fetch_row(check($result_id));

if($lots_id[4]) {
        $lots_id[2] = $lots_id[4];
    }


if ($lots_id === null) {
    http_response_code(404);
    $content = include_template('404.php', ['error' => '404 Страница не найдена']);
    print($content);
   die();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required = ['cost'];

    if (empty($_POST['cost'])) {
        $page_lot = include_template ('lot.php',[
        'lots_id' => $lots_id,
        'lot_id' => $lot_id,
        'error' => 'form__item form__item--invalid',
        'error_rate' => 'Введите сумму'
        ]);
        print($page_lot);
        die();
    }

    if(!ctype_digit($_POST['cost'])) {
         $error = 'form__item form__item--invalid';
        $error_rate = 'Неккоректная сумма';
        $page_lot = include_template ('lot.php',[
        'lots_id' => $lots_id,
        'lot_id' => $lot_id,
        'error' => $error,
        'error_rate' => $error_rate
         ]);
        print($page_lot);
         die();
    }

    $sql_rate = "SELECT r.price, l.price lot, l.step FROM rate r
        LEFT JOIN lots l
        ON r.rate_lots = $lot_id
        LEFT JOIN user u
        ON u.id = r.rate_user
        WHERE l.id = $lot_id
        ORDER BY r.date_create DESC LIMIT 1";
    $result_rate = mysqli_query($link, $sql_rate);
    $rate_id = mysqli_fetch_assoc(check($result_rate));
        if (!$rate_id['price']){
            $rate_id['price'] = $rate_id['lot'];
        }

    $rate = $_POST['cost'];
    $rate_max = $rate_id['price'];
    $step = $rate_id['step'];


    if (($rate_max + $step) >= $rate) {
        $error = 'form__item form__item--invalid';
        $error_rate = 'Ставка должна быть больше текущей цены';
        $page_lot = include_template ('lot.php',[
        'lots_id' => $lots_id,
        'lot_id' => $lot_id,
        'error' => $error,
        'error_rate' => $error_rate
         ]);
        print($page_lot);
         die();
    }

    $lots_id[2] =  $rate;
    $rat = mysqli_real_escape_string($link, $_POST['cost']);
    $date_create =  date('Y'.'m'. 'd'. 'H'. 'i'. 's');
    $sql = "INSERT INTO rate (date_create, price, rate_lots)
            VALUES ($date_create, $rat, $lot_id)";
    $result = mysqli_query($link, $sql);
    check($result);
}

 $page_lot = include_template ('lot.php',[
    'lots_id' => $lots_id,
    'lot_id' => $lot_id

]);
print($page_lot);






