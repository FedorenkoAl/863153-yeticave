<?php
require_once('functions.php');
$link = mysqli_connect('localhost', 'root', '', 'YetiCave');
mysqli_set_charset($link, "utf8");
    if (!$link) {
       print('Ошибка подключения:' . mysqli_connect_error());
       die();
    }
    unset($_SESSION['user']);

  header('Location: /');
  die();

