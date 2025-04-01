<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use ZakharovAndrew\messenger\models\ChatUser;
use ZakharovAndrew\messenger\assets\ChatAsset;

ChatAsset::register($this);

$this->title = $chat->name;
$this->params['breadcrumbs'][] = ['label' => 'Мои чаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    #chat-messages {background-color: #e9edef; padding: 1rem;}
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    .message-in {
        align-items: flex-start;
    }
    .message-out {
        align-items: flex-end;
        margin-left: auto;
    }
    .message-item {
        max-width: 60%;
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
    }
    .message-in .message-bubble {
        background-color:#ffffff;
        ;
        border-top-left-radius: 0;
    }

    .message-out .message-bubble {
        background-color: #e2ffc7;
        border-bottom-right-radius: 0;
    }

    .message-bubble {
        padding: 10px 15px;
        border-radius: 15px;
        position: relative;
        word-break: break-word;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    .message-info {
        font-size: 12px;
        color: #888;
        margin-top: 5px;
        display: flex;
        align-items: center;
    }
    .message-bubble .message-header {
        max-width: 100%;
        white-space: nowrap;
        display: flex;
        font-weight: bold;
        font-size:12px;
        text-overflow:ellipsis;
        -webkit-font-smoothing:antialiased;
        line-height:20px;
        overflow-x:hidden;
        overflow-y:hidden;
    }
    .message-out .message-header {
        display:none;
    }
    
    /* footer */
    .panel-footer {
        padding:12px;
        background-color: #fff;
        border:1px solid #d1d5db;
    }
    .panel-footer .flex {
        align-items: center;
        display: flex;
    }
    .flex-1 {
        flex: 1 1 0%;
    }
    .panel-footer input[type="text"] {
        padding:8px 16px;
        border-radius: 35px;
        background-color: #f3f4f6;
        border:none;
    }
</style>

<div class="chat-view">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Информация о чате</h3>
                </div>
                <div class="panel-body">
                    <div class="text-center">
                        <?= Html::img(
                            $chat->avatar ? 
                                Url::to('@web/uploads/chat_avatars/' . $chat->avatar) : 
                                Url::to('@web/images/default-chat.png'),
                            ['class' => 'img-circle', 'style' => 'width: 100px']
                        ) ?>
                        <h3><?= Html::encode($chat->name) ?></h3>
                        <p><?= Html::encode($chat->description) ?></p>
                        
                        <?php if ($chatUser->role == ChatUser::ROLE_ADMIN): ?>
                            <?= Html::a(
                                'Управление чатом', 
                                ['manage/users', 'chatId' => $chat->id], 
                                ['class' => 'btn btn-primary btn-sm']
                            ) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Сообщения</h3>
                </div>
                <div class="panel-body chat-messages" id="chat-messages">
                    <!-- Сообщения будут загружаться через AJAX -->
                    <div class="text-center" id="loading-messages">
                        <i class="fa fa-spinner fa-spin"></i> Загрузка сообщений...
                    </div>
                </div>
                
                <div class="panel-footer">
                    <?php if (!$chatUser->is_banned && (!$chatUser->muted_until || $chatUser->muted_until < time())): ?>
                        <?php $form = ActiveForm::begin(['id' => 'message-form']); ?>
                            <div class="flex">
                                <input type="text" 
                                       id="message-input" 
                                       class="form-control" 
                                       placeholder="Напишите сообщение...">
                                <span class="input-group-btn" style="margin-left: 12px;">
                                    <button class="btn btn-primary" type="button" id="message-send">Отправить</button>
                                </span>
                            </div>
                        <?php ActiveForm::end(); ?>
                    <?php elseif ($chatUser->muted_until && $chatUser->muted_until > time()): ?>
                        <div class="alert alert-warning">
                            Вы заглушены до <?= Yii::$app->formatter->asDatetime($chatUser->muted_until) ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            Вы заблокированы в этом чате
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$api_message_url = Url::to(['/messenger/api/messages', 'chatId' => $chat->id]);
$api_message_send = Url::to(['/messenger/message/send', 'chatId' => $chat->id]);

$js = <<<JS
// Загрузка сообщений
function loadMessages() {
    $.get('$api_message_url', function(data) {
        $('#loading-messages').hide();
        $('#chat-messages').empty();
        
        $.each(data, function(index, message) {
            let messageClass = message.is_own ? 'message-out' : 'message-in';
            const dateStr = new Date(message.created_at.replace(" ", "T")).toLocaleString();
            var html = `
                <div class="message-item fade-in \${messageClass}">
                    
                    <div class="message-bubble">
                        <div class="message-header">\${message.user.name}</div>
                        \${message.text}
                    </div>
                    <div class="message-info">\${dateStr}</div>
                </div>
            `;
            $('#chat-messages').append(html);
        });
        
        // Прокрутка вниз
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    });
}
        
$("#message-send").on('click', function () {
    console.log('asd');
    var message = $('#message-input').val();
    
    if (message.trim() === '') return;
    
    $.post('$api_message_send', {message: message}, function(response) {
        if (response.success) {
            $('#message-input').val('');
            loadMessages();
        } else {
            alert(response.error);
        }
    });
    
    return false;
});

// Первоначальная загрузка сообщений
loadMessages();

// Обновление сообщений каждые 5 секунд
setInterval(loadMessages, 5000);
JS;

$this->registerJs($js);
?>