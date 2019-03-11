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
    $required = ['email', 'password'];
    $errors = [];
    $error = 'form--invalid';
    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'form__item--invalid';
        }
    }

    if (count($errors)) {
        if(!$errors['email']) {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                 $errors['email'] = 'form__item--invalid';
                 $error_email = 'Email должен быт корректным';
            }
        }
        $page_sign = include_template('login.php',[
        'error' =>  $error,
        'errors' => $errors,
        'error_email' => $error_email
        ]);
        print($page_sign);
        die();
    }
    $password_bad = $_POST['password'];
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = mysqli_real_escape_string($link, $_POST['password']);

    $sql = "SELECT email, password, name FROM user
     WHERE email IN ('$email')";
    $result = mysqli_query($link, $sql);
     $result_sql = mysqli_fetch_assoc(check($result));
        if (!$result_sql) {
            $error_email  = 'Неверный email';
            $errors['email'] = 'form__item--invalid';
                $page_sign = include_template('login.php',[
               'error' =>  $error,
                'errors' => $errors,
                'error_email' =>  $error_email
                ]);
                print($page_sign);
                die();
        }
        $pass = $result_sql['password'];
            if (password_verify($password_bad ,$pass)) {
                $_SESSION['user'] = $result_sql;

                header('Location: /');
                die();
            }
            else {
                $error_password = 'Невнерный пароль';
                $errors['password'] = 'form__item--invalid';
                $page_sign = include_template('login.php',[
                'POST' => $_POST,
                'error' =>  $error,
                'errors' => $errors,
                'error_password' => $error_password
                ]);
                print($page_sign);
                die();
            }
}

else {
    $page_login = include_template('login.php',[]);

    print($page_login);
}


