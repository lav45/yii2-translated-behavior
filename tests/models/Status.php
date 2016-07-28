<?php
/**
 * Created by PhpStorm.
 * User: lav45
 * Date: 28.07.16
 * Time: 2:19
 */

namespace tests\models;

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
                'class' => TranslatedBehavior::className(),
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
        return $this->hasMany(StatusLang::className(), ['status_id' => 'id']);
    }
}