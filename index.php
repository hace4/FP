<?php
include 'db.php';
ini_set('display_errors', 0);  // Отключаем отображение ошибок
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); // Указываем, какие ошибки следует записывать (но не показывать)
// Получение ассортимента из базы данных
$query = $db->query("SELECT * FROM assorty");
$assorty = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Fabrica Para</title>
</head>
<body class="bg-dark text-white">
    <div class="container text-center mt-5">
        <h1>Fabrica Para</h1>
        
        <div class="container text-center mt-5">

        <?php if (!isset($_GET['page']) || $_GET['page'] != "Catalog"): ?>
        <div id="main" class="mt-5">
            <a href="?page=Catalog" class="btn btn-light m-2" style="width: 150px;">Каталог</a> <!-- Кнопка Каталог с переходом на сайт -->
            <br>
            <a href="?page=Sales" class="btn btn-light m-2" style="width: 150px;">Акции</a> <!-- Кнопка Акции без обработчика -->
        </div>
        <?php endif; ?>

        <?php
        if($_GET['page'] == "Sales"){
            echo '<h2 class="mt-5">Этот функционал еще в разработке</h2>';
        }
        // Проверяем, есть ли выбранный ассортимент и выводим соответствующие товары
        else if (isset($_GET['assorty_id']) && $_GET['page'] == "Catalog") {
            $assorty_id = $_GET['assorty_id'];
            $tovars = $db->prepare("SELECT * FROM tovar WHERE assorty_id = ?");
            $tovars->execute([$assorty_id]);
            $products = $tovars->fetchAll(PDO::FETCH_ASSOC);
            echo '<a href="index.php?page=Catalog" class="btn btn-light m-3">Вернуться к ассортименту</a>';
            if ($products): ?>

            <div class="input-group mt-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Поиск товаров">
            </div>
                <h2 class="mt-5">Товары:</h2>
                <div data-assortid="<?php echo $_GET['assorty_id'];?>" id="searchResults" class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 mt-3">
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                        <div class="tovar-card card h-100">
                <!-- Изображение товара -->
                        <div class="image-container">
                            <img src="<?php echo 'TovarPhoto/' . hash('sha256', $product['name']) . '.png'; ?>" 
                                class="card-img-top" alt="<?php echo $product['name']; ?>" 
                                style="object-fit: cover; min-height: 250px; max-height: 250px;">
                            <!-- Список вкусов, который появляется при наведении -->
                            <div class="flavor-list">
                                <ul>
                                    <p class="card-text text-success fw-bold">Вкусы:</p>
                                    <?php foreach (explode(' ', str_replace(',', '',$product['flavors'])) as $flavor): ?>
                                        <li><?php echo $flavor; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body align-content-center">
                            <!-- Название товара -->
                            <h5 class="card-title text-dark"><?php echo $product['name']; ?></h5>
                        </div>
                        <div class="card-footer text-end">
                            <p class="card-text text-success fw-bold"><?php echo $product['price']; ?> ₽</p>
                        </div>
                    </div>
                </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <h2 class="mt-5">Нет товаров в данном ассортименте.</h2>
            <?php endif;
        }
            
         else if($_GET['page'] == "Catalog"){ 
            $assorty = $db->query("SELECT * FROM assorty")->fetchAll(PDO::FETCH_ASSOC); // Получаем ассортимент

            if ($assorty): ?>
                <a href="index.php" class="btn btn-light m-3">Вернуться на главную</a>
                <h2 class="mt-5">Ассортимент:</h2>
                <div class="mt-3">
                    <?php foreach ($assorty as $item): ?>
                        <a href="index.php?page=Catalog&assorty_id=<?php echo $item['id']; ?>" class="btn btn-light m-2"><?php echo $item['name']; ?></a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <h2 class="mt-5">Нет ассортимента.</h2>
            <?php endif;
        } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
    <script>

            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            console.log(searchResults.dataset)
            const assortyId = searchResults.dataset.assortid;
            
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();
                if (query.length >= 0) {
                    fetch(`search.php?search=${encodeURIComponent(query)}&assorty_id=${assortyId}`)
                        .then(response => response.json())
                        .then(data => {
                            searchResults.innerHTML = ''; // Очищаем старые результаты
                            if(data.length == 0){
                                searchResults.innerHTML = "<h2 class='mx-auto'>Ничего не найдено</h2>";
                            }else{

                            
                            data.forEach(product => {
                                // Создаем карточку товара
                                const flavors = product.flavors.replace(',', ' ').split(' ');
                                let flavorListHtml = '';

                                // Добавляем каждый вкус в <li>
                                flavors.forEach(flavor => {
                                    flavorListHtml += `<li>${flavor}</li>`;
                                });
                                const col = document.createElement('div');
                                col.className = 'col';
                                col.innerHTML = `
                                    <div class="tovar-card card h-100">
                                    <div class="image-container">
                                        <img src="TovarPhoto/${product.path}.png" class="card-img-top" alt="${product.name}" style="object-fit: cover; min-height: 250px; max-height: 250px;">
                                        <div class="flavor-list">
                                            <ul>
                                                <p class="card-text text-success fw-bold">Вкусы:</p>
                                                ${flavorListHtml}
                                            </ul>
                                        </div>
                                    </div>
                                        <div class="card-body">
                                            <h5 class="card-title text-dark">${product.name}</h5>
                                            <p class="card-text text-success fw-bold">${product.price} ₽</p>
                                        </div>
                                    </div>`;
                                searchResults.appendChild(col);
                            });
                        }
                    })
                        .catch(err => console.error('Ошибка поиска:', err));
                } else {
                    searchResults.innerHTML = ''; // Очищаем результаты, если строка пустая
                }
            });
        </script>
</body>
</html>