<?php

namespace ZakharovAndrew\messenger\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property int $role
 * @property int $joined_at
 * @property int|null $muted_until
 * @property bool $is_banned
 * 
 * @property Chat $chat
 * @property User $user
 * @property ChatAdminPermission[] $permissions
 */
class ChatUser extends ActiveRecord
{
    const ROLE_MEMBER = 1;
    const ROLE_ADMIN = 2;

    public static function tableName()
    {
        return '{{%chat_user}}';
    }

    public function rules()
    {
        return [
            [['chat_id', 'user_id'], 'required'],
            [['chat_id', 'user_id', 'role', 'joined_at', 'muted_until'], 'integer'],
            [['is_banned'], 'boolean'],
            [['role'], 'in', 'range' => array_keys(self::getRoles())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Чат',
            'user_id' => 'Пользователь',
            'role' => 'Роль',
            'joined_at' => 'Дата вступления',
            'muted_until' => 'Заглушен до',
            'is_banned' => 'Заблокирован',
        ];
    }

    public static function getRoles()
    {
        return [
            self::ROLE_MEMBER => 'Участник',
            self::ROLE_ADMIN => 'Администратор',
        ];
    }

    public function getChat()
    {
        return $this->hasOne(Chat::class, ['id' => 'chat_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'user_id']);
    }

    public function getPermissions()
    {
        return $this->hasMany(ChatAdminPermission::class, ['chat_user_id' => 'id']);
    }

    public function hasPermission($permission)
    {
        if ($this->role === self::ROLE_ADMIN) {
            foreach ($this->permissions as $perm) {
                if ($perm->permission === $permission) {
                    return true;
                }
            }
        }
        return false;
    }

}