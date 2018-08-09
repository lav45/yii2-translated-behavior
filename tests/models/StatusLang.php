<?php

namespace lav45\translate\test\models;

use yii\db\ActiveRecord;

/**
 * Class StatusLang
 * @package tests\models
 *
 * @property integer $status_id
 * @property string $lang_id
 * @property string $title
 */
class StatusLang extends ActiveRecord
{
    public static function tableName()
    {
        return 'status_lang';
    }
}