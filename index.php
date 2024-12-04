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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Подключение иконок -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script>
        console.log(Object.keys(Telegram.WebApp.initDataUnsafe).length);
    // Проверяем, доступно ли приложение через Telegram WebApp
        // Проверяем, доступно ли приложение через Telegram WebApp
        if (typeof Telegram === 'undefined' || !Telegram.WebApp || !Telegram.WebApp.initDataUnsafe || Object.keys(Telegram.WebApp.initDataUnsafe).length == 0) {
        // Если объект WebApp или initDataUnsafe не существует, значит, приложение не через Telegram
        alert('Это приложение доступно только через Telegram!');
        window.location.href = "https://t.me/";  // Перенаправляем пользователя на Telegramяяя
    
    }
    ;</script>

    <title>Fabrica Para</title>
</head>

<body class="bg-\\\text-white 100vh">
    <div class="container text-center mt- ">
        <?php if (isset($_GET["assorty_id"])) {
            echo '<img src="icon.jpg" class="mb-5 mt-4" width="100%" style="margin-top: -10px; border-radius: 27px;">';
        } else {
            echo '<img src="icon.jpg" class="mb-5 mt-4" width="100%" style="margin-top: -10px; border-radius: 27px;">';
        }
        ?>
        <div class="container text-center  h-100 w-100">



            <?php
            if ($_GET['page'] == "Sales") {
                echo '<h2 class="mt-5">Этот функционал еще в разработке</h2>';
            }
            // Проверяем, есть ли выбранный ассортимент и выводим соответствующие товары
            else if (isset($_GET['assorty_id']) && $_GET['page'] == "Catalog") {
                $assorty_id = $_GET['assorty_id'];
                $tovars = $db->prepare("SELECT * FROM tovar WHERE assorty_id = ?");
                $tovars->execute([$assorty_id]);
                $products = $tovars->fetchAll(PDO::FETCH_ASSOC);

                if ($products): ?>
                    <div class="vh-80 pb-6">
                        <div id="searchField" class="input-group mt-4 mx-auto border-0">
                            <span class="input-group-text border-0 " style="background-color: #bdc6b5; "><i class="bi bi-search" style="color: #2f3c35; "></i></span> <!-- Иконка лупы -->
                            <input type="text" id="searchInput" class="form-control border-0" placeholder="Поиск товаров" style="background-color: #bdc6b5; color: #2f3c35;">
                        </div>

                        <!-- Индикатор загрузки (скрыт по умолчанию) -->
                        <div id="loading-spinner" style="display:none; z-index: 100000;">
                            <div class="circle-spinner">
                                <div class="spinner-bar"></div>
                            </div>
                            <p>Оформляем ваш заказ...</p>
                        </div>


                        <div data-assortid="<?php echo $_GET['assorty_id']; ?>" id="searchResults" class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 mt-3">
                            <?php foreach ($products as $product): ?>
                                <div class="col">
                                    <div class="card_rounded tovar-card card h-100 olive-card">
                                        <!-- Изображение товара -->

                                        <div class=" <?php echo !empty($product['params']) ?  'image-container_tovar' :  ""; ?>">
                                            <img loading="lazy" src="<?php echo 'TovarPhoto/' . hash('sha256', $product['name']) . '.png'; ?>"
                                                class="card-img-top card_rounded img-fluid" alt="<?php echo $product['name']; ?>"
                                                style="object-fit: cover; min-height: 200px; max-height: 200px;">
                                            <?php if (!empty($product['params'])): ?>
                                                <div class="flavor-list card_rounded">
                                                    <ul>
                                                        <?php if (!empty($product['flavors'])): ?>
                                                            <p class="card-text text-success fw-bold"><?php echo htmlspecialchars($product['params']); ?>:</p>
                                                            <?php foreach (explode('^$^', str_replace(',', '^$^', $product['flavors'])) as $flavor): ?>
                                                                <hr>
                                                                <li><?php echo trim($flavor); ?></li>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                        <div class="card-body align-content-center my-0 pb-0 pt-1">
                                            <!-- Название товара -->
                                            <p class="card-title text-dark"><?php echo $product['name']; ?></p>
                                            <div class="d-flex flex-nowrap w-100 align-items-center justify-content-center mb-3 ">
                                                <p class="card-text text-success fw-bold my-0 "><?php echo $product['price']; ?> ₽ </p>
                                                <!-- <button class="  btn btn-transperent order-btn pt-0" data-product-id="<?php //echo $product['id']; 
                                                                                                                            ?>" data-product-name="<?php //echo $product['name']; 
                                                                                                                                                                                ?>" data-product-price="<?php //echo $product['price']; 
                                                                                                                                                                                                                                        ?>" data-product-image="<?php //echo 'TovarPhoto/' . hash('sha256', $product['name']) . '.png'; 
                                                                                                                                                                                                                                                                                                ?>"><i class="bi bi-basket fs-4"></i></button>  -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <h2 class="mt-5">Нет товаров в данном ассортименте.</h2>
                <?php endif;
                echo '<button id="scrollToTopBtn" class=" olve-btn border-0 hide d-flex alert alert-light fixed-bottom pe-4 mt-3 mb-2 py-0 mb-6 ms-auto me-end justify-content-end align-items-center rounded-0 rounded-start"  style="width: 55px; ">
                         <i id="scrollToTopArrow" class="bi bi-arrow-up-circle py-0 my-0 olve-btn" style="font-size: 1.5rem;"></i> <!-- Иконка стрелки вверх -->
                       </button>
                       <div class=" fixed-bottom mx-auto rounded_footer">
                       <a href="index.php?page=Catalog" class="btn olve-btn bg_btn m-3 back_Btn rounded">Вернуться к ассортименту</a>
                     </div>
                     
                        ';
            } else {
                $assorty = $db->query("SELECT * FROM assorty")->fetchAll(PDO::FETCH_ASSOC); // Получаем ассортимент

                if ($assorty): ?>

                    <h2 class="mt-2">Ассортимент:</h2>
                    <div class="mt-3 w-100">
                        <?php foreach ($assorty as $item): ?>
                            <a href="index.php?page=Catalog&assorty_id=<?php echo $item['id']; ?>" class="bg_btn olve-btn  border-0 btn btn-light m-2 w-100"><?php echo $item['name']; ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <h2 class="mt-5">Нет ассортимента.</h2>
            <?php endif;
                echo '';
                echo '<div class="fixed-bottom mx-auto  rounded_footer  d-flex flex-column gap-2">
            <a href="https://t.me/Qnc1teee" class=" bb2 text-white bg_btn  mx-2 w-100 rounded">Курьер</a>
            <a href="https://t.me/+jhhFUi7OrNE0ZDYy" class=" bb2 text-white bg_btn  mx-2 w-100 rounded">Отзывы</a>
            <a href="https://yandex.ru/maps/-/CHAKa6Zz" class=" bb2 text-white bg_btn  mx-2 w-100 rounded">Местоположение</a>
            <h1 class=" fs-1 title main-footer mb-1">Самые топовые цены и оригинальная продукция в «ParDar Т-48»
            </h1>
            </div>';
                //<a href="https://t.me/-1002072187822" class="text-white bg_btn mb-1 mx-2 w-100">Флудилка</a>
            } ?>
        </div>
        <!-- Модальное окно Bootstrap -->
        <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderModalLabel">Ваш заказ оформлен</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Ваш заказ успешно оформлен, наш менеджер скоро с вами свяжется.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <!-- <script async src="https://telegram.org/js/telegram-widget.js?7"
    data-telegram-login="Tetsik_some_huys_bot" data-size="large" data-radius="10" data-auth-url="https://d3d5-91-77-161-155.ngrok-free.app"> -->
        </script>

        <script>
            document.querySelectorAll('.order-btn').forEach(button => {
                button.addEventListener('click', function() {
                    Telegram.WebApp.ready();

                    // Получаем данные о пользователе
                    const user = Telegram.WebApp.initDataUnsafe.user;
                    console.log("User data:", user);
                    // Показываем индикатор загрузки
                    document.getElementById('loading-spinner').style.display = 'flex';

                    const productId = this.dataset.productId;
                    const productName = this.dataset.productName;
                    const productPrice = this.dataset.productPrice;
                    const productImage = this.dataset.productImage;

                    // Отправляем данные на сервер
                    fetch('send_to_telegram.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                productId: productId,
                                productName: productName,
                                productPrice: productPrice,
                                productImage: productImage,
                                userId: user.id,
                                userName: user.username
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Скрываем индикатор загрузки
                            document.getElementById('loading-spinner').style.display = 'none';

                            // Пытаемся оформить заказ
                            if (data.success) {
                                // Показываем модальное окно, если заказ успешен
                                const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
                                orderModal.show();
                            } else {
                                alert('Ошибка при оформлении!');
                            }

                        })
                        .catch(() => {
                            // В случае ошибки скрываем индикатор и показываем alert
                            document.getElementById('loading-spinner').style.display = 'none';
                            alert('Ошибка при соединении с сервером!');
                        });
                });
            });


            const scrollToTopBtn = document.getElementById('scrollToTopBtn');
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            console.log(searchResults.dataset)
            const assortyId = searchResults.dataset.assortid;
            window.addEventListener('scroll', () => {
                if (window.scrollY - searchInput.offsetHeight - 5 > searchInput.getBoundingClientRect().height + searchInput.offsetHeight) {
                    // Показываем кнопку, когда строка поиска выходит за экран
                    scrollToTopBtn.classList.add('show'); // Добавляем класс для анимации
                    scrollToTopBtn.classList.remove('hide');
                } else {
                    // Скрываем кнопку, когда строка поиска видна
                    scrollToTopBtn.classList.add('hide');
                    scrollToTopBtn.classList.remove('show'); // Убираем класс для скрытия
                }
            });
            // Закрытие клавиатуры при нажатии на клавишу "Enter"
            searchInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    searchInput.blur(); // Убираем фокус с поля ввода
                }
            });

            // При нажатии на кнопку, прокручиваем страницу вверх
            scrollToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();
                if (query.length >= 0) {
                    fetch(`search.php?search=${encodeURIComponent(query)}&assorty_id=${assortyId}`)
                        .then(response => response.json())
                        .then(data => {
                            searchResults.innerHTML = ''; // Очищаем старые результаты
                            if (data.length == 0) {
                                searchResults.innerHTML = "<h2 class='mx-auto'>Ничего не найдено</h2>";
                            } else {
                                data.forEach(product => {
                                    // Получаем метку вкусов из params, если она существует
                                    console.log(product)
                                    const flavorsLabel = product.params;
                                    const flavors = product.flavors.replace(',', '^$^').split('^$^');
                                    let flavorListHtml = '';

                                    // Добавляем каждый вкус в <li>, если есть хотя бы один вкус
                                    if (flavors.length > 0 && flavors.some(flavor => flavor.trim() !== '')) {
                                        flavors.forEach(flavor => {
                                            flavorListHtml += `<hr><li>${flavor.trim()}</li>`;
                                        });
                                    }

                                    const col = document.createElement('div');
                                    col.className = 'col';
                                    col.innerHTML = `
                                    <div class="card_rounded tovar-card card h-100 olive-card">
                                        <div class="image-container_tovar">
                                            <img loading="lazy" src="TovarPhoto/${product.path}.png" class="card-img-top card_rounded img-fluid" alt="${product.name}" style="object-fit: cover; min-height: 200px; max-height: 200px;">
                                            
                                            ${flavorListHtml ? `
                                                <div class="flavor-list card_rounded">
                                                    <ul>
                                                        <p class="card-text text-success fw-bold sticky-top">${flavorsLabel}:</p>
                                                        ${flavorListHtml}
                                                    </ul>
                                                </div>
                                            ` : ''}
                                        </div>
                                        <div class="card-body align-content-center my-0 pb-0 pt-1">
                                            <p class="card-title text-dark">${product.name}</p>
                                            <div><p class="card-text text-success fw-bold">${product.price} ₽</p></div>
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