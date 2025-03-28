<?php

namespace ZakharovAndrew\messenger\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property string $message
 * @property int $created_at
 * @property int $updated_at
 * @property bool $is_deleted
 * @property int|null $deleted_by
 * @property int|null $deleted_at
 * 
 * @property Chat $chat
 * @property User $user
 * @property User $deletedBy
 */
class ChatMessage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%chat_message}}';
    }

    public function rules()
    {
        return [
            [['chat_id', 'message'], 'required'],
            [['chat_id', 'user_id', 'deleted_by'], 'integer'],
            [['message'], 'string'],
            [['is_deleted'], 'boolean'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Чат',
            'user_id' => 'Автор',
            'message' => 'Сообщение',
            'created_at' => 'Дата отправки',
            'updated_at' => 'Дата обновления',
            'is_deleted' => 'Удалено',
            'deleted_by' => 'Кем удалено',
            'deleted_at' => 'Дата удаления',
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

    public function getDeletedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'deleted_by']);
    }

    public function softDelete($userId)
    {
        $this->is_deleted = true;
        $this->deleted_by = $userId;
        $this->deleted_at = time();
        return $this->save(false, ['is_deleted', 'deleted_by', 'deleted_at']);
    }
}