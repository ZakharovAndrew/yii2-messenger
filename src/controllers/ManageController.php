<?php

namespace ZakharovAndrew\messenger\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use ZakharovAndrew\messenger\models\Chat;
use ZakharovAndrew\messenger\models\ChatUser;
use ZakharovAndrew\messenger\models\ChatAdminPermission;
use ZakharovAndrew\messenger\services\ChatService;
use ZakharovAndrew\messenger\assets\ChatAsset;

class ManageController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionUsers($chatId)
    {
        $chat = Chat::findOne($chatId);
        if (!$chat) {
            throw new \yii\web\NotFoundHttpException('Чат не найден');
        }

        // Проверяем права на управление пользователями
        $currentUser = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$currentUser || $currentUser->role !== ChatUser::ROLE_ADMIN || 
            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_REMOVE_USERS)) {
            throw new \yii\web\ForbiddenHttpException('У вас нет прав управлять пользователями чата');
        }

        $users = ChatUser::find()
            ->where(['chat_id' => $chatId])
            ->with('user')
            ->orderBy(['role' => SORT_DESC, 'joined_at' => SORT_ASC])
            ->all();
        
        ChatAsset::register($this);

        return $this->render('users', [
            'chat' => $chat,
            'users' => $users,
            'currentUser' => $currentUser,
        ]);
    }

    public function actionRemoveUser($chatId, $userId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Проверяем права на удаление пользователей
        $currentUser = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$currentUser || $currentUser->role !== ChatUser::ROLE_ADMIN || 
            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_REMOVE_USERS)) {
            return ['success' => false, 'error' => 'У вас нет прав удалять пользователей'];
        }

        // Нельзя удалить себя
        if ($userId == Yii::$app->user->id) {
            return ['success' => false, 'error' => 'Вы не можете удалить себя из чата'];
        }

        $userToRemove = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);

        if (!$userToRemove) {
            return ['success' => false, 'error' => 'Пользователь не найден в чате'];
        }

        // Нельзя удалить администратора, если нет прав manage_admins
        if ($userToRemove->role == ChatUser::ROLE_ADMIN && 
            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_MANAGE_ADMINS)) {
            return ['success' => false, 'error' => 'У вас нет прав удалять администраторов'];
        }

        if ($userToRemove->delete()) {
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Не удалось удалить пользователя'];
    }

    public function actionBanUser($chatId, $userId, $ban = true)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Проверяем права на блокировку пользователей
        $currentUser = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$currentUser || $currentUser->role !== ChatUser::ROLE_ADMIN || 
            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_BAN_USERS)) {
            return ['success' => false, 'error' => 'У вас нет прав блокировать пользователей'];
        }

        // Нельзя заблокировать себя
        if ($userId == Yii::$app->user->id) {
            return ['success' => false, 'error' => 'Вы не можете заблокировать себя'];
        }

        $userToBan = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);

        if (!$userToBan) {
            return ['success' => false, 'error' => 'Пользователь не найден в чате'];
        }

        // Нельзя заблокировать администратора, если нет прав manage_admins
        if ($userToBan->role == ChatUser::ROLE_ADMIN && 
            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_MANAGE_ADMINS)) {
            return ['success' => false, 'error' => 'У вас нет прав блокировать администраторов'];
        }

        $userToBan->is_banned = $ban;
        if ($userToBan->save()) {
            return ['success' => true, 'is_banned' => $ban];
        }

        return ['success' => false, 'error' => 'Не удалось изменить статус блокировки'];
    }

    public function actionMuteUser($chatId, $userId, $minutes)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Проверяем права на заглушение пользователей
        $currentUser = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$currentUser || $currentUser->role !== ChatUser::ROLE_ADMIN || 
            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_MUTE_USERS)) {
            return ['success' => false, 'error' => 'У вас нет прав заглушать пользователей'];
        }

        // Нельзя заглушить себя
        if ($userId == Yii::$app->user->id) {
            return ['success' => false, 'error' => 'Вы не можете заглушить себя'];
        }

        $userToMute = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);

        if (!$userToMute) {
            return ['success' => false, 'error' => 'Пользователь не найден в чате'];
        }

        // Нельзя заглушить администратора, если нет прав manage_admins
        if ($userToMute->role == ChatUser::ROLE_ADMIN && 
            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_MANAGE_ADMINS)) {
            return ['success' => false, 'error' => 'У вас нет прав заглушать администраторов'];
        }

        $userToMute->muted_until = $minutes > 0 ? time() + ($minutes * 60) : null;
        if ($userToMute->save()) {
            return ['success' => true, 'muted_until' => $userToMute->muted_until];
        }

        return ['success' => false, 'error' => 'Не удалось изменить статус заглушения'];
    }

    public function actionSetAdmin($chatId, $userId, $isAdmin = true)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Проверяем права на управление администраторами
        $currentUser = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$currentUser || $currentUser->role !== ChatUser::ROLE_ADMIN || 
            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_MANAGE_ADMINS)) {
            return ['success' => false, 'error' => 'У вас нет прав назначать администраторов'];
        }

        $userToChange = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);

        if (!$userToChange) {
            return ['success' => false, 'error' => 'Пользователь не найден в чате'];
        }

        $userToChange->role = $isAdmin ? ChatUser::ROLE_ADMIN : ChatUser::ROLE_MEMBER;
        if ($userToChange->save()) {
            // Удаляем все права, если снимаем админку
            if (!$isAdmin) {
                ChatAdminPermission::deleteAll(['chat_user_id' => $userToChange->id]);
            }
            return ['success' => true, 'is_admin' => $isAdmin];
        }

        return ['success' => false, 'error' => 'Не удалось изменить роль пользователя'];
    }

    public function actionSetAdminPermissions($chatId, $userId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Проверяем права на управление администраторами
        $currentUser = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$currentUser || $currentUser->role !== ChatUser::ROLE_ADMIN || 
            !$currentUser->hasPermission(ChatAdminPermission::PERMISSION_MANAGE_ADMINS)) {
            return ['success' => false, 'error' => 'У вас нет прав изменять права администраторов'];
        }

        $userToChange = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);

        if (!$userToChange || $userToChange->role !== ChatUser::ROLE_ADMIN) {
            return ['success' => false, 'error' => 'Пользователь не является администратором'];
        }

        $permissions = Yii::$app->request->post('permissions', []);
        $availablePermissions = array_keys(ChatAdminPermission::getPermissionList());

        // Удаляем старые права
        ChatAdminPermission::deleteAll(['chat_user_id' => $userToChange->id]);

        // Добавляем новые
        foreach ($permissions as $permission) {
            if (in_array($permission, $availablePermissions)) {
                $model = new ChatAdminPermission();
                $model->chat_user_id = $userToChange->id;
                $model->permission = $permission;
                $model->save();
            }
        }

        return ['success' => true];
    }
}