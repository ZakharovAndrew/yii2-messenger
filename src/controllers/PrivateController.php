<?php

namespace ZakharovAndrew\messenger\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use ZakharovAndrew\messenger\models\Chat;
use ZakharovAndrew\user\models\User;

class PrivateController extends Controller
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

    public function actionStart($userId)
    {
        $companion = User::findOne($userId);
        if (!$companion) {
            throw new \yii\web\NotFoundHttpException('Пользователь не найден');
        }
        
        $chat = Chat::findOrCreatePrivateChat(Yii::$app->user->id, $userId);
        
        return $this->redirect(['default/view', 'id' => $chat->id]);
    }

    public function actionList()
    {
        $privateChats = Chat::find()
            ->where(['is_private' => true])
            ->andWhere([
                'or',
                ['private_user1_id' => Yii::$app->user->id],
                ['private_user2_id' => Yii::$app->user->id]
            ])
            ->all();
            
        return $this->render('list', [
            'privateChats' => $privateChats,
        ]);
    }
}