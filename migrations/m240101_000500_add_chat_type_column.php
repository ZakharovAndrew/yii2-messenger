<?php

use yii\db\Migration;

class m240101_000500_add_chat_type_column extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%chat}}', 'is_private', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%chat}}', 'private_user1_id', $this->integer());
        $this->addColumn('{{%chat}}', 'private_user2_id', $this->integer());
        
        // Индекс для поиска личных чатов
        $this->createIndex(
            'idx-chat-private_users',
            '{{%chat}}',
            ['private_user1_id', 'private_user2_id']
        );
    }

    public function safeDown()
    {
        $this->dropColumn('{{%chat}}', 'is_private');
        $this->dropColumn('{{%chat}}', 'private_user1_id');
        $this->dropColumn('{{%chat}}', 'private_user2_id');
    }
}