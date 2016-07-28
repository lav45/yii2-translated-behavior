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

/**
 * Class BaseTranslatedBehavior
 * @package lav45\translate
 *
 * @property array $translateAttributes
 * @property string $language
 * @property string $sourceLanguage
 */
class BaseTranslatedBehavior extends Behavior
{
    use LocaleHelperTrait;
    /**
     * @var string the current translate language. If not set, it will use the value of
     * [[\yii\base\Application::language]].
     */
    private $_language;
    /**
     * @var string the language that the original messages are in. If not set, it will use the value of
     * [[\yii\base\Application::sourceLanguage]].
     */
    private $_sourceLanguage;
    /**
     * @var array
     */
    private $_attributes = [];

    /**
     * @return string
     */
    public function getLanguage()
    {
        if ($this->_language === null) {
            $this->setLanguage(Yii::$app->language);
        }
        return $this->_language;
    }

    /**
     * @param string $locale
     */
    public function setLanguage($locale)
    {
        $this->_language = $this->getPrimaryLanguage($locale);
    }

    /**
     * @return string
     */
    public function getSourceLanguage()
    {
        if ($this->_sourceLanguage === null) {
            $this->setSourceLanguage(Yii::$app->sourceLanguage);
        }
        return $this->_sourceLanguage;
    }

    /**
     * @param string $locale
     */
    public function setSourceLanguage($locale)
    {
        $this->_sourceLanguage = $this->getPrimaryLanguage($locale);
    }

    /**
     * @return array
     */
    public function getTranslateAttributes()
    {
        return $this->_attributes;
    }

    /**
     * @param array $attributes the list of translateAttributes to be translated
     */
    public function setTranslateAttributes($attributes)
    {
        $this->_attributes = [];
        foreach ((array) $attributes as $key => $value) {
            $key = is_int($key) ? $value : $key;
            $this->_attributes[$key] = $value;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function isAttribute($name)
    {
        return isset($this->_attributes[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getTranslateAttributeName($name)
    {
        return $this->isAttribute($name) ? $this->_attributes[$name] : null;
    }

    /**
     * @return bool
     */
    public function isSourceLanguage()
    {
        return $this->getLanguage() === $this->getSourceLanguage();
    }
}