<?php

namespace ZakharovAndrew\messenger\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $chat_user_id
 * @property string $permission
 * 
 * @property ChatUser $chatUser
 */
class ChatAdminPermission extends ActiveRecord
{
    const PERMISSION_DELETE_MESSAGES = 'delete_messages';
    const PERMISSION_BAN_USERS = 'ban_users';
    const PERMISSION_MUTE_USERS = 'mute_users';
    const PERMISSION_ADD_USERS = 'add_users';
    const PERMISSION_REMOVE_USERS = 'remove_users';
    const PERMISSION_EDIT_CHAT_INFO = 'edit_chat_info';
    const PERMISSION_MANAGE_ADMINS = 'manage_admins';

    public static function tableName()
    {
        return '{{%chat_admin_permission}}';
    }

    public function rules()
    {
        return [
            [['chat_user_id', 'permission'], 'required'],
            [['chat_user_id'], 'integer'],
            [['permission'], 'string', 'max' => 50],
            [['permission'], 'in', 'range' => array_keys(self::getPermissionList())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_user_id' => 'Администратор чата',
            'permission' => 'Право',
        ];
    }

    public static function getPermissionList()
    {
        return [
            self::PERMISSION_DELETE_MESSAGES => 'Удаление сообщений',
            self::PERMISSION_BAN_USERS => 'Блокировка пользователей',
            self::PERMISSION_MUTE_USERS => 'Заглушение пользователей',
            self::PERMISSION_ADD_USERS => 'Добавление пользователей',
            self::PERMISSION_REMOVE_USERS => 'Удаление пользователей',
            self::PERMISSION_EDIT_CHAT_INFO => 'Изменение информации чата',
            self::PERMISSION_MANAGE_ADMINS => 'Управление администраторами',
        ];
    }

    public function getChatUser()
    {
        return $this->hasOne(ChatUser::class, ['id' => 'chat_user_id']);
    }
}