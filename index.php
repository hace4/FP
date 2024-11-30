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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">



    <title>Fabrica Para</title>
</head>
<body class="bg-dark text-white 100vh">
    <div class="container text-center mt-5">
        <?php if(isset($_GET["assorty_id"])) {
            echo '<div class="sticky-top bg-dark 
            rounded_header ptH-3"><h1 class="mt-2">Fabrica Para</h1><h2 class=" mx-auto ">Товары</h2></div>';
        }else{
            echo '<h1 class="sticky-top pt-4">Fabrica Para</h1>';
        }
        ?>
        <div class="container text-center mt-5">

        <?php if (!isset($_GET['page']) || $_GET['page'] != "Catalog"): ?>
        <div id="main" class="mt-5">
            <a href="?page=Catalog" class="btn btn-light m-2 bg_btn" style="width: 150px;">Каталог</a> <!-- Кнопка Каталог с переходом на сайт -->
            <br>
            <a href="?page=Sales" class="btn btn-light m-2 bg_btn" style="width: 150px;">Акции</a> <!-- Кнопка Акции без обработчика -->
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
           
            if ($products): ?>
            <div class="input-group mt-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Поиск товаров">
            </div>
                
                <div data-assortid="<?php echo $_GET['assorty_id'];?>" id="searchResults" class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 mt-3">
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                        <div class="card_rounded tovar-card card h-100">
                <!-- Изображение товара -->
                        <div class="image-container">
                            <img loading="lazy" src="<?php echo 'TovarPhoto/' . hash('sha256', $product['name']) . '.png'; ?>" 
                                class="card-img-top card_rounded img-fluid" alt="<?php echo $product['name']; ?>" 
                                style="object-fit: cover; min-height: 200px; max-height: 200px;">
                            <!-- Список вкусов, который появляется при наведении -->
                            <div class="flavor-list card_rounded">
                                <ul>
                                    <p class="card-text text-success fw-bold">Вкусы:</p>
                                    <?php foreach (explode(' ', str_replace(',', '',$product['flavors'])) as $flavor): ?>
                                        <li><?php echo $flavor; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body align-content-center my-0 pb-0 pt-1">
                            <!-- Название товара -->
                            <p class="card-title text-dark"><?php echo $product['name']; ?></p>
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
            echo '<div class="bg-dark sticky-bottom mx-auto rounded_footer "><a href="index.php?page=Catalog" class="btn btn-light bg_btn m-3 ">Вернуться к ассортименту</a></div>';
        }
            
         else if($_GET['page'] == "Catalog"){ 
            $assorty = $db->query("SELECT * FROM assorty")->fetchAll(PDO::FETCH_ASSOC); // Получаем ассортимент

            if ($assorty): ?>
                <a href="index.php" class="btn bg_btn  btn-light m-3">Вернуться на главную</a>
                <h2 class="mt-5">Ассортимент:</h2>
                <div class="mt-3">
                    <?php foreach ($assorty as $item): ?>
                        <a href="index.php?page=Catalog&assorty_id=<?php echo $item['id']; ?>" class="bg_btn btn btn-light m-2"><?php echo $item['name']; ?></a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <h2 class="mt-5">Нет ассортимента.</h2>
            <?php endif;
        } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
                                    <div class="card_rounded tovar-card card h-100">
                                    <div class="image-container">
                                        <img loading="lazy" src="TovarPhoto/${product.path}.png" class="card-img-top" alt="${product.name}" style="object-fit: cover; min-height: 250px; max-height: 250px;">
                                        <div class="flavor-list">
                                            <ul>
                                                <p class="card-text text-success fw-bold">Вкусы:</p>
                                                ${flavorListHtml}
                                            </ul>
                                        </div>
                                    </div>
                                        <div class="card-body align-content-center my-0 pb-0 pt-1">
                                            <p class="card-title text-dark">${product.name}</p>
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