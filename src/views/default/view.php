<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = $chat->name;
$this->params['breadcrumbs'][] = ['label' => 'Мои чаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

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
                            <div class="input-group">
                                <input type="text" 
                                       id="message-input" 
                                       class="form-control" 
                                       placeholder="Напишите сообщение...">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="submit">Отправить</button>
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
$js = <<<JS
// Загрузка сообщений
function loadMessages() {
    $.get('/chat/api/messages?chatId=$chat->id', function(data) {
        $('#loading-messages').hide();
        $('#chat-messages').empty();
        
        $.each(data, function(index, message) {
            let messageClass = message.is_own ? 'own-message' : 'other-message';
            let dateStr = new Date(message.created_at * 1000).toLocaleString();
            var html = `
                <div class="message-item ${messageClass}">
                    <div class="message-header">
                        <strong>${message.user.name}</strong>
                        <small>${dateStr}</small>
                    </div>
                    <div class="message-body">${message.text}</div>
                </div>
            `;
            $('#chat-messages').append(html);
        });
        
        // Прокрутка вниз
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    });
}

// Отправка сообщения
$('#message-form').on('submit', function(e) {
    e.preventDefault();
    var message = $('#message-input').val();
    
    if (message.trim() === '') return;
    
    $.post('/chat/message/send?chatId=$chat->id', {message: message}, function(response) {
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