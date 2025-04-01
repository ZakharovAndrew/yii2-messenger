<?php
use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\messenger\assets\ChatAsset;

ChatAsset::register($this);

$this->title = 'Мои чаты';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="chat-index">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Мои чаты</h3>
                </div>
                <div class="panel-body">
                    <?= Html::a('Создать новый чат', ['create'], ['class' => 'btn btn-success btn-block']) ?>
                    <?= Html::a('Личные сообщения', ['private/list'], ['class' => 'btn btn-primary btn-block']) ?>
                    
                    <hr>
                    
                    <div id="chat-list">
                        <?php foreach ($userChats as $userChat): ?>
                            <div class="chat-item media">
                                <div class="media-left">
                                    <?= Html::img(
                                        $userChat->chat->avatar ? 
                                            Url::to('@web/uploads/chat_avatars/' . $userChat->chat->avatar) : 
                                            Url::to('@web/images/default-chat.png'),
                                        ['class' => 'media-object', 'style' => 'width: 50px']
                                    ) ?>
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading">
                                        <?= Html::a(
                                            Html::encode($userChat->chat->name), 
                                            ['view', 'id' => $userChat->chat->id]
                                        ) ?>
                                    </h4>
                                    <p><?= Html::encode($userChat->chat->description) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
