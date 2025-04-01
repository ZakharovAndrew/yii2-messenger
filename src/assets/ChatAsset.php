<?php
/**
 * @link https://github.com/ZakharovAndrew/yii2-messenger
 * @copyright Copyright (c) 2025 Zakharov Andrey
 */

namespace ZakharovAndrew\messenger\assets;

use yii\web\AssetBundle;

class ChatAsset extends AssetBundle
{
    public $sourcePath = '@vendor/zakharov-andrew/yii2-messenger/src/assets';

    public $css = [
        'css/chat.css',
    ];

    public $js = [
    //    'js/script.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap5\BootstrapAsset',
    ];
}
