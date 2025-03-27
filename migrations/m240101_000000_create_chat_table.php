<?php

use yii\db\Migration;

/**
 * Table for chats
 */
class m240101_000000_create_chat_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%chat}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'avatar' => $this->string(255),
            'access_type' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1 - по ссылке, 2 - по приглашению, 3 - только ручное добавление'),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
            'updated_at' => $this->timestamp()->null(),
            'created_by' => $this->integer()->notNull(),
        ]);
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%chat}}');
    }
}