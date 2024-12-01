--
-- Файл сгенерирован с помощью SQLiteStudio v3.4.4 в Вс дек 1 12:30:31 2024
--
-- Использованная кодировка текста: UTF-8
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Таблица: assorty
DROP TABLE IF EXISTS assorty;
CREATE TABLE IF NOT EXISTS assorty (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL);
INSERT INTO assorty (id, name) VALUES (1, 'Жижи');
INSERT INTO assorty (id, name) VALUES (2, 'Одноразки');
INSERT INTO assorty (id, name) VALUES (3, 'Девайсы');
INSERT INTO assorty (id, name) VALUES (5, 'Дмитрий');

-- Таблица: tovar
DROP TABLE IF EXISTS tovar;
CREATE TABLE IF NOT EXISTS tovar (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, price REAL NOT NULL, assorty_id INTEGER, path TEXT, flavors TEXT, params TEXT, FOREIGN KEY (assorty_id) REFERENCES assorty (id));
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (21, 'KORI E-HOOKAH 100’000 puffs 🤯', 3000.0, 2, '2cac3bb5024534370f887f20f9b6da7426b998c14da828b1a0b0b028fb8ac857', 'яблоко, киви', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (26, 'Storm X 30k', 1550.0, 2, '89cb014870b6971c5dd52da7652089cba73c15dcc8594438ebfebc8923b1d880', 'вкусы уточняйте', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (33, 'Duall Hard', 350.0, 1, '9efab274c258c81ec5dce480dbed1dd6251b57536de4ca8b7e89f6d98812ef71', 'Виноград клубника, Морозный спрайт, Морозный арбуз, Клубника киви, Морозный виноград, Яблоко малина, Арбузный лимонад, Жвачка клубника киви, Смородина малина яблоко, Холодная смородина, Морозное яблоко, Сладкая мята, Ледяная малина, Спрайт арбуз лайм, Ледяная клубника с личи, Печенье карамель, Фруктовый мармелад, Морозный личи, Энергетик Виноград, Черинка арбуз, Ягоднывй чай, Ледяная кола, Персиковый чай, Персиковый лимонад, Садовые ягоды, Яблоко виноград, Клубничный лимонад, Экзотические фрукты, Лимонад с грейпфрутом, Клубника банан, Ледяная черика, Клубничный йогурт, Малиновый йогурт, Клубника лайм малина, Вишня клубника', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (34, 'ELFBAR 10’000 puffs', 700.0, 2, '10a2d07c03f6e8a7a0881d159d4963fd2d567957376a38809eab7f6654972921', 'Ежевика клюква, Лимон Лайм', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (35, 'Husky 12’000 puffs ', 1300.0, 2, 'd4351ef0756aed4773b1bf9127e47c97a6ae2f94f8ec940c25c6a1def6925014', 'разные', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (36, 'Waka 26’000 puffs', 1800.0, 2, '17cda90b27132460d1dacb19beaca3e0ecefd5418bd7acef4e4131a08b65e963', '.', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (37, 'Waka 28’000 puffs', 1800.0, 2, '5719b7d554e9e7c12f54b49e8badff69ee2c7c23ed7bb21de7bda176d76add90', '.', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (38, 'WEYI 15’000 puffs', 1000.0, 2, '86c13583bae34a48af611f909063774775d3aa5ef13c2c77228946bcb5c5d8fd', '.', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (39, 'Waka 6’000 puffs', 800.0, 2, '5db7b46a20aabde836b6fc5b60abb9f8fcd9efaab93bc42fe3f249dfa04b4e86', 'Арбуз', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (40, 'Vozol 20’000 puffs с экраном', 1150.0, 2, 'c4044ffb09dd8e618cfb615b580487926029c7b39348a62e962dd70328e94052', '.', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (41, 'Waka 10’000 puffs ', 1250.0, 2, 'c56ec569785d8e183b9fd95a6a9c557369dd75ab4249d551ae3bcf82f020f458', 'Raspberry watermelon, Chewy watermelon ', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (42, 'Snoopysmoke ', 750.0, 2, '8e862895d45ea9a4d75f0e535cd78d819d6d5fa04b74b91ea604d280c040a259', 'Черника малина лёд, Тройной ягодный лёд, Виноград лёд, Арбуз лёд, Тропические фрукты, Лесные ягоды ', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (43, 'Varmilo 28’000 puffs смартфон ', 1800.0, 2, '485387a1115de4a6da3f9aea11afe409327eca9c600a83f79de7079b235da7dd', 'Черешня ', 'Вкусы');
INSERT INTO tovar (id, name, price, assorty_id, path, flavors, params) VALUES (46, 'Manto Aio Plus', 2400.0, 3, 'd404ca6635cff1003b2f3154f7fb963deb79bce4f52f1375d97b82dd009e69c9', 'Цвета уточняйте', 'Цвета');

-- Таблица: users
DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT, password TEXT);
INSERT INTO users (id, username, password) VALUES (1, 'admin', '4af77e5d02257f1675801d85cf75e3cd64d2c7833dc37149f1d69de8fd1accf4');

COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
