<?php

namespace ZakharovAndrew\messenger\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use ZakharovAndrew\messenger\models\Chat;
use ZakharovAndrew\messenger\models\ChatUser;
use ZakharovAndrew\messenger\services\MessageService;

class ApiController extends Controller
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
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionChats()
    {
        $userChats = ChatUser::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with('chat')
            ->orderBy(['joined_at' => SORT_DESC])
            ->all();

        $result = [];
        foreach ($userChats as $userChat) {
            $result[] = [
                'id' => $userChat->chat->id,
                'name' => $userChat->chat->name,
                'avatar' => $userChat->chat->avatar ? Yii::getAlias('@web/uploads/chat_avatars/' . $userChat->chat->avatar) : null,
                'unread_count' => 0, // Можно добавить логику подсчета непрочитанных
                'last_message' => null, // Можно добавить последнее сообщение
                'is_admin' => $userChat->role == ChatUser::ROLE_ADMIN,
            ];
        }

        return $result;
    }

    public function actionChatInfo($id)
    {
        $chat = Chat::findOne($id);
        if (!$chat) {
            throw new \yii\web\NotFoundHttpException('Чат не найден');
        }

        // Проверяем доступ к чату
        $chatUser = ChatUser::findOne([
            'chat_id' => $id,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$chatUser || $chatUser->is_banned) {
            throw new \yii\web\ForbiddenHttpException('У вас нет доступа к этому чату');
        }

        $users = ChatUser::find()
            ->where(['chat_id' => $id])
            ->with('user')
            ->orderBy(['role' => SORT_DESC, 'joined_at' => SORT_ASC])
            ->all();

        $members = [];
        foreach ($users as $user) {
            $members[] = [
                'id' => $user->user->id,
                'name' => $user->user->username,
                'avatar' => null, // Можно добавить аватар пользователя
                'role' => $user->role,
                'is_banned' => $user->is_banned,
                'muted_until' => $user->muted_until,
                'is_you' => $user->user_id == Yii::$app->user->id,
            ];
        }

        return [
            'id' => $chat->id,
            'name' => $chat->name,
            'description' => $chat->description,
            'avatar' => $chat->avatar ? Yii::getAlias('@web/uploads/chat_avatars/' . $chat->avatar) : null,
            'access_type' => $chat->access_type,
            'created_at' => $chat->created_at,
            'is_admin' => $chatUser->role == ChatUser::ROLE_ADMIN,
            'members' => $members,
        ];
    }

    public function actionMessages($chatId)
    {
        $messageService = new MessageService();
        $messages = $messageService->getChatMessages($chatId);

        $result = [];
        foreach ($messages as $message) {
            $result[] = [
                'id' => $message->id,
                'text' => $message->message,
                'created_at' => $message->created_at,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->username,
                ],
                'is_own' => $message->user_id == Yii::$app->user->id,
            ];
        }

        return $result;
    }
    
    public function actionPrivateChats()
    {
        $privateChats = Chat::find()
            ->where(['is_private' => true])
            ->andWhere([
                'or',
                ['private_user1_id' => Yii::$app->user->id],
                ['private_user2_id' => Yii::$app->user->id]
            ])
            ->all();

        $result = [];
        foreach ($privateChats as $chat) {
            $companionId = $chat->getPrivateChatCompanion(Yii::$app->user->id);
            $companion = User::findOne($companionId);

            $result[] = [
                'id' => $chat->id,
                'companion' => [
                    'id' => $companion->id,
                    'name' => $companion->username,
                    'avatar' => null, // URL аватара
                ],
                'last_message' => null, // Можно добавить логику
                'unread_count' => 0, // Можно добавить логику
            ];
        }

        return $result;
    }

    public function actionCreatePrivate($userId)
    {
        $companion = User::findOne($userId);
        if (!$companion) {
            return ['success' => false, 'error' => 'Пользователь не найден'];
        }

        if ($companion->id == Yii::$app->user->id) {
            return ['success' => false, 'error' => 'Нельзя создать чат с самим собой'];
        }

        $chat = Chat::findOrCreatePrivateChat(Yii::$app->user->id, $companion->id);

        return ['success' => true, 'chat_id' => $chat->id];
    }
}