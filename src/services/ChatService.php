<?php

namespace ZakharovAndrew\messenger\services;

use ZakharovAndrew\messenger\models\Chat;
use ZakharovAndrew\messenger\models\ChatUser;
use ZakharovAndrew\messenger\models\ChatAdminPermission;
use Yii;
use yii\web\UploadedFile;

class ChatService
{
    public function createChat($name, $description, $accessType, $avatarFile = null)
    {
        $chat = new Chat();
        $chat->name = $name;
        $chat->description = $description;
        $chat->access_type = $accessType;
        $chat->created_by = Yii::$app->user->id;
        
        if ($avatarFile) {
            $chat->avatarFile = UploadedFile::getInstance($avatarFile, 'avatarFile');
            if ($chat->avatarFile) {
                $chat->uploadAvatar();
            }
        }
        
        if ($chat->save()) {
            // Автоматически добавляем создателя в чат как администратора
            $this->addUserToChat($chat->id, Yii::$app->user->id, ChatUser::ROLE_ADMIN, [
                ChatAdminPermission::PERMISSION_DELETE_MESSAGES,
                ChatAdminPermission::PERMISSION_BAN_USERS,
                ChatAdminPermission::PERMISSION_MUTE_USERS,
                ChatAdminPermission::PERMISSION_ADD_USERS,
                ChatAdminPermission::PERMISSION_REMOVE_USERS,
                ChatAdminPermission::PERMISSION_EDIT_CHAT_INFO,
                ChatAdminPermission::PERMISSION_MANAGE_ADMINS,
            ]);
            
            return $chat;
        }
        
        return null;
    }
    
    public function addUserToChat($chatId, $userId, $role = ChatUser::ROLE_MEMBER, $permissions = [])
    {
        // Проверяем, не добавлен ли уже пользователь
        $existing = ChatUser::findOne(['chat_id' => $chatId, 'user_id' => $userId]);
        if ($existing) {
            return $existing;
        }
        
        $chatUser = new ChatUser();
        $chatUser->chat_id = $chatId;
        $chatUser->user_id = $userId;
        $chatUser->role = $role;
        
        if ($chatUser->save()) {
            // Добавляем права, если это администратор
            if ($role === ChatUser::ROLE_ADMIN && !empty($permissions)) {
                $this->addAdminPermissions($chatUser->id, $permissions);
            }
            
            return $chatUser;
        }
        
        return null;
    }
    
    public function addAdminPermissions($chatUserId, $permissions)
    {
        foreach ($permissions as $permission) {
            $model = new ChatAdminPermission();
            $model->chat_user_id = $chatUserId;
            $model->permission = $permission;
            $model->save();
        }
    }
    
    public function updateChat($chatId, $name, $description, $accessType, $avatarFile = null)
    {
        $chat = Chat::findOne($chatId);
        if (!$chat) {
            return null;
        }
        
        $chat->name = $name;
        $chat->description = $description;
        $chat->access_type = $accessType;
        
        if ($avatarFile) {
            $chat->avatarFile = UploadedFile::getInstance($avatarFile, 'avatarFile');
            if ($chat->avatarFile) {
                $chat->uploadAvatar();
            }
        }
        
        if ($chat->save()) {
            return $chat;
        }
        
        return null;
    }
    
    public function canUserJoinChat($chatId, $userId)
    {
        $chat = Chat::findOne($chatId);
        if (!$chat) {
            return false;
        }
        
        // Для личных чатов - только участники
        if ($chat->is_private) {
            return in_array($userId, [$chat->private_user1_id, $chat->private_user2_id]);
        }
        
        // Проверяем, не добавлен ли уже пользователь
        $existing = ChatUser::findOne(['chat_id' => $chatId, 'user_id' => $userId]);
        if ($existing) {
            return !$existing->is_banned;
        }
        
        // Проверяем тип доступа
        switch ($chat->access_type) {
            case Chat::ACCESS_TYPE_PUBLIC:
                return true;
            case Chat::ACCESS_TYPE_INVITE:
                // Здесь можно добавить проверку приглашения
                return false;
            case Chat::ACCESS_TYPE_PRIVATE:
                return false;
            default:
                return false;
        }
    }
}