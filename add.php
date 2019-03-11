<?php
require_once('functions.php');
require_once('mysql_helper.php');
$link = mysqli_connect('localhost', 'root', '', 'YetiCave');
mysqli_set_charset($link, "utf8");
    if (!$link) {
       print('Ошибка подключения:' . mysqli_connect_error());
       die();
    }

    if (!isset($_SESSION['user'])) {
       http_response_code(403);
         die();
    }

$sql = 'SELECT id, name FROM category';
$result = mysqli_query($link, $sql);
    if ($result) {
        $category = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    else {
        $error = mysqli_error($link);
        print('Ошибка MySQL' . $error);
        die();
    }

    $page_add = include_template('add.php',[
        'category' => $category,
        'option' => 'Выберите категорию'
    ]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $lots = $_POST;
    $required = ['lot_name', 'message', 'lot_rate', 'lot_step', 'lot_date', 'category'];
    $dict = ['lot_name' => 'Введите наименование лота', 'category' => 'Выберете катеорию','message'=> 'Напишите описание лота', 'lot_rate' => 'Введите начальную цену','lot_step' => 'Введите шаг ставки', 'lot_date' => 'Введите дату завершения торгов'];
    $errors = [];

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'form__item--invalid';
        }
    }
    if ($_POST['category'] == 'Выберите категорию') {
        $errors['category'] = 'form__item--invalid';
    }

    $cat = $_POST['category'];
    $error = 'form--invalid';
    the_end($errors, $lots, $category, $cat, $errors, $error);

    if(!ctype_digit($_POST['lot_rate'])) {
        $errors['lot_rate'] = 'form__item--invalid';
        the_end($errors, $lots, $category, $cat, $errors, $error);
    }

    if(!ctype_digit($_POST['lot_step']))  {
         $errors['lot_step'] = 'form__item--invalid';
          the_end($errors, $lots, $category, $cat, $errors, $error);
    }

     $filename = $_FILES['lot_img']['name'];

    if (!$filename) {
        $errors['lot_img'] = 'form__item--uploaded';
        $page_add = include_template('add.php',[
        'lots' => $lots,
        'category' => $category,
        'option' =>  $_POST['category'],
        'errors' => $errors
        ]);
        print($page_add);
        die();
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tmp_name = $_FILES['lot_img']['tmp_name'];
    $file_type = finfo_file($finfo, $tmp_name);
        if ($file_type == 'image/png' || $file_type == 'image/jpeg') {
            $errors['file'] = 'Загрузите картинку в формате jpg';
        }
    move_uploaded_file($_FILES['lot_img']['tmp_name'], 'img/' . $filename);

    $data_end = $_POST['lot_date'];
    $creation_date = time();
    $time = strtotime( $data_end);
        if (($time - $creation_date) < 86400) {
            $errors['lot_date'] = 'form__item--invalid';
            the_end($errors, $lots, $category, $cat, $errors, $error);
    }

    $safe_cat =  mysqli_real_escape_string($link, $cat);
    $cat_id = "SELECT id FROM category WHERE name IN ('$safe_cat')";
    $result_cat = mysqli_query($link, $cat_id);
    if ($result_cat) {
        $categor = mysqli_fetch_row($result_cat);
    }
    else {
        $error = mysqli_error($link);
        print('Ошибка MySQL' . $error);
        die();
    }

    $file = 'img/' . $filename;
    $time = date('Y'.'m'. 'd'. 'H'. 'i'. 's');
    $name = ($_POST['lot_name']);
    $description = ($_POST['message']);
    $price = ($_POST['lot_rate']);
    $step = ($_POST['lot_step']);

    $sql_id = "INSERT INTO lots (creation_date, name, description,image, price, step, lots_category, data_end)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $result_id = db_get_prepare_stmt($link, $sql_id, [$time,  $name, $description, $file, $price, $step, $categor[0], $data_end]);

    $result = mysqli_stmt_execute( $result_id);
        if ($result) {
            $id = mysqli_insert_id($link);
            header('Location: lot.php?id=' . $id);

            $page_lot = include_template ('lot.php',[
            'lots_id' => $lots_id
            ]);
            print($page_lot);
            die();
        }
}

else {
     $page_add = include_template('add.php', [
        'category' => $category,
        'option' => 'Выберите категорию'
     ]);
}
print($page_add);



