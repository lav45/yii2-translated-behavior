<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class TranslatedBehavior
 * @package lav45\translate\TranslatedBehavior
 *
 * ================ Example use ================
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => TranslatedBehavior::className(),
 *             'translateRelation' => 'postLangs',
 *             'translateAttributes' => [
 *                 'titleLang' => 'title',
 *                 'description',
 *             ]
 *         ]
 *     ];
 * }
 *
 * @property ActiveRecord[] $currentTranslate
 * @property array $hasTranslate
 * @property ActiveRecord $translation
 * @property ActiveRecord $owner
 */
class TranslatedBehavior extends BaseTranslatedBehavior
{
    /**
     * @var string the translations relation name
     */
    public $translateRelation;
    /**
     * @var string the translations model language attribute name
     */
    public $languageAttribute = 'lang_id';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'eventBeforeDelete',
            ActiveRecord::EVENT_AFTER_INSERT => 'eventAfterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'eventAfterSave',
        ];
    }

    public function eventAfterSave()
    {
        $this->owner->link('currentTranslate', $this->getTranslation());
    }

    public function eventBeforeDelete()
    {
        $this->owner->unlinkAll($this->translateRelation, true);
    }

    /**
     * Returns the translation model for the specified language.
     * @param string|null $language
     * @return ActiveRecord
     */
    public function getTranslation($language = null)
    {
        $language = $language ?: $this->language;

        $translations = $this->getTranslateRelations();
        if (isset($translations[$language])) {
            return $translations[$language];
        }

        $attributes = isset($translations[$this->sourceLanguage]) ?
            ArrayHelper::toArray($translations[$this->sourceLanguage]) : [];

        $translations[$language] = $this->createTranslation($language, $attributes);

        $this->setTranslateRelations($translations);

        return $translations[$language];
    }

    /**
     * @return ActiveRecord[]
     */
    protected function getTranslateRelations()
    {
        $records = $this->owner->getRelatedRecords();
        if (!isset($records['currentTranslate']) && isset($records[$this->translateRelation])) {
            $translations = ArrayHelper::index($this->owner->{$this->translateRelation}, $this->languageAttribute);
            $this->setTranslateRelations($translations);
        }
        return $this->owner['currentTranslate'];
    }

    /**
     * @param ActiveRecord[] $models
     */
    protected function setTranslateRelations($models)
    {
        $this->owner->populateRelation('currentTranslate', $models);
    }

    /**
     * @param string $language
     * @param array $attributes
     * @return ActiveRecord
     */
    protected function createTranslation($language, $attributes = [])
    {
        $class = $this->getRelation()->modelClass;
        /** @var ActiveRecord $model */
        $model = new $class();
        $attributes[$this->languageAttribute] = $language;
        $model->setAttributes($attributes, false);
        return $model;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    protected function getRelation()
    {
        return $this->owner->getRelation($this->translateRelation);
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $this->isAttribute($name) ||
        parent::canGetProperty($name, $checkVars) ||
        (is_object($this->getTranslation()) && $this->getTranslation()->canGetProperty($name, $checkVars));
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $this->isAttribute($name) ||
        parent::canSetProperty($name, $checkVars) ||
        (is_object($this->getTranslation()) && $this->getTranslation()->canSetProperty($name, $checkVars));
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } else {
            $name = $this->getTranslateAttributeName($name) ?: $name;
            return $this->getTranslation()[$name];
        }
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } else {
            $name = $this->getTranslateAttributeName($name) ?: $name;
            $this->getTranslation()[$name] = $value;
        }
    }

    /**
     * Returns a value indicating whether a method is defined.
     *
     * The default implementation is a call to php function `method_exists()`.
     * You may override this method when you implemented the php magic method `__call()`.
     * @param string $name the method name
     * @return boolean whether the method is defined
     */
    public function hasMethod($name)
    {
        return parent::hasMethod($name) || 
        is_object($this->getTranslation()) && $this->getTranslation()->hasMethod($name);
    }

    /**
     * Calls the named method which is not a class method.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when an unknown method is being invoked.
     * @param string $name the method name
     * @param array $params method parameters
     * @return mixed the method return value
     */
    public function __call($name, $params)
    {
        return call_user_func_array([$this->getTranslation(), $name], $params);
    }

    /**
     * @return bool
     */
    public function isTranslated()
    {
        return $this->isSourceLanguage() === false && $this->getTranslation()->getIsNewRecord() === false;
    }

    /**
     * This read only relations designed for method $this->hasTranslate()
     * @return \yii\db\ActiveQuery
     */
    public function getHasTranslate()
    {
        $relations = $this->getRelation();
        $select = array_keys($relations->link);
        $select[] = $this->languageAttribute;

        return $relations
            ->select($select)
            ->indexBy($this->languageAttribute)
            ->asArray();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrentTranslate()
    {
        $langList = [$this->language, $this->sourceLanguage];
        $langList = array_keys(array_flip($langList));
        /** @var ActiveRecord $class */
        $class = $this->getRelation()->modelClass;
        $table = $class::tableName();

        return $this->getRelation()
            ->where([$table . '.' . $this->languageAttribute => $langList])
            ->indexBy($this->languageAttribute);
    }
}
