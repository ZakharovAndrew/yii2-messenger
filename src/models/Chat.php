<?php

namespace ZakharovAndrew\messenger\models;
use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $avatar
 * @property int $access_type
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * 
 * @property ChatUser[] $chatUsers
 * @property ChatMessage[] $messages
 */
class Chat extends ActiveRecord
{
    const ACCESS_TYPE_PUBLIC = 1; // Доступ по ссылке
    const ACCESS_TYPE_INVITE = 2; // Доступ по приглашению
    const ACCESS_TYPE_PRIVATE = 3; // Только ручное добавление

    /**
     * @var UploadedFile
     */
    public $avatarFile;

    public static function tableName()
    {
        return '{{%chat}}';
    }

    public function rules()
    {
        return [
            [['name', 'access_type'], 'required'],
            [['description'], 'string'],
            [['access_type'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['avatar'], 'string', 'max' => 255],
            [['avatarFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            [['access_type'], 'in', 'range' => array_keys(self::getAccessTypes())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Описание',
            'avatar' => 'Аватар',
            'access_type' => 'Тип доступа',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
            'created_by' => 'Создатель',
        ];
    }

    public static function getAccessTypes()
    {
        return [
            self::ACCESS_TYPE_PUBLIC => 'По ссылке',
            self::ACCESS_TYPE_INVITE => 'По приглашению',
            self::ACCESS_TYPE_PRIVATE => 'Только ручное добавление',
        ];
    }

    public function uploadAvatar()
    {
        if ($this->avatarFile) {
            $filename = Yii::$app->security->generateRandomString() . '.' . $this->avatarFile->extension;
            $path = Yii::getAlias('@webroot/uploads/chat_avatars/' . $filename);
            if ($this->avatarFile->saveAs($path)) {
                // Удаляем старый аватар, если он есть
                if ($this->avatar) {
                    $oldPath = Yii::getAlias('@webroot/uploads/chat_avatars/' . $this->avatar);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $this->avatar = $filename;
                return true;
            }
        }
        return false;
    }

    public function getChatUsers()
    {
        return $this->hasMany(ChatUser::class, ['chat_id' => 'id']);
    }

    public function getMessages()
    {
        return $this->hasMany(ChatMessage::class, ['chat_id' => 'id']);
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // Удаляем аватар при удалении чата
        if ($this->avatar) {
            $path = Yii::getAlias('@webroot/uploads/chat_avatars/' . $this->avatar);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        return true;
    }
}