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
        $lots = $_POST;
        $errors = [];
        $error = 'form--invalid';
        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'form__item--invalid';
            }
        }

        if($_POST['email']) {
             $error_email = 'Введите e-mail';
        }

         if($_POST['password']) {
             $error_password = 'Введите пароль';
        }

        if (count($errors)) {
            $page_sign = include_template('login.php',[
             'lots' => $lots,
            'error' =>  $error,
            'errors' => $errors,
             'error_email' => $error_email,
             'error_password' => $error_password
            ]);
            print($page_sign);
             die();
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $errors['email'] = 'form__item--invalid';
            $page_sign = include_template('login.php',[
            'lots' => $lots,
             'error' =>  $error,
             'errors' => $errors,
             'error_email' => 'Email должен быт корректным'
             ]);
             print($page_sign);
             die();
        }
    $password_bad = $_POST['password'];


    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = mysqli_real_escape_string($link, $_POST['password']);

    $sql = "SELECT * FROM user
     WHERE email IN ('$email')";
    $result = mysqli_query($link, $sql);

            $result_sql = mysqli_fetch_row($result);
            $pass = $result_sql[4];
            if ($pass) {
                if (password_verify($password_bad ,$pass)) {
                    $_SESSION['user'] = $result_sql;
                      header('Location: /');
                    die();
                }
                else {
                     $errors['password'] = 'form__item--invalid';
                    $page_sign = include_template('login.php',[
                    'lots' => $lots,
                     'error' =>  $error,
                     'errors' => $errors,
                     'error_password' => 'Неверный пароль'
                     ]);
                     print($page_sign);
                     die();
                }
        }
        else {
             $errors['email'] = 'form__item--invalid';
            $page_sign = include_template('login.php',[
            'lots' => $lots,
             'error' =>  $error,
             'errors' => $errors,
             'error_email' => 'Неверный Email'
             ]);
             print($page_sign);
             die();
        }
}

else {
    $page_login = include_template('login.php',[]);
     print($page_login);
    }


