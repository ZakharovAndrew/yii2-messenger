<?php

use yii\db\Migration;

class m240101_000200_create_chat_admin_permission_table extends Migration
{
    public function safeUp()
    {
        // Таблица прав администраторов
        $this->createTable('{{%chat_admin_permission}}', [
            'id' => $this->primaryKey(),
            'chat_user_id' => $this->integer()->notNull(),
            'permission' => $this->string(50)->notNull(),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
        ]);
        
        $this->addForeignKey(
            'fk-chat_admin_permission-chat_user_id',
            '{{%chat_admin_permission}}',
            'chat_user_id',
            '{{%chat_user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%chat_admin_permission}}');
    }
}