<?php

/****************************
 * Yii2 messenger - Chat Module for Yii2
 * 
 * This module provides a complete messaging system including:
 * - Private 1:1 conversations
 * - Group chats with different access levels
 * - Advanced moderation tools
 * - Real-time messaging capabilities
 * 
 * Key components:
 * - Chat management (create/update/delete)
 * - Message handling system
 * - User permissions management
 * - Invitation system
 * - Administrative controls
 * 
 * @version 0.0.1
 * @license MIT
 * 
 * @link https://github.com/ZakharovAndrew/yii2-messenger/
 * @copyright Copyright (c) 2025 Zakharov Andrew
 */

namespace ZakharovAndrew\messenger;

use Yii;

class Module extends \yii\base\Module
{
    /**
     * @var string path to the images directory
     */
    public $uploadDir = '';
    
    /**
     *
     * @var string source language for translation 
     */
    public $sourceLanguage = 'en-US';
    
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'ZakharovAndrew\messenger\controllers';

    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Registers the translation files
     */
    protected function registerTranslations()
    {
        Yii::$app->i18n->translations['extension/yii2-messenger/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => $this->sourceLanguage,
            'basePath' => '@vendor/zakharov-andrew/yii2-messenger/src/messages',
            'on missingTranslation' => ['app\components\TranslationEventHandler', 'handleMissingTranslation'],
            'fileMap' => [
                'extension/yii2-messenger/messenger' => 'messenger.php',
            ],
        ];
    }

    /**
     * Translates a message. This is just a wrapper of Yii::t
     *
     * @see Yii::t
     *
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($message, $params = [], $language = null)
    {
        $category = 'messenger';
        return Yii::t('extension/yii2-messenger/' . $category, $message, $params, $language);
    }
}
