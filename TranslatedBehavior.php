<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

/**
 * Class TranslatedBehavior
 * @package lav45\translate\TranslatedBehavior
 *
 * @property ActiveRecord|TranslatedTrait $owner
 */
class TranslatedBehavior extends Behavior
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
     * @var string[] the list of translateAttributes to be translated
     */
    private $_translate_attributes;
    /**
     * @var string current translate language
     */
    private $_translate;

    /**
     * @param string $language
     * @return ActiveRecord
     */
    public function setTranslate($language)
    {
        if (!empty($language)) {
            $this->_translate = $language;
        }
    }

    /**
     * @return string
     */
    public function getTranslate()
    {
        if ($this->_translate === null) {
            $this->_translate = substr(Yii::$app->language, 0, 2);
        }
        return $this->_translate;
    }

    /**
     * @return string
     */
    protected function getSourceLanguage()
    {
        return substr(Yii::$app->sourceLanguage, 0, 2);
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT => 'initEvent',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    public function initEvent()
    {
        $keys = array_keys($this->getTranslateAttributes());
        $this->owner->validators[] = Validator::createValidator('safe', $this->owner, $keys);
    }

    /**
     * @return ActiveRecord[]
     */
    protected function getTranslateRelations()
    {
        $records = $this->owner->getRelatedRecords();
        if (!isset($records['currentTranslate']) && isset($records[$this->translateRelation])) {
            $translations = ArrayHelper::index($this->owner->{$this->translateRelation}, $this->languageAttribute);
            $this->owner->populateRelation('currentTranslate', $translations);
        }
        return $this->owner->currentTranslate;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    protected function getRelation()
    {
        return $this->owner->getRelation($this->translateRelation);
    }

    /**
     * @return string|ActiveRecord
     */
    protected function getRelationClass()
    {
        return $this->getRelation()->modelClass;
    }

    /**
     * Returns the translation model for the specified language.
     * @param string|null $language
     * @return ActiveRecord
     */
    public function getTranslation($language = null)
    {
        if ($language === null) {
            $language = $this->getTranslate();
        }

        $translations = $this->getTranslateRelations();
        if (isset($translations[$language])) {
            return $translations[$language];
        }

        $class = $this->getRelationClass();
        /** @var ActiveRecord $translation */
        $translation = new $class();
        $sourceLanguage = $this->getSourceLanguage();
        if (isset($translations[$sourceLanguage])) {
            $translation->setAttributes($translations[$sourceLanguage]->attributes, false);
        }
        $translation->setAttribute($this->languageAttribute, $language);
        $translations[$language] = $translation;
        $this->owner->populateRelation('currentTranslate', $translations);

        return $translation;
    }

    public function afterValidate()
    {
        /** @var ActiveRecord $class */
        $class = $this->getRelationClass();
        $columns = array_keys($class::getTableSchema()->columns);
        $ignore_columns = array_keys($this->getRelation()->link);
        $attributes = array_diff($columns, $ignore_columns);

        foreach ($this->getTranslateRelations() as $item) {
            if ($item->validate($attributes) === false) {
                $this->owner->addErrors($item->getErrors());
            }
        }
    }

    public function afterSave()
    {
        foreach ($this->getTranslateRelations() as $translation) {
            $this->owner->link('currentTranslate', $translation);
        }
    }

    public function beforeDelete()
    {
        $this->owner->unlinkAll($this->translateRelation, true);
    }

    /**
     * @return string[]
     */
    public function getTranslateAttributes()
    {
        if ($this->_translate_attributes === null && $this->owner !== null) {
            /** @var ActiveRecord $class */
            $class = $this->getRelationClass();
            $columns = array_keys($class::getTableSchema()->columns);
            $primaryKey = $class::getTableSchema()->primaryKey;
            $attributes = array_diff($columns, $primaryKey);
            $this->setTranslateAttributes($attributes);
        }
        return $this->_translate_attributes;
    }

    /**
     * @param array|string $value
     */
    public function setTranslateAttributes($value)
    {
        $this->_translate_attributes = array_flip((array)$value);
    }

    protected function isAttribute($name)
    {
        return isset($this->getTranslateAttributes()[$name]);
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $this->isAttribute($name) ? : parent::canGetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $this->isAttribute($name) ? : parent::canSetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($this->isAttribute($name)) {
            return ArrayHelper::getValue($this->getTranslation(), $name);
        } else {
            return parent::__get($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if ($this->isAttribute($name)) {
            $this->getTranslation()->setAttribute($name, $value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @param null|string $language
     * @return bool
     */
    public function hasTranslate($language = null)
    {
        if ($language === null) {
            $language = $this->getTranslate();
        }
        return isset($this->owner->hasTranslate[$language]);
    }

    /**
     * @return bool
     */
    public function isSourceLanguage()
    {
        return $this->getTranslate() === $this->getSourceLanguage();
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
        $langList[$this->getTranslate()] = true;
        $langList[$this->getSourceLanguage()] = true;
        $langList = array_keys($langList);

        return $this->getRelation()
            ->where([$this->languageAttribute => $langList])
            ->indexBy($this->languageAttribute);
    }
}
