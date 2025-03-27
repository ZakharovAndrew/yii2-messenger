<?php

use yii\db\Migration;

class m240101_000300_create_chat_message_table extends Migration
{
    public function safeUp()
    {
        // Таблица сообщений
        $this->createTable('{{%chat_message}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
            'updated_at' => $this->timestamp()->null(),
            'is_deleted' => $this->boolean()->defaultValue(false),
            'deleted_by' => $this->integer()->null(),
            'deleted_at' => $this->timestamp()->null(),
        ]);
        
        $this->addForeignKey(
            'fk-chat_message-chat_id',
            '{{%chat_message}}',
            'chat_id',
            '{{%chat}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%chat_message}}');
    }
}