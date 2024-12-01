<?php
// Подключаемся к новой базе данных
$db = new PDO('sqlite:database.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Функция для чтения и выполнения SQL из файла
function executeSqlFromFile($db, $filePath) {
    // Проверяем, существует ли файл
    if (!file_exists($filePath)) {
        throw new Exception("Файл не найден: " . $filePath);
    }

    // Читаем содержимое файла
    $sql = file_get_contents($filePath);

    // Разделяем файл на отдельные SQL запросы, предполагая, что они разделены символом ";"
    $queries = explode(';', $sql);

    // Выполняем каждый запрос
    foreach ($queries as $query) {
        // Убираем возможные лишние пробелы в начале и конце строки
        $query = trim($query);
        
        // Пропускаем пустые строки
        if (empty($query)) {
            continue;
        }
        
        try {
            // Выполняем запрос
            $db->exec($query);
        } catch (PDOException $e) {
            echo "Ошибка выполнения запроса: " . $e->getMessage() . "\n";
        }
    }
}

// Вызываем функцию для выполнения SQL из файла test.sql
try {
    executeSqlFromFile($db, 'test.sql');
    echo "SQL команды успешно выполнены!\n";
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}

?>
