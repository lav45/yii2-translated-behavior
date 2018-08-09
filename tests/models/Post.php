<?php

namespace lav45\translate\test\models;

use yii\db\ActiveRecord;
use lav45\translate\TranslatedTrait;
use lav45\translate\TranslatedBehavior;

/**
 * Class Post
 * @package tests\models
 *
 * @property integer $id
 * @property integer $status_id
 *
 * @property PostLang[] $postLangs
 * @property Status $status
 *
 * @property string $titleLang
 * @property string $description
 *
 * @mixin PostLang
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

    /**
     * @inheritdoc
     */
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
                '__class' => TranslatedBehavior::class,
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
        return $this->hasMany(PostLang::class, ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::class, ['id' => 'status_id']);
    }
}
