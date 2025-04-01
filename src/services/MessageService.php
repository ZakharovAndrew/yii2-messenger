<?php

namespace ZakharovAndrew\messenger\services;

use ZakharovAndrew\messenger\models\Chat;
use ZakharovAndrew\messenger\models\ChatMessage;
use Yii;

class MessageService
{
    public function sendMessage($chatId, $userId, $message)
    {
        $model = new ChatMessage();
        $model->chat_id = $chatId;
        $model->user_id = $userId;
        $model->message = $message;
        
        if ($model->save()) {
            return $model;
        }
        
        return null;
    }
    
    public function deleteMessage($messageId, $userId)
    {
        $message = ChatMessage::findOne($messageId);
        if (!$message) {
            return false;
        }
        
        return $message->softDelete($userId);
    }
    
    public function getChatMessages($chatId, $limit = 50, $offset = 0)
    {
        return ChatMessage::find()
            ->where(['chat_id' => $chatId, 'is_deleted' => false])
            ->orderBy(['created_at' => SORT_ASC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }
}