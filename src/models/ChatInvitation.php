<?php

namespace ZakharovAndrew\messenger\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $chat_id
 * @property string $code
 * @property int $created_by
 * @property int $created_at
 * @property int|null $expires_at
 * @property int|null $max_uses
 * @property int $used_count
 * 
 * @property Chat $chat
 * @property User $createdBy
 */
class ChatInvitation extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%chat_invitation}}';
    }

    public function rules()
    {
        return [
            [['chat_id', 'code'], 'required'],
            [['chat_id', 'created_by', 'created_at', 'expires_at', 'max_uses', 'used_count'], 'integer'],
            [['code'], 'string', 'max' => 32],
            [['code'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Чат',
            'code' => 'Код приглашения',
            'created_by' => 'Создатель',
            'created_at' => 'Дата создания',
            'expires_at' => 'Истекает',
            'max_uses' => 'Макс. использований',
            'used_count' => 'Использовано раз',
        ];
    }

    public function getChat()
    {
        return $this->hasOne(Chat::class, ['id' => 'chat_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'created_by']);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < time();
    }

    public function isMaxUsesReached()
    {
        return $this->max_uses && $this->used_count >= $this->max_uses;
    }

    public function canBeUsed()
    {
        return !$this->isExpired() && !$this->isMaxUsesReached();
    }

    public function incrementUsedCount()
    {
        $this->used_count++;
        return $this->save(false, ['used_count']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->code = Yii::$app->security->generateRandomString(32);
        }
        return parent::beforeSave($insert);
    }
}