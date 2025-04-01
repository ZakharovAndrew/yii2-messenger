<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use ZakharovAndrew\messenger\models\ChatUser;
use ZakharovAndrew\messenger\models\ChatAdminPermission;
use ZakharovAndrew\messenger\assets\ChatAsset;

ChatAsset::register($this);

$this->title = 'Управление участниками: ' . $chat->name;
$this->params['breadcrumbs'][] = ['label' => 'Мои чаты', 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $chat->name, 'url' => ['default/view', 'id' => $chat->id]];
$this->params['breadcrumbs'][] = 'Управление участниками';
?>

<div class="chat-manage-users">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Участники чата</h3>
                </div>
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => new \yii\data\ArrayDataProvider([
                            'allModels' => $users,
                            'pagination' => false,
                        ]),
                        'columns' => [
                            [
                                'attribute' => 'user.username',
                                'label' => 'Пользователь',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::img(
                                        $model->user->avatar ? 
                                            Url::to('@web/uploads/user_avatars/' . $model->user->avatar) : 
                                            Url::to('@web/images/default-user.png'),
                                        ['class' => 'img-circle', 'style' => 'width: 30px']
                                    ) . ' ' . Html::encode($model->user->username);
                                },
                            ],
                            [
                                'attribute' => 'role',
                                'label' => 'Роль',
                                'value' => function($model) {
                                    return $model->getRoles()[$model->role];
                                },
                            ],
                            [
                                'attribute' => 'is_banned',
                                'label' => 'Статус',
                                'value' => function($model) {
                                    if ($model->is_banned) {
                                        return '<span class="label label-danger">Заблокирован</span>';
                                    } elseif ($model->muted_until && $model->muted_until > time()) {
                                        return '<span class="label label-warning">Заглушен</span>';
                                    } else {
                                        return '<span class="label label-success">Активен</span>';
                                    }
                                },
                                'format' => 'raw',
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{ban} {mute} {admin} {remove}',
                                'buttons' => [
                                    'ban' => function($url, $model) use ($chat, $currentUser) {
                                        if ($model->user_id == Yii::$app->user->id) return '';
                                        if ($model->role == ChatUser::ROLE_ADMIN && 
                                            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_MANAGE_ADMINS)) {
                                            return '';
                                        }
                                        
                                        return Html::a(
                                            $model->is_banned ? '<i class="fa fa-unlock"></i>' : '<i class="fa fa-ban"></i>',
                                            ['manage/ban-user', 'chatId' => $chat->id, 'userId' => $model->user_id, 'ban' => !$model->is_banned],
                                            [
                                                'title' => $model->is_banned ? 'Разблокировать' : 'Заблокировать',
                                                'class' => 'btn btn-xs btn-' . ($model->is_banned ? 'success' : 'danger'),
                                                'data' => [
                                                    'confirm' => $model->is_banned ? 
                                                        'Разблокировать пользователя?' : 
                                                        'Заблокировать пользователя?',
                                                    'method' => 'post',
                                                ],
                                            ]
                                        );
                                    },
                                    'mute' => function($url, $model) use ($chat, $currentUser) {
                                        if ($model->user_id == Yii::$app->user->id) return '';
                                        if ($model->role == ChatUser::ROLE_ADMIN && 
                                            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_MANAGE_ADMINS)) {
                                            return '';
                                        }
                                        
                                        return Html::a(
                                            '<i class="fa fa-volume-off"></i>',
                                            '#',
                                            [
                                                'title' => 'Заглушить',
                                                'class' => 'btn btn-xs btn-warning mute-btn',
                                                'data' => [
                                                    'user-id' => $model->user_id,
                                                    'chat-id' => $chat->id,
                                                    'is-muted' => $model->muted_until && $model->muted_until > time(),
                                                ],
                                            ]
                                        );
                                    },
                                    'admin' => function($url, $model) use ($chat, $currentUser) {
                                        if ($model->user_id == Yii::$app->user->id) return '';
                                        if (!$currentUser->hasPermission(ChatAdminPermission::PERMISSION_MANAGE_ADMINS)) {
                                            return '';
                                        }
                                        
                                        return Html::a(
                                            $model->role == ChatUser::ROLE_ADMIN ? '<i class="fa fa-user-times"></i>' : '<i class="fa fa-user-plus"></i>',
                                            ['manage/set-admin', 'chatId' => $chat->id, 'userId' => $model->user_id, 'isAdmin' => $model->role != ChatUser::ROLE_ADMIN],
                                            [
                                                'title' => $model->role == ChatUser::ROLE_ADMIN ? 'Убрать админа' : 'Сделать админом',
                                                'class' => 'btn btn-xs btn-' . ($model->role == ChatUser::ROLE_ADMIN ? 'danger' : 'primary'),
                                                'data' => [
                                                    'confirm' => $model->role == ChatUser::ROLE_ADMIN ? 
                                                        'Убрать права администратора?' : 
                                                        'Назначить администратором?',
                                                    'method' => 'post',
                                                ],
                                            ]
                                        );
                                    },
                                    'remove' => function($url, $model) use ($chat, $currentUser) {
                                        if ($model->user_id == Yii::$app->user->id) return '';
                                        if ($model->role == ChatUser::ROLE_ADMIN && 
                                            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_MANAGE_ADMINS)) {
                                            return '';
                                        }
                                        
                                        return Html::a(
                                            '<i class="fa fa-trash"></i>',
                                            ['manage/remove-user', 'chatId' => $chat->id, 'userId' => $model->user_id],
                                            [
                                                'title' => 'Удалить из чата',
                                                'class' => 'btn btn-xs btn-danger',
                                                'data' => [
                                                    'confirm' => 'Удалить пользователя из чата?',
                                                    'method' => 'post',
                                                ],
                                            ]
                                        );
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Обработка кнопки заглушения
$('.mute-btn').on('click', function(e) {
    e.preventDefault();
    
    var btn = $(this);
    var userId = btn.data('user-id');
    var chatId = btn.data('chat-id');
    var isMuted = btn.data('is-muted');
    
    if (isMuted) {
        // Снять заглушение
        $.post('/chat/manage/mute-user', {
            chatId: chatId,
            userId: userId,
            minutes: 0
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.error);
            }
        });
    } else {
        // Заглушить
        var minutes = prompt('На сколько минут заглушить пользователя?', '60');
        if (minutes !== null && !isNaN(minutes) && minutes > 0) {
            $.post('/chat/manage/mute-user', {
                chatId: chatId,
                userId: userId,
                minutes: minutes
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.error);
                }
            });
        }
    }
});
JS;

$this->registerJs($js);
?>