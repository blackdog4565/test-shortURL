-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 15 2019 г., 18:42
-- Версия сервера: 5.6.41
-- Версия PHP: 7.0.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `short_url`
--

-- --------------------------------------------------------

--
-- Структура таблицы `shorturl`
--

CREATE TABLE `shorturl` (
  `id` int(11) NOT NULL,
  `full` varchar(255) NOT NULL,
  `short` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `time_create` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `shorturl`
--

INSERT INTO `shorturl` (`id`, `full`, `short`, `time`, `time_create`) VALUES
(115, 'http://google.com', '6fuLE0pG', 0, '2019-02-15 13:42:45'),
(116, 'http://google.com', 'q8BiwNrS', 0, '2019-02-15 10:42:51'),
(117, 'http://example.com', 'c6pObwRM', 2, '2019-02-15 18:17:26'),
(118, 'http://example.com', 'QLSNPnsY', 0, '2019-02-15 18:25:48'),
(119, 'http://google.com ', 'MJ4v5201', 0, '2019-02-15 18:29:38');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `shorturl`
--
ALTER TABLE `shorturl`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `shorturl`
--
ALTER TABLE `shorturl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
