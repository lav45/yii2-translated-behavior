<?php

namespace tests\models;

use yii\db\ActiveRecord;
use lav45\translate\TranslatedTrait;
use lav45\translate\TranslatedBehavior;

/**
 * Class Post
 *
 * @property integer $id
 *
 * @property PostLang[] $postLangs
 *
 * @property string $title
 * @property string $titleLang
 * @property string $description
 */
class Post extends ActiveRecord
{
    use TranslatedTrait;

    public $title;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['title'] = 'titleLang';
        $fields['description'] = 'description';

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'trim'],
            [['title'], 'required'],
            [['title'], 'string', 'max' => 128],

            [['titleLang'], 'trim'],
            [['titleLang'], 'required'],
            [['titleLang'], 'string', 'max' => 128],

            [['description'], 'required'],
            [['description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TranslatedBehavior::className(),
                'translateRelation' => 'postLangs',
                'translateAttributes' => [
                    'titleLang' => 'title',
                    'description',
                ]
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostLangs()
    {
        return $this->hasMany(PostLang::className(), ['post_id' => 'id']);
    }
}
