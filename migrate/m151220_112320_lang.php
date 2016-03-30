<?php

use yii\db\Migration;

class m151220_112320_lang extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%lang}}', [
            'id' => $this->string(2)->notNull(),
            'locale' => $this->string(8)->notNull(),
            'name' => $this->string(32)->notNull(),
            'status' => $this->smallInteger(),
            'PRIMARY KEY (id)',
        ], $tableOptions);

        $this->createIndex('lang_name_idx', 'lang', 'name', true);
        $this->createIndex('lang_status_idx', 'lang', 'status');

        $this->insert('lang', [
            'id' => 'en',
            'locale' => 'en-US',
            'name' => 'ENG',
            'status' => 10,
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('lang');
    }
}
