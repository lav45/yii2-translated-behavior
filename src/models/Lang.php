<?php

namespace lav45\translate\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use lav45\translate\LocaleHelperTrait;

/**
 * This is the model class for table "lang".
 *
 * @property string $id
 * @property string $locale
 * @property string $name
 * @property integer $status
 *
 * @property array $statusList
 * @property string $statusName
 */
class Lang extends ActiveRecord
{
    use LocaleHelperTrait;

    const STATUS_DISABLE = 1;

    const STATUS_ACTIVE = 10;

    const PATTERN = '[a-z]{2}';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lang}}';
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

            [['locale'], 'trim'],
            [['locale'], 'required'],
            [['locale'], 'string', 'max' => 8],

            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => array_keys($this->getStatusList())],

            [['id', 'status', 'locale'], function($attribute) {
                if ($this->isAttributeChanged($attribute, false) && $this->isSourceLanguage()) {
                    $this->addError($attribute, Yii::t('app', 'This field is not editable.'));
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
            'id' => Yii::t('app', 'ID'),
            'locale' => Yii::t('app', 'Locale'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function isSourceLanguage()
    {
        return $this->getOldAttribute('id') == $this->getPrimaryLanguage(Yii::$app->sourceLanguage);
    }

    /**
     * @return string[]
     */
    public function getStatusList()
    {
        return [
            static::STATUS_ACTIVE => Yii::t('app', 'Active'),
            static::STATUS_DISABLE => Yii::t('app', 'Disable'),
        ];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return ArrayHelper::getValue($this->getStatusList(), $this->status);
    }

    /**
     * @param bool $active default false so it is most often used in backend
     * @return array
     */
    public static function getList($active = false)
    {
        $query = static::find()
            ->select(['name', 'id'])
            ->orderBy('id')
            ->indexBy('id');

        if ($active === true) {
            $query->active();
        }

        return $query->column();
    }

    /**
     * @param bool $active default true so it is most often used in frontend
     * @return array
     */
    public static function getLocaleList($active = true)
    {
        $query = static::find()
            ->select(['locale', 'id'])
            ->indexBy('id');

        if ($active === true) {
            $query->active();
        }

        return $query->column();
    }

    /**
     * @inheritdoc
     * @return LangQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LangQuery(get_called_class());
    }
}
