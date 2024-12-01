<?php
// Получаем данные из запроса
$data = json_decode(file_get_contents('php://input'), true);

// Проверяем, пришли ли все данные
if (!isset($data['productName'], $data['productPrice'], $data['productImage'], $data['userId'], $data['userName'])) {
    echo json_encode(['success' => false, 'message' => 'Не все данные переданы']);
    exit;
}

$productName = $data['productName'];
$productPrice = $data['productPrice'];
$productImage = $data['productImage'];
$userId = $data['userId']; // ID пользователя
$userName = $data['userName']; // Имя пользователя

// Ваш токен бота и чат ID
$botToken = "7693761118:AAE147f6yxKx2MNd90BiBHccv05N_P_SB9k";
$chatId = "-1002216844212"; // Это чат ID вашего канала или группы

// Сообщение, которое будет отправлено в Telegram
$message = "Новый заказ от пользователя $userName (ID: $userId):\n\nТовар: $productName\nЦена: $productPrice ₽";

// Отправляем сообщение в Telegram
$url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);

// Отправка фото товара
$imageUrl = "https://fabricapara.shop/$productImage"; // Убедитесь, что путь правильный
$imageUrl = str_replace(" ", "%20", $imageUrl); // Для правильной обработки пробелов в URL

$imageMessage = "https://api.telegram.org/bot$botToken/sendPhoto?chat_id=$chatId&photo=$imageUrl";

// Инициализируем cURL для отправки сообщений
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);

// Отправка изображения
$ch = curl_init($imageMessage);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);

// Ответ, чтобы клиент знал, что все прошло успешно
echo json_encode(['success' => true]);
