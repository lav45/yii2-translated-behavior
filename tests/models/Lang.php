<?php
/**
 * Created by PhpStorm.
 * User: lav45
 * Date: 17.07.15
 * Time: 2:55
 */

namespace tests\models;

use yii\db\ActiveRecord;

/**
 * Class Lang
 *
 * @property string $id
 */
class Lang extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'string', 'min' => 2, 'max' => 2],
        ];
    }
}