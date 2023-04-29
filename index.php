<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Массив для временного хранения сообщений пользователю.
    $messages = array();
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '');
        $messages['success'] = 'Спасибо, результаты сохранены.';
        //При создании нового пользователя выведется его логин и пароль
        if (!empty($_COOKIE['login'])) {
            $messages['success'] = sprintf(
                'Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
                // strip_tags() удаляет html и php теги из строки, если есть
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['pass'])
            );
            setcookie('login', '');
            setcookie('pass', '');
        }
    }

    // Складываем признак ошибок в массив.
    $errors = array();
    $errors['name'] = !empty($_COOKIE['name_error']);
    $errors['bio'] = !empty($_COOKIE['bio_error']);

    if ($errors['name']) {
        $messages['name'] = 'Заполните имя латиницей<br>';
        setcookie('name_error', '');
    }

    if ($errors['bio']) {
        $messages['bio'] = 'Заполните биографию латиницей<br>';
        setcookie('bio_error', '');
    }

    // при создании сессии автоматически создается куки с именем сессии
    if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {
        printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
    }

    // Складываем предыдущие значения полей в массив, если есть.
    $values = array();
    $values['name'] = empty($_COOKIE['name_value']) ? '' : strip_tags($_COOKIE['name_value']);
    $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
    $values['birthday'] = empty($_COOKIE['birthday_value']) ? '' : strip_tags($_COOKIE['birthday_value']);
    $values['sex'] = empty($_COOKIE['sex_value']) ? '' : strip_tags($_COOKIE['sex_value']);
    $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : strip_tags($_COOKIE['limbs_value']);
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);
    $values['contract'] = empty($_COOKIE['contract_value']) ? '' : strip_tags($_COOKIE['contract_value']);

    $values['powers'] = [];
    $powersCookie = array();
    if (!empty($_COOKIE['powers_value'])) {
        $powersCookie = (array)json_decode($_COOKIE['powers_value']);
        foreach ($powersCookie as $power) {
            $values['powers'][$power] = $power;
        }
    }

    // Включаем содержимое файла form.php.
    // В нем будут доступны переменные $messages, $errors и $values для вывода
    // сообщений, полей с ранее заполненными данными и признаками ошибок.
    include('form.php');
} else {
    $user = 'u53890';
    $password = '8091112';
    $connection = new PDO("mysql:host=localhost;dbname=u53890", $user, $password, array(PDO::ATTR_PERSISTENT => true));


    $name = $_POST['field-name'];
    $email = $_POST['field-email'];
    $birthday = $_POST['field-date'];
    $sex = $_POST['radio-group-1'];
    $limbs = $_POST['radio-group-2'];
    $powers = $_POST['field-power'];
    $bio = $_POST['field-biography'];
    $contract = $_POST['cntrt'];

    $errors = FALSE;


    if (!preg_match("/^[A-z]*$/", $name)) {
        setcookie('name_error', '1');
        $errors = TRUE;
    } else {
        setcookie('name_value', $name, time() + 12 * 30 * 24 * 60 * 60);
    }

    if (!preg_match("/^[A-z]*$/", $bio)) {
        setcookie('bio_error', '1');
        $errors = TRUE;
    } else {
        setcookie('bio_value', $bio, time() + 12 * 30 * 24 * 60 * 60);
    }

    setcookie('email_value', $email, time() + 12 * 30 * 24 * 60 * 60);
    setcookie('powers_value', json_encode($powers), time() + 12 * 30 * 24 * 60 * 60);
    setcookie('birthday_value', $birthday, time() + 12 * 30 * 24 * 60 * 60);
    setcookie('sex_value', $sex, time() + 12 * 30 * 24 * 60 * 60);
    setcookie('limbs_value', $limbs, time() + 12 * 30 * 24 * 60 * 60);
    setcookie('contract_value', $contract, time() + 12 * 30 * 24 * 60 * 60);

    if ($errors) {
        header('Location: index.php');
        exit();
    } else {
        setcookie('name_error', '');
        setcookie('bio_error', '');
    }

    //обновляем данные, если пользователь залогинился
    if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
        $query = "UPDATE form SET name = :name, email= :email, birthday = :birthday, sex=:sex, limbs=:limbs, bio=:bio, contract=:contract WHERE id =:id";
        $stmt = $connection->prepare($query);

        $stmt->execute(['id' => $_SESSION['uid'], 'name' => $name,'email' => $email, 'birthday' =>$birthday, 'sex' => $sex, 'limbs' => $limbs, 'bio' => $bio, 'contract' => $contract]);


        $toDelete = $connection->prepare('DELETE FROM form_power WHERE form_id = ?');
        $toDelete->execute([$_SESSION['uid']]);


        $ex2 = $connection->prepare("INSERT INTO form_power (form_id, power_id) VALUES (:form_id, (SELECT id FROM powers WHERE power=:power))");

        foreach ($powers as $power) {
            $ex2->bindParam(':form_id', $_SESSION['uid']);
            $ex2->bindParam(':power', $power);
            $ex2->execute();
        }
    } else {
        //создаем логин и пароль новому пользователю и сохраняем все данные в бд
        $id = uniqid();
        $hash = md5($id);
        $login = substr($hash, 0, 10);
        $pass = substr($hash, 10, 15);
        $hash_pass = substr(hash("sha256", $pass), 0, 16);

        $ex1 = $connection->prepare("INSERT INTO form (name, email, birthday, sex, limbs, bio, contract) VALUES (?, ?, ?, ?, ?, ?, ?)");

        $ex1->execute(array($name, $email, $birthday, $sex, $limbs, $bio, $contract));
        $id_user = $connection->lastInsertId();

        $query2 = "INSERT INTO form_power (form_id, power_id) VALUES (:form_id, (SELECT id FROM powers WHERE power=:power))";

        $ex2 = $connection->prepare($query2);

        foreach ($powers as $power) {
            $ex2->bindParam(':form_id', $id_user);
            $ex2->bindParam(':power', $power);
            $ex2->execute();
        }

        $stmt = $connection->prepare("INSERT INTO user_pass (user_id, login, hash_pass) VALUES (?, ?, ?)");
        $stmt->execute(array($id_user, $login, $hash_pass));

        $_SESSION['login'] = $login;
        $_SESSION['uid'] = $id_user;
        setcookie('login', $login, time() + 30 * 24 * 60 * 60);
        setcookie('pass', $pass, time() + 30 * 24 * 60 * 60);
    }
    setcookie('save', '1', time() + 12 * 30 * 24 * 60 * 60);
    header('Location: index.php');
}