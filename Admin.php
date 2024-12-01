<?php
session_start();

// Проверяем, если сессия не установлена, редиректим на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php'; // Ваш файл подключения к базе данных

// Обработка добавления нового ассортимента
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_assorty'])) {
        $name = $_POST['name'];
        $stmt = $db->prepare("INSERT INTO assorty (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
    }

    if (isset($_POST['add_tovar'])) {
        $name = $_POST['t_name'];
        $assorty_id = $_POST['assorty_id'];
        $flavors = $_POST['flavors'];
        $price = $_POST['price'];
        $params = $_POST['params'];
        $stmt = $db->prepare("INSERT INTO tovar (name, assorty_id, price, path, flavors, params) VALUES (:name, :assorty_id, :price, :path, :flavors, :params)");
        $stmt->execute(['name' => $name, 'assorty_id' => $assorty_id, 'price' => $price, "path" => hash("sha256", $name), "flavors" => $flavors, "params" => $params]);

        // Сохранение фото товара
        $photoPath = 'TovarPhoto/' . basename(hash("sha256", $name)) . ".png";
        move_uploaded_file($_FILES['tovar_photo']['tmp_name'], $photoPath);

        // Редирект после добавления товара, чтобы избежать повторной отправки формы
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['delete_assorty'])) {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM assorty WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    if (isset($_POST['delete_tovar'])) {
        $id = $_POST['id'];

        // Получаем путь к фотографии товара перед его удалением
        $stmt = $db->prepare("SELECT path FROM tovar WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Определяем путь к фото товара
            $photoPath = 'TovarPhoto/' . $product['path'] . '.png';

            // Удаляем фото, если оно существует
            if (file_exists($photoPath)) {
                unlink($photoPath); // Удаление файла
            }
        }

        // Удаляем товар из базы данных
        $stmt = $db->prepare("DELETE FROM tovar WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    // Редактирование ассортимента
    if (isset($_POST['edit_assorty'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];

        // Обновляем ассортименты
        $stmt = $db->prepare("UPDATE assorty SET name = :name WHERE id = :id");
        $stmt->execute(['name' => $name, 'id' => $id]);
    }

    // Редактирование товара
    if (isset($_POST['edit_tovar'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $flavors = $_POST['flavors'];
        $assorty_id = $_POST['assorty_id'];
        $params = $_POST['params']; // добавляем параметры

        // Получаем старое название товара для изменения фото
        $stmt = $db->prepare("SELECT name, path FROM tovar WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $oldProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        // Обработка фотографии товара (если загружается новая)
        if ($_FILES['tovar_photo']['error'] === UPLOAD_ERR_OK) {
            // Удаление старого фото
            $oldPhotoPath = 'TovarPhoto/' . $oldProduct['path'] . '.png';
            if (file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }

            // Сохранение нового фото товара
            $photoPath = 'TovarPhoto/' . basename(hash("sha256", $name)) . ".png";
            move_uploaded_file($_FILES['tovar_photo']['tmp_name'], $photoPath);
        }

        // Переименование фото товара, если его название изменилось
        if ($oldProduct['name'] !== $name) {
            $oldPhotoPath = 'TovarPhoto/' . $oldProduct['path'] . '.png';
            $newPhotoPath = 'TovarPhoto/' . hash('sha256', $name) . '.png';
            if (file_exists($oldPhotoPath)) {
                rename($oldPhotoPath, $newPhotoPath); // Переименование файла
            }
        }

        // Обновляем товар
        $stmt = $db->prepare("UPDATE tovar SET name = :name, price = :price, flavors = :flavors, assorty_id = :assorty_id, path = :path, params = :params WHERE id = :id");
        $stmt->execute(['name' => $name, 'price' => $price, 'flavors' => $flavors, 'assorty_id' => $assorty_id, 'id' => $id, 'path' => hash("sha256", $name), 'params' => $params]);
    }
}

// Получаем данные для отображения
$assorty = $db->query("SELECT * FROM assorty")->fetchAll(PDO::FETCH_ASSOC);
$tovars = $db->query("SELECT * FROM tovar")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <title>Админка</title>
</head>
<style>
.h1-style {
  font-size: 2rem;
  font-weight: bold;
  text-decoration: none;
  color: inherit;
  border: 0;
}
</style>
<body class="bg-dark text-white">
    <div class="container mt-5">

        <h1 class="text-white text-center">Администрирование</h1>
        <hr>
        <!-- Добавление ассортимента -->
        <button class="btn btn-link px-0 h1-style" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAssortyForm" aria-expanded="false" aria-controls="collapseAssortyForm">
            Добавление ассортимента
        </button>
        <div class="collapse" id="collapseAssortyForm">
            <h2 class="text-center">Добавить ассортимент</h2>
            <form method="POST" class="row g-3 col-md-6 mx-auto">
                <div class="col-12">
                    <label for="name" class="form-label">Название ассортимента</label>
                    <input type="text" name="name" placeholder="Название ассортимента" class="form-control" required>
                </div>
                <div class="col-12">
                    <button type="submit" name="add_assorty" class="btn btn-primary mt-1 ms-auto me-0 w-100">Добавить</button>
                </div>
            </form>
        </div>

        <hr>
        <!-- Просмотр и редактирование ассортимента -->
        <button class="btn btn-link px-0 h1-style" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAssorty" aria-expanded="false" aria-controls="collapseAssorty">
            Посмотреть добавленный ассортимент
        </button>
        <ul id="collapseAssorty" class="collapse list-group mt-3">
            <?php foreach ($assorty as $item): ?>
                <li class="mt-3 rounded list-group-item bg-light d-flex flex-nowrap justify-content-between">
                    <?php echo $item['name']; ?>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <button type="submit" name="delete_assorty" class="btn btn-danger btn-sm">Удалить</button>
                    </form>
                    <!-- Редактирование ассортимента -->
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editAssortyModal<?php echo $item['id']; ?>">Редактировать</button>
                </li>

                <!-- Модальное окно редактирования ассортимента -->
                <div data-bs-theme="dark" class="modal fade" id="editAssortyModal<?php echo $item['id']; ?>" tabindex="-1" aria-labelledby="editAssortyModalLabel<?php echo $item['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header ">
                                <h5 class="modal-title" id="editAssortyModalLabel<?php echo $item['id']; ?>">Редактировать ассортимент</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body ">
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Название ассортимента</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo $item['name']; ?>" required>
                                    </div>
                                    <button type="submit" name="edit_assorty" class="btn btn-primary w-100">Сохранить изменения</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </ul>

        <hr>
        <!-- Добавление товара -->
        <button class="btn btn-link px-0 h1-style" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTovarForm" aria-expanded="false" aria-controls="collapseTovarForm">
            Добавление товара
        </button>
        <div class="collapse" id="collapseTovarForm">
            <h2 class="mt-5 text-center">Добавить товар</h2>
            <form method="POST" enctype="multipart/form-data" class="row g-3 col-md-6 mx-auto">
                <div class="col-12">
                    <label for="t_name" class="form-label">Название товара</label>
                    <input type="text" name="t_name" id="t_name" class="form-control" placeholder="Название товара" required>
                </div>

                <div class="col-12">
                    <label for="price" class="form-label">Цена</label>
                    <input type="text" name="price" id="price" class="form-control" placeholder="Цена" required>
                </div>
                <div class="col-12">
                    <label for="params" class="form-label">Заголовок обратной стороны карточки ассортимента</label>
                    <input name="params" class="form-control" placeholder="Вкусы"></input>
                </div>
                <div class="col-12">
                    <label for="flavors" class="form-label">Список на задней стороне карточки</label>
                    <textarea id="flavors" name="flavors" rows="5" class="form-control" placeholder="Малина, Арбуз" required></textarea>
                </div>

                <div class="col-12">
                    <label for="tovar_photo" class="form-label">Фото товара</label>
                    <input type="file" name="tovar_photo" id="tovar_photo" class="form-control" accept="image/*" required>
                </div>

                <div class="col-12">
                    <label for="assorty_id" class="form-label">Выберите ассортимент</label>
                    <select name="assorty_id" id="assorty_id" class="form-select" required>
                        <option value="">Выберите ассортимент</option>
                        <?php foreach ($assorty as $item): ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" name="add_tovar" class="btn btn-primary w-100">Добавить</button>
                </div>
            </form>
        </div>

        <hr>
        <!-- Просмотр и редактирование товаров -->
        <button class="mt-2 btn btn-link px-0 h1-style" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTovar" aria-expanded="false" aria-controls="collapseTovar">
            Посмотреть добавленные товары
        </button>
        <ul class="collapse list-group mt-3" id="collapseTovar">
            <?php foreach ($tovars as $product): ?>
                <li class="list-group-item bg-light d-flex flex-wrap justify-content-between align-content-end my-3 rounded-end w-50 mx-auto">
                    <img src="TovarPhoto/<?php echo $product['path']; ?>.png" width="auto" height="200px" alt="<?php echo $product['name']; ?>">
                    <?php echo $product['name']; ?> - <?php echo $product['price']; ?> руб.
                    
                    <form method="POST" class="d-inline align-self-end">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <button type="submit" name="delete_tovar" class="btn btn-danger btn-sm">Удалить</button>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editTovarModal<?php echo $product['id']; ?>">Редактировать</button>
                    </form>
                </li>

                <!-- Модальное окно редактирования товара -->
                <div data-bs-theme="dark" class="modal  fade" id="editTovarModal<?php echo $product['id']; ?>" tabindex="-1" aria-labelledby="editTovarModalLabel<?php echo $product['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editTovarModalLabel<?php echo $product['id']; ?>">Редактировать товар</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Название товара</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Цена</label>
                                        <input type="text" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
                                    </div>  
                                    <div class="mb-3">
                                        <label for="params" class="form-label">Заголовок обратной стороны карточки ассортимента</label>
                                        <textarea name="params" class="form-control" rows="3"><?php echo $product['params']; ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="flavors" class="form-label">Вкусы</label>
                                        <textarea name="flavors" class="form-control" rows="5"><?php echo $product['flavors']; ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tovar_photo" class="form-label">Фото товара</label>
                                        <input type="file" name="tovar_photo" class="form-control" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label for="assorty_id" class="form-label">Выберите ассортимент</label>
                                        <select name="assorty_id" class="form-select">
                                            <?php foreach ($assorty as $item): ?>
                                                <option value="<?php echo $item['id']; ?>" <?php if ($product['assorty_id'] == $item['id']) echo 'selected'; ?>>
                                                    <?php echo $item['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <button type="submit" name="edit_tovar" class="btn btn-primary w-100">Сохранить изменения</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
