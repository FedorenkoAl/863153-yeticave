<?php
require_once('functions.php');
require_once('mysql_helper.php');
$link = mysqli_connect('localhost', 'root', '', 'YetiCave');
mysqli_set_charset($link, "utf8");
    if (!$link) {
       print('Ошибка подключения:' . mysqli_connect_error());
       die();
    }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required = ['email', 'password', 'name', 'message'];
    $lots = $_POST;
    $errors = [];
    $error = 'form--invalid';
    $error_email = 'Введите e-mail';
    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'form__item--invalid';
        }
    }

    if($_POST['email']) {
         $error_email = 'Введите e-mail';
    }

    if (count($errors)) {
        $page_sign = include_template('sign-up.php',[
        'lots' => $lots,
        'error' =>  $error,
        'errors' => $errors,
        'error_email' => $error_email
        ]);
        print($page_sign);
         die();
    }

    $email = mysqli_real_escape_string($link, $_POST['email']);
    $sql = "SELECT id FROM user WHERE email IN ('$email') LIMIT 1";
    $r = mysqli_query($link, $sql);
    $result = mysqli_num_rows(check($r));
        if ($result) {
            $errors['email'] = 'form__item--invalid';
            $page_sign = include_template('sign-up.php',[
            'lots' => $lots,
            'error' =>  $error,
            'errors' => $errors,
            'error_email' => 'Пользователь с этим email уже зарегистрирован'
             ]);
             print($page_sign);
             die();
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $errors['email'] = 'form__item--invalid';
            $page_sign = include_template('sign-up.php',[
            'lots' => $lots,
             'error' =>  $error,
             'errors' => $errors,
             'error_email' => 'Email должен быт корректным'
             ]);
             print($page_sign);
             die();
        }

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $date_registration = date('Y'.'m'. 'd'. 'H'. 'i'. 's');
    $email = $_POST['email'];
    $name = $_POST['name'];
    $contak = $_POST['message'];

     $sql = "INSERT INTO user (date_registration, email,name, password, contak)
    VALUES (?, ?, ?, ?, ?)";

     $result_email = db_get_prepare_stmt($link, $sql,[$date_registration, $email, $name, $password, $contak]);
     $result = mysqli_stmt_execute($result_email);

     $filename = $_FILES['avatar_img']['name'];

    if ($filename) {
        $filename = $_FILES['avatar_img']['name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tmp_name = $_FILES['avatar_img']['tmp_name'];
        $file_type = finfo_file($finfo, $tmp_name);
            if ($file_type == 'image/png' || $file_type == 'image/jpeg') {
                $errors['file'] = 'Загрузите картинку в формате jpg';
            }
        move_uploaded_file($_FILES['avatar_img']['tmp_name'], 'img/' . $filename);
    }
    header('Location: /');
    die();
}
else {
    $page_sign = include_template('sign-up.php',[]);

}
 print($page_sign);
