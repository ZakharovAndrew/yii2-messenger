<?php

namespace ZakharovAndrew\messenger\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use ZakharovAndrew\messenger\models\Chat;
use ZakharovAndrew\messenger\models\ChatMessage;
use ZakharovAndrew\messenger\models\ChatUser;
use ZakharovAndrew\messenger\services\MessageService;

class MessageController extends Controller
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

    public function actionSend($chatId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $message = Yii::$app->request->post('message');
        if (empty($message)) {
            return ['success' => false, 'error' => 'Сообщение не может быть пустым'];
        }

        // Проверяем доступ к чату
        $chatUser = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$chatUser || $chatUser->is_banned || 
            ($chatUser->muted_until && $chatUser->muted_until > time())) {
            return ['success' => false, 'error' => 'У вас нет прав отправлять сообщения в этот чат'];
        }

        $messageService = new MessageService();
        $message = $messageService->sendMessage($chatId, Yii::$app->user->id, $message);

        if ($message) {
            return [
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'text' => $message->message,
                    'created_at' => Yii::$app->formatter->asDatetime($message->created_at),
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->username,
                    ],
                ],
            ];
        }

        return ['success' => false, 'error' => 'Не удалось отправить сообщение'];
    }

    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $message = ChatMessage::findOne($id);
        if (!$message) {
            return ['success' => false, 'error' => 'Сообщение не найдено'];
        }

        // Проверяем права на удаление
        $chatUser = ChatUser::findOne([
            'chat_id' => $message->chat_id,
            'user_id' => Yii::$app->user->id,
        ]);

        $canDelete = false;

        // Пользователь может удалить свое сообщение
        if ($message->user_id == Yii::$app->user->id) {
            $canDelete = true;
        }
        // Или администратор с соответствующими правами
        elseif ($chatUser && $chatUser->role == ChatUser::ROLE_ADMIN && 
               $chatUser->hasPermission(ChatAdminPermission::PERMISSION_DELETE_MESSAGES)) {
            $canDelete = true;
        }

        if (!$canDelete) {
            return ['success' => false, 'error' => 'У вас нет прав удалять это сообщение'];
        }

        $messageService = new MessageService();
        if ($messageService->deleteMessage($id, Yii::$app->user->id)) {
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Не удалось удалить сообщение'];
    }

    public function actionLoad($chatId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Проверяем доступ к чату
        $chatUser = ChatUser::findOne([
            'chat_id' => $chatId,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$chatUser || $chatUser->is_banned) {
            return ['success' => false, 'error' => 'У вас нет доступа к этому чату'];
        }

        $offset = Yii::$app->request->get('offset', 0);
        $limit = Yii::$app->request->get('limit', 50);

        $messageService = new MessageService();
        $messages = $messageService->getChatMessages($chatId, $limit, $offset);

        $result = [];
        foreach ($messages as $message) {
            $result[] = [
                'id' => $message->id,
                'text' => $message->message,
                'created_at' => Yii::$app->formatter->asDatetime($message->created_at),
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->username,
                ],
                'is_own' => $message->user_id == Yii::$app->user->id,
            ];
        }

        return ['success' => true, 'messages' => $result];
    }
}