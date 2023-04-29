<?php
/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/
// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.


// Если нажали на выход, то удаляем сессию и удаляем куки
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['exit'])) {
        session_destroy();
        foreach ($_COOKIE as $item => $value) {
            setcookie($item, '', 1);
        }
        header('Location: ./login.php');
    }

    if (!empty($_SESSION['login'])) {
        print ('<div>Вы авторизованы как ' . $_SESSION['login'] . ', uid ' . $_SESSION['uid'] . '</div>')
        // После знака "?" идут GET-параметры запроса
        ?>
        <a href="./login.php?exit=1">Выйти</a>
        <a href="./">Главная страница</a>
        <?php
        exit();
    } else {
        ?>

        <form action="./login.php" method="post">
            <input name="login" placeholder="login" required>
            <input name="pass" placeholder="pass" required>
            <input type="submit" value="Войти">
        </form>
        <a href="./">Главная страница</a>
        <?php
    }
} // Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {

    $user = 'u53890';
    $pass = '8091112';
    $db = new PDO('mysql:host=localhost;dbname=u53890', $user, $pass, [PDO::ATTR_PERSISTENT => true]);

    // Сравниваем введеные логин и пароль с логином и хеш паролем, сохраннеными в таблице
    $stmt1 = $db->prepare('SELECT user_id FROM user_pass WHERE login = ? && hash_pass = ?');
    $stmt1->execute([$_POST['login'], substr(hash("sha256", $_POST['pass']), 0, 16)]);
    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        print("Неверный логин или пароль");
        exit();
    }

    // Если все ок, то авторизуем пользователя.
    $_SESSION['login'] = $_POST['login'];
    // Записываем ID пользователя.
    $_SESSION['uid'] = $row['user_id'];

    // Записываем данные из бд, хранящиеся под id юзера в куки
    // Эти данные автоматически заполнят форму
    $stmt1 = $db->prepare('SELECT name, email, birthday, sex, limbs, bio, contract FROM form WHERE id = ?');
    $stmt1->execute([$_SESSION['uid']]);
    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
    //PDO::FETCH_ASSOC: возвращает массив, индексированный именами столбцов результирующего набора

    setcookie('name_value', $row['name'], time() + 30 * 24 * 60 * 60);
    setcookie('email_value', $row['email'], time() + 30 * 24 * 60 * 60);
    setcookie('birthday_value', $row['birthday'], time() + 30 * 24 * 60 * 60);
    setcookie('sex_value', $row['sex'], time() + 30 * 24 * 60 * 60);
    setcookie('limbs_value', $row['limbs'], time() + 30 * 24 * 60 * 60);
    setcookie('bio_value', $row['bio'], time() + 30 * 24 * 60 * 60);

    $stmt2 = $db->prepare("SELECT power FROM powers WHERE id IN (SELECT power_id FROM form_power WHERE form_id = ?)");
    $stmt2->execute([$_SESSION['uid']]);
    $powers = array();
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $powers[$row['power']] = $row['power'];
    }
    setcookie('powers_value', json_encode($powers), time() + 12 * 30 * 24 * 60 * 60);


    // Делаем перенаправление.
    header('Location: ./login.php');
}