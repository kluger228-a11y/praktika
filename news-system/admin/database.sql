-- Система управления новостями - База данных

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `news_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `news_system`;

-- --------------------------------------------------------
-- Таблица пользователей
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','moderator','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Таблица категорий
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Таблица новостей
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `views` int(11) DEFAULT 0,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `author_id` (`author_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `news_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `news_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Таблица комментариев
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `news_id` (`news_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Таблица логов действий
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Вставка тестовых данных

-- Категории
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Политика', 'politics', 'Новости политики и государственного управления'),
('Экономика', 'economy', 'Экономические новости и аналитика'),
('Технологии', 'technology', 'Новости технологий и IT индустрии'),
('Спорт', 'sports', 'Спортивные новости и события'),
('Культура', 'culture', 'Культурные события и искусство'),
('Наука', 'science', 'Научные открытия и исследования'),
('Здоровье', 'health', 'Новости медицины и здорового образа жизни');

-- Пользователи (пароль для всех: 123456)
INSERT INTO `users` (`username`, `email`, `password`, `role`, `bio`) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Администратор системы'),
('moderator', 'moderator@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'moderator', 'Модератор контента'),
('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Активный пользователь'),
('user2', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Любитель новостей');

-- Новости
INSERT INTO `news` (`title`, `slug`, `content`, `excerpt`, `author_id`, `category_id`, `views`, `meta_title`, `meta_description`) VALUES
('Добро пожаловать в систему управления новостями', 'welcome-to-news-system', 'Это первая новость в нашей системе. Здесь вы найдете самые свежие и интересные новости из разных областей.

Система позволяет:
- Просматривать новости
- Комментировать материалы
- Создавать свой профиль
- Участвовать в обсуждениях

Мы постоянно работаем над улучшением платформы и будем рады вашим отзывам!', 'Первая новость в нашей системе управления новостями', 1, 3, 45, 'Добро пожаловать в систему новостей', 'Первая новость в системе управления новостями'),
('Новые функции платформы', 'new-platform-features', 'Мы рады сообщить о добавлении новых функций в нашу платформу:

## Что нового:

1. **Улучшенный поиск** - теперь можно искать по категориям и датам
2. **Мобильная версия** - адаптивный дизайн для всех устройств
3. **Система уведомлений** - получайте уведомления о новых комментариях
4. **API для разработчиков** - интеграция с другими системами

Будем рады вашим предложениям по улучшению!', 'Обзор новых функций платформы', 2, 3, 32, 'Новые функции платформы', 'Новые функции платформы управления новостями'),
('Экономический рост в текущем квартале', 'economic-growth-current-quarter', 'По данным министерства экономики, в текущем квартале наблюдается стабильный рост основных экономических показателей.

## Основные достижения:

| Показатель | Значение | Изменение |
|------------|----------|-----------|
| ВВП        | +3.2%    | ↑         |
| Инфляция   | 4.1%     | ↓         |
| Безработица| 5.3%     | ↓         |

Эксперты отмечают положительную динамику и прогнозируют дальнейший рост в следующем квартале.', 'Экономические показатели за квартал показывают рост', 1, 2, 28, 'Экономический рост', 'Экономические показатели за квартал'),
('Спортивные достижения наших атлетов', 'sports-achievements', 'Наши спортсмены показали отличные результаты на международных соревнованиях.

### Золотые медали:
- Алексей Иванов - легкая атлетика (100 метров)
- Мария Петрова - плавание (200 метров баттерфляем)
- Команда по волейболу - чемпионат Европы

### Серебряные медали:
- Дмитрий Смирнов - борьба
- Екатерина Козлова - гимнастика

Поздравляем наших чемпионов и желаем дальнейших успехов!', 'Достижения спортсменов на международной арене', 2, 4, 51, 'Спортивные достижения', 'Наши спортсмены завоевали медали на международных соревнованиях'),
('Новые технологии в образовании', 'new-technologies-in-education', 'Современные технологии кардинально меняют подход к образованию. 

## Основные тенденции:

**1. Онлайн-обучение**
Платформы для дистанционного образования становятся все популярнее.

**2. VR/AR технологии**
Виртуальная и дополненная реальность используются для создания иммерсивных обучающих сред.

**3. Искусственный интеллект**
AI помогает создавать персонализированные учебные планы.

**4. Геймификация**
Игровые элементы повышают мотивацию студентов.

Будущее образования за технологиями!', 'Как технологии меняют образовательный процесс', 1, 3, 19, 'Технологии в образовании', 'Новые технологии кардинально меняют подход к образованию'),
('Культурные события месяца', 'cultural-events-month', 'В этом месяце нас ждут интересные культурные события:

### Выставки:
- "Современное искусство" в Центральном музее (15-30 числа)
- "История фотографии" в Галерее искусств

### Концерты:
- Симфонический оркестр (20 числа)
- Джазовый фестиваль (25-27 числа)

### Театр:
- Премьера "Гамлет" в Театре драмы
- "Ревизор" в Молодежном театре

Не пропустите эти интересные события!', 'Обзор культурных событий на текущий месяц', 2, 5, 23, 'Культурные события', 'Культурные события на текущий месяц');

-- Комментарии
INSERT INTO `comments` (`news_id`, `user_id`, `content`, `is_approved`) VALUES
(1, 3, 'Отличная новость! Ждем продолжения.', 1),
(1, 4, 'Интересно, какие еще функции планируются?', 1),
(2, 3, 'Улучшенный поиск - это то, что нужно!', 1),
(3, 4, 'Хорошие новости для экономики!', 1),
(4, 3, 'Поздравляю наших спортсменов!', 1),
(5, 4, 'Технологии действительно меняют все сферы жизни.', 1);

-- Логи действий
INSERT INTO `activity_logs` (`user_id`, `action`, `description`, `ip_address`) VALUES
(1, 'user.login', 'Вход в систему', '127.0.0.1'),
(1, 'news.create', 'Создана новость "Добро пожаловать в систему управления новостями"', '127.0.0.1'),
(2, 'news.create', 'Создана новость "Новые функции платформы"', '127.0.0.1'),
(3, 'comment.create', 'Добавлен комментарий к новости 1', '127.0.0.1'),
(4, 'comment.create', 'Добавлен комментарий к новости 1', '127.0.0.1'),
(3, 'comment.create', 'Добавлен комментарий к новости 2', '127.0.0.1'),
(1, 'news.create', 'Создана новость "Экономический рост в текущем квартале"', '127.0.0.1');

COMMIT;