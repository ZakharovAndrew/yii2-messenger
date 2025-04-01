<?php
use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\messenger\assets\ChatAsset;

ChatAsset::register($this);

$this->title = 'Личные сообщения';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="private-chat-list">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Мои диалоги</h3>
                </div>
                <div class="panel-body">
                    <div id="private-chat-list">
                        <?php foreach ($privateChats as $chat): ?>
                            <?php $companionId = $chat->getPrivateChatCompanion(Yii::$app->user->id); ?>
                            <?php $companion = $chat->private_user1_id == Yii::$app->user->id ? 
                                $chat->privateUser2 : $chat->privateUser1; ?>
                                
                            <div class="private-chat-item media">
                                <div class="media-left">
                                    <?= Html::img(
                                        $companion->avatar ? 
                                            Url::to('@web/uploads/user_avatars/' . $companion->avatar) : 
                                            Url::to('@web/images/default-user.png'),
                                        ['class' => 'media-object', 'style' => 'width: 50px']
                                    ) ?>
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading">
                                        <?= Html::a(
                                            Html::encode($companion->username), 
                                            ['view', 'id' => $chat->id]
                                        ) ?>
                                    </h4>
                                    <p class="text-muted">Последнее сообщение...</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>