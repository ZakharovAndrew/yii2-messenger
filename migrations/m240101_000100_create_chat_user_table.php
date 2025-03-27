<?php

use yii\db\Migration;

class m240101_000100_create_chat_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%chat_user}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'role' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1 - участник, 2 - администратор'),
            'joined_at' => $this->integer()->notNull(),
            'muted_until' => $this->integer(),
            'is_banned' => $this->boolean()->defaultValue(false),
        ]);
        
        $this->addForeignKey(
            'fk-chat_user-chat_id',
            '{{%chat_user}}',
            'chat_id',
            '{{%chat}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%chat_user}}');
    }
}