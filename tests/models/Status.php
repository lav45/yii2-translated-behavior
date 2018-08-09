<?php

namespace lav45\translate\test\models;

use yii\db\ActiveRecord;
use lav45\translate\TranslatedBehavior;

/**
 * Class Status
 * @package tests\models
 *
 * @property integer $id
 *
 * @property StatusLang[] $statusLangs
 *
 * @property string $title
 */
class Status extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                '__class' => TranslatedBehavior::class,
                'translateRelation' => 'statusLangs',
                'translateAttributes' => [
                    'title',
                ]
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusLangs()
    {
        return $this->hasMany(StatusLang::class, ['status_id' => 'id']);
    }
}