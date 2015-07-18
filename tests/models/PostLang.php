<?php

namespace tests\models;

use yii\db\ActiveRecord;

/**
 * Class PostLang
 *
 * @property integer $post_id
 * @property integer $lang_id
 * @property string $title
 * @property string $description
 */
class PostLang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'lang_id'], 'required'],
            [['post_id', 'lang_id'], 'required'],

            [['title'], 'required'],
            [['title'], 'string', 'max' => 128],

            [['description'], 'required'],
            [['description'], 'string'],
        ];
    }
}