<?php

namespace lav45\translate\models;

use Yii;
use yii\db\ActiveRecord;
use Locale;

/**
 * This is the model class for table "lang".
 *
 * @property string $id
 * @property string $local
 * @property string $name
 * @property integer $status
 */
class Lang extends ActiveRecord
{
    const STATUS_DISABLE = 1;

    const STATUS_ACTIVE = 10;

    const PATTERN = '[a-z]{2}';

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
            [['id'], 'trim'],
            [['id'], 'required'],
            [['id'], 'string', 'min' => 2, 'max' => 2],
            [['id'], 'match', 'pattern' => '/^' . self::PATTERN . '$/'],
            [['id'], 'unique'],

            [['name'], 'trim'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 32],
            [['name'], 'unique'],

            [['local'], 'trim'],
            [['local'], 'required'],
            [['local'], 'string', 'max' => 8],

            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => array_keys($this->getStatusList())],

            [['id', 'status', 'local'], function($attribute) {
                if ($this->isAttributeChanged($attribute, false) && $this->isSourceLanguage()) {
                    $this->addError($attribute, 'This field is not editable.');
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'local' => 'Local',
            'name' => 'Name',
            'status' => 'Status',
        ];
    }

    public function isSourceLanguage()
    {
        return $this->getOldAttribute('id') == Locale::getPrimaryLanguage(Yii::$app->sourceLanguage);
    }

    /**
     * @return array
     */
    public function getStatusList()
    {
        return [
            static::STATUS_ACTIVE => 'Active',
            static::STATUS_DISABLE => 'Disable',
        ];
    }

    /**
     * @param bool $active
     * @return array
     */
    public static function getList($active = false)
    {
        $condition = $active ? ['status' => self::STATUS_ACTIVE] : [];

        return static::find()
            ->select(['name', 'id'])
            ->filterWhere($condition)
            ->orderBy('id')
            ->indexBy('id')
            ->column();
    }

    /**
     * @return array
     */
    public static function getLocaleList()
    {
        return static::find()
            ->select(['local', 'id'])
            ->where(['status' => self::STATUS_ACTIVE])
            ->indexBy('id')
            ->column();
    }
}
