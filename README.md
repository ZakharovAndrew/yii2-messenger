<div align="center">

# 🚀 Yii2 messenger - Chat Module for Yii2 📨✨

[![Latest Stable Version](https://poser.pugx.org/zakharov-andrew/yii2-messenger/v/stable)](https://packagist.org/packages/zakharov-andrew/yii2-messenger)
[![Total Downloads](https://poser.pugx.org/zakharov-andrew/yii2-messenger/downloads)](https://packagist.org/packages/zakharov-andrew/yii2-messenger)
[![License](https://poser.pugx.org/zakharov-andrew/yii2-messenger/license)](https://packagist.org/packages/zakharov-andrew/yii2-messenger)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

</div>

<p align="center">
The <strong>Yii2 messenger</strong> provides a complete solution for messaging between users, including private conversations and group chats with advanced management capabilities.
</p>

<p align="center">
  <a href="#-features">Features</a> •
  <a href="#-installation">Installation</a> •
  <a href="#-usage">Usage</a> •
  <a href="#-system-requirements">System Requirements</a> •
  <a href="#-contributing">Contributing</a> •
  <a href="#-license">License</a>
</p>

<p align="center">
  <a href="README.ru.md">🇷🇺 Русская версия</a>
</p>

---


## 🔥 Features

### 💬 Messaging System
- Real-time text messaging ⚡
- Message history view 📜
- Delete your own messages 🗑️
- Admin ability to delete any messages 🛡️

### 👥 Group Chats
- Create chats with name and description 🏷️
- Upload chat avatar 🖼️
- Different access types:
  - 🔗 Public (via link)
  - ✉️ Invite-only
  - 🔒 Private (manual addition only)
- View participant list 👀

### 🛠 Chat Management (for admins)
- Assign administrators 👑
- Flexible permission system:
  - Delete messages 🗑️
  - Ban users ⛔
  - Temporary mute users 🔕
  - Add/remove participants ➕➖
  - Edit chat info ✏️
  - Manage other admins 🛡️
- View statistics and activity 📊

### 🤝 Private Messages
- Automatic chat creation for first message 🤖
- List of all conversations 📋
- Search message history 🔍

## 🚀 Installation

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

## 🎨 Frontend Integration

The module provides:
- Ready-to-use AJAX controllers for easy integration 📡
- Responsive interface (mobile-friendly) 📱
- Customizable styling options 🎨

## 📌 System Requirements
- PHP 7.4+
- Yii2 2.0.40+
- MySQL 5.7+ or PostgreSQL 9.5+

## 👥 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
