<?php
$is_auth = rand(0, 1);
$user_name = 'Алексей';

require_once('functions.php');

$link = mysqli_connect('localhost', 'root', '', 'YetiCave');
mysqli_set_charset($link, "utf8");

    if (!$link) {
       print('Ошибка подключения:' . mysqli_connect_error());
       die();
    }
    else {
        $sql = 'SELECT name FROM category';
        $result = mysqli_query($link, $sql);

            if ($result) {
                 $category = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
            else {
                $error = mysqli_error($link);
                print('Ошибка MySQL' . $error);
                die();
            }

        $sql_lots = 'SELECT l.name, l.image, c.name cat, l.price FROM lots l
        JOIN category c ON l.lots_category = c.id';
        $result_lots = mysqli_query($link, $sql_lots);

            if ($result_lots) {
                $lots = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);
            }
            else {
                $error = mysqli_error($link);
                print('Ошибка MySQL' . $error);
                die();
            }
        }

$page_content = include_template('index.php', [
    'lots' => $lots,
    'category' => $category
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'category' => $category,
    'title' => 'Главная страница'
]);

print($layout_content);
