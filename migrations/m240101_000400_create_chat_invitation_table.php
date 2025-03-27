<?php

use yii\db\Migration;

class m240101_000400_create_chat_invitation_table extends Migration
{
    public function safeUp()
    {
        // Таблица приглашений
        $this->createTable('{{%chat_invitation}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer()->notNull(),
            'code' => $this->string(32)->notNull(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
            'expires_at' => $this->timestamp()->null(),
            'max_uses' => $this->integer(),
            'used_count' => $this->integer()->defaultValue(0),
        ]);

        $this->addForeignKey(
            'fk-chat_invitation-chat_id',
            '{{%chat_invitation}}',
            'chat_id',
            '{{%chat}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%chat_invitation}}');
    }
}