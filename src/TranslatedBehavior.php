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

/**
 * Class TranslatedBehavior
 * @package lav45\translate\TranslatedBehavior
 *
 * @property ActiveRecord[] $currentTranslate
 * @property array $hasTranslate
 * @property ActiveRecord $owner
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
    public $translateAttributes = [];
    /**
     * @var string the current translate language. If not set, it will use the value of
     * [[\yii\base\Application::language]].
     */
    public $language;
    /**
     * @var string the language that the original messages are in. If not set, it will use the value of
     * [[\yii\base\Application::sourceLanguage]].
     */
    public $sourceLanguage;

    /**
     * Initializes this behavior.
     */
    public function init()
    {
        parent::init();
        if ($this->language === null) {
            $this->language = substr(Yii::$app->language, 0, 2);
        }
        if ($this->sourceLanguage === null) {
            $this->sourceLanguage = substr(Yii::$app->sourceLanguage, 0, 2);
        }
        if (!empty($this->translateAttributes)) {
            $this->translateAttributes = array_flip((array)$this->translateAttributes);
        }
    }

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
            $this->owner->populateRelation('currentTranslate', $translations);
        }
        return $this->owner['currentTranslate'];
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

        $class = $this->getRelation()->modelClass;
        /** @var ActiveRecord $translation */
        $translation = new $class();
        if (isset($translations[$this->sourceLanguage])) {
            $attributes = $translations[$this->sourceLanguage] instanceof ActiveRecord ?
                $translations[$this->sourceLanguage]->attributes : $translations[$this->sourceLanguage];
            $translation->setAttributes((array) $attributes, false);
        }
        $translation->setAttribute($this->languageAttribute, $language);
        $translations[$language] = $translation;
        $this->owner->populateRelation('currentTranslate', $translations);

        return $translation;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function isAttribute($name)
    {
        return isset($this->translateAttributes[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getAttributeName($name)
    {
        return is_integer($this->translateAttributes[$name]) ? $name : $this->translateAttributes[$name];
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $this->isAttribute($name) ?: parent::canGetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $this->isAttribute($name) ?: parent::canSetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($this->isAttribute($name)) {
            $name = $this->getAttributeName($name);
            return $this->getTranslation()[$name];
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
            $name = $this->getAttributeName($name);
            $this->getTranslation()->$name = $value;
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
        $language = $language ?: $this->language;
        return isset($this->owner['hasTranslate'][$language]);
    }

    /**
     * @return bool
     */
    public function isSourceLanguage()
    {
        return $this->language === $this->sourceLanguage;
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
