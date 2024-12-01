<?php
session_start();
include 'db.php'; // Ваш файл подключения к базе данных

// Проверяем, был ли отправлен POST запрос с логином и паролем
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Подготовка запроса для поиска пользователя в базе
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Если пользователь найден и пароль совпадает
    if ($user && ($user['password'] == hash("sha256",$password))) {
        $_SESSION['user_id'] = $user['id']; // Сохраняем информацию о пользователе в сессии
        header('Location: admin.php'); // Переходим на страницу администрирования
        exit();
    } else {
        $error = "Неверный логин или пароль!";
    }
}
?>

<!-- Форма для входа -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
</head>
<body>
    <h2>Вход в админку</h2>
    <form method="POST">
        <label for="username">Логин</label>
        <input type="text" name="username" required>
        <label for="password">Пароль</label>
        <input type="password" name="password" required>
        <button type="submit">Войти</button>
    </form>
    <?php if (isset($error)) { echo '<p>' . $error . '</p>'; } ?>
</body>
</html>
