<?php

namespace lav45\translate\test\models;

use yii\db\ActiveRecord;

/**
 * Class PostLang
 *
 * @property integer $post_id
 * @property string $lang_id
 * @property string $title
 * @property string $description
 *
 * @property string $modelData
 *
 * @mixin TestBehavior
 */
class PostLang extends ActiveRecord
{
    private $_model_data;

    public $modelTestProperty = 'OK';

    public function modelTestMethod()
    {
        return 'OK';
    }

    public function getModelData()
    {
        return $this->_model_data;
    }

    public function setModelData($value)
    {
        $this->_model_data = $value;
    }

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
            [['post_id'], 'required'],
            [['post_id'], 'integer'],

            [['lang_id'], 'required'],
            [['lang_id'], 'string'],

            [['title'], 'required'],
            [['title'], 'string', 'max' => 128],

            [['description'], 'required'],
            [['description'], 'string'],
        ];
    }

    public function behaviors()
    {
        return [
            TestBehavior::class
        ];
    }
}