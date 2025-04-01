<?php

namespace ZakharovAndrew\messenger\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use ZakharovAndrew\messenger\models\Chat;
use ZakharovAndrew\messenger\models\ChatUser;
use ZakharovAndrew\messenger\Module;
use ZakharovAndrew\messenger\services\ChatService;

class DefaultController extends Controller
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

    public function actionIndex()
    {
        $userChats = ChatUser::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with('chat')
            ->orderBy(['joined_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'userChats' => $userChats,
        ]);
    }

    public function actionCreate()
    {
        $model = new Chat();
        $chatService = new ChatService();

        if ($model->load(Yii::$app->request->post())) {
            $chat = $chatService->createChat(
                $model->name,
                $model->description,
                $model->access_type,
                $model
            );

            if ($chat) {
                Yii::$app->session->setFlash('success', Module::t('Chat successfully created'));
                return $this->redirect(['view', 'id' => $chat->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        $chat = Chat::findOne($id);
        if (!$chat) {
            throw new \yii\web\NotFoundHttpException(Module::t('Chat not found'));
        }
        
        // Проверяем доступ пользователя к чату
        $chatUser = ChatUser::findOne([
            'chat_id' => $id,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$chatUser || $chatUser->is_banned) {
            throw new \yii\web\ForbiddenHttpException('У вас нет доступа к этому чату');
        }

        return $this->render('view', [
            'chat' => $chat,
            'chatUser' => $chatUser,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = Chat::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException(Module::t('Chat not found'));
        }

        // Проверяем права на редактирование чата
        $chatUser = ChatUser::findOne([
            'chat_id' => $id,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$chatUser || $chatUser->role !== ChatUser::ROLE_ADMIN || 
            !$chatUser->hasPermission(ChatAdminPermission::PERMISSION_EDIT_CHAT_INFO)) {
            throw new \yii\web\ForbiddenHttpException('У вас нет прав для редактирования этого чата');
        }

        $chatService = new ChatService();

        if ($model->load(Yii::$app->request->post())) {
            $chat = $chatService->updateChat(
                $model->id,
                $model->name,
                $model->description,
                $model->access_type,
                $model
            );

            if ($chat) {
                Yii::$app->session->setFlash('success', 'Чат успешно обновлен');
                return $this->redirect(['view', 'id' => $chat->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionJoin($id)
    {
        $chat = Chat::findOne($id);
        if (!$chat) {
            throw new \yii\web\NotFoundHttpException(Module::t('Chat not found'));
        }

        $chatService = new ChatService();
        $userId = Yii::$app->user->id;

        if ($chatService->canUserJoinChat($id, $userId)) {
            $chatUser = $chatService->addUserToChat($id, $userId);
            if ($chatUser) {
                Yii::$app->session->setFlash('success', 'Вы успешно присоединились к чату');
                return $this->redirect(['view', 'id' => $id]);
            }
        }

        Yii::$app->session->setFlash('error', 'Не удалось присоединиться к чату');
        return $this->redirect(['index']);
    }
}