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
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    public function afterSave()
    {
        $this->owner->link('currentTranslate', $this->getTranslation());
    }

    public function beforeDelete()
    {
        $this->owner->unlinkAll($this->translateRelation, true);
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
     * @return \yii\db\ActiveQuery
     */
    protected function getRelation()
    {
        return $this->owner->getRelation($this->translateRelation);
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
        $translations[$language] = $this->createTranslation($language, $translations);
        $this->setTranslateRelations($translations);
        return $translations[$language];
    }

    /**
     * @param string $language
     * @param ActiveRecord[]|array $translations
     * @return ActiveRecord
     */
    protected function createTranslation($language, $translations)
    {
        $class = $this->getRelation()->modelClass;
        /** @var ActiveRecord $model */
        $model = new $class();
        if (isset($translations[$this->sourceLanguage])) {
            $attributes = $translations[$this->sourceLanguage] instanceof ActiveRecord ?
                $translations[$this->sourceLanguage]->attributes : $translations[$this->sourceLanguage];
            $model->setAttributes((array) $attributes, false);
        }
        $model->setAttribute($this->languageAttribute, $language);
        return $model;
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $this->isAttribute($name) ||
        parent::canGetProperty($name, $checkVars) ||
        $this->getTranslation()->canGetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $this->isAttribute($name) ||
        parent::canSetProperty($name, $checkVars) ||
        $this->getTranslation()->canSetProperty($name, $checkVars);
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
            if ($this->isAttribute($name)) {
                $name = $this->normalizeAttributeName($name);
            }
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
            if ($this->isAttribute($name)) {
                $name = $this->normalizeAttributeName($name);
            }
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
        return parent::hasMethod($name) || $this->getTranslation()->hasMethod($name);
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

        return $this->getRelation()
            ->where([$this->languageAttribute => $langList])
            ->indexBy($this->languageAttribute);
    }
}
