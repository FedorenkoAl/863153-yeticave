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

    $sql_id = 'SELECT l.name, l.image, c.name c, l.price FROM lots l
      JOIN category c
      ON l.lots_category = c.id
      WHERE l.id = '.$lot_id;
    $result_id = mysqli_query($link, $sql_id);

    $lots_id = mysqli_fetch_row( $result_id);

    if ($lots_id == null) {
        http_response_code(404);
        $content = include_template('404.php', ['error' => '404 Страница не найдена']);
        print($content);
       die();
    }



$page_lot = include_template ('lot.php',[
    'lots_id' => $lots_id
]);

print($page_lot);
