<div align="center">

# 🚀 Yii2 messenger - Модуль чатов для Yii2 📨✨

[![Latest Stable Version](https://poser.pugx.org/zakharov-andrew/yii2-messenger/v/stable)](https://packagist.org/packages/zakharov-andrew/yii2-messenger)
[![Total Downloads](https://poser.pugx.org/zakharov-andrew/yii2-messenger/downloads)](https://packagist.org/packages/zakharov-andrew/yii2-messenger)
[![License](https://poser.pugx.org/zakharov-andrew/yii2-messenger/license)](https://packagist.org/packages/zakharov-andrew/yii2-messenger)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

</div>

<p align="center">
**Yii2 messenger** предоставляет полный функционал для обмена сообщениями между пользователями, включая личные переписки и групповые чаты с расширенными возможностями управления.
</p>

<p align="center">
  <a href="README.ru.md">🇷🇺 Русская версия</a>
</p>

---


## 🔥 Основные возможности

### 💬 Система сообщений
- Отправка текстовых сообщений в реальном времени ⚡
- Просмотр истории сообщений 📜
- Удаление своих сообщений 🗑️
- Возможность админам удалять любые сообщения 🛡️

### 👥 Групповые чаты
- Создание чатов с названием и описанием 🏷️
- Загрузка аватара чата 🖼️
- Разные типы доступа:
  - 🔗 По ссылке (публичный)
  - ✉️ По приглашению
  - 🔒 Только по добавлению (приватный)
- Просмотр списка участников 👀

### 🛠 Управление чатами (для администраторов)
- Назначение администраторов 👑
- Гибкая система прав:
  - Удаление сообщений 🗑️
  - Блокировка пользователей ⛔
  - Заглушение на время 🔕
  - Добавление/удаление участников ➕➖
  - Изменение информации чата ✏️
  - Управление другими админами 🛡️
- Просмотр статистики и активности 📊

### 🤝 Личные сообщения
- Автоматическое создание чата при первом сообщении 🤖
- Список всех диалогов 📋
- Поиск по истории переписк 🔍

## 🚀 Установка

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ composer require zakharov-andrew/yii2-messenger
```
or add

```
"zakharov-andrew/yii2-messenger": "*"
```

to the ```require``` section of your ```composer.json``` file.

Subsequently, run

```
./yii migrate/up --migrationPath=@vendor/zakharov-andrew/yii2-messenger/migrations
```

in order to create the settings table in your database.

Or add to console config

```php
return [
    // ...
    'controllerMap' => [
        // ...
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@console/migrations', // Default migration folder
                '@vendor/zakharov-andrew/yii2-messenger/src/migrations'
            ]
        ]
        // ...
    ]
    // ...
];
```

## Usage

Add this to your main configuration's modules array

```php
    'modules' => [
        'messenger' => [
            'class' => 'ZakharovAndrew\messenger\Module',
            'bootstrapVersion' => 5, // if use bootstrap 5
            'defaultChatImage' => '/images/default-product-image.jpg', // Path to the default image for a chat
            'uploadWebDir' => '/web/path/to/upload/dir/'
        ],
        'imageupload' => [
            'class' => 'ZakharovAndrew\imageupload\Module',
            'uploadDir' => '/path/to/upload/dir/',
        ],
        // ...
    ],
```
**Note**: the maximum number of additional parameters is 3. Change the value of **uploadDir** to the directory for uploading images. Uses the [yii2-image-upload-widget](https://github.com/ZakharovAndrew/yii2-image-upload-widget) module to upload images.

Add this to your main configuration's urlManager array

```php
'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // ...
                'chat/<url:[\w\d\-]+>' => 'messenger/chat/view',
                // ...
            ],
        ],
```

## 🎨 Интеграция с фронтендом

Модуль предоставляет:
- Готовые AJAX-контроллеры для быстрой интеграции 📡
- Адаптивный интерфейс (mobile-friendly) 📱
- Возможность кастомизации стилей 🎨

## 📌 Системные требования
- PHP 7.4+
- Yii2 2.0.40+
- MySQL 5.7+ или PostgreSQL 9.5+

## 👥 Вклад в проект

Вклады приветствуются! Пожалуйста, не стесняйтесь отправлять Pull Request.

1. Сделайте форк репозитория
2. Создайте новую ветку для своей фичи (`git checkout -b feature/amazing-feature`)
3. Закоммитьте изменения (`git commit -m 'Добавлена потрясающая фича'`)
4. Запушьте ветку (`git push origin feature/amazing-feature`)
5. Откройте Pull Request

## 📄 Лицензия

Этот проект лицензирован под лицензией MIT – см. файл [LICENSE](LICENSE) для подробностей.
