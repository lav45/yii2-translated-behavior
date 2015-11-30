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
use Locale;

/**
 * Class BaseTranslatedBehavior
 * @package lav45\translate
 *
 * @property array $translateAttributes
 */
class BaseTranslatedBehavior extends Behavior
{
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
     * @var array
     */
    private $_attributes = [];

    /**
     * Initializes this behavior.
     */
    public function init()
    {
        parent::init();
        if ($this->language === null) {
            $this->language = Locale::getPrimaryLanguage(Yii::$app->language);
        }
        if ($this->sourceLanguage === null) {
            $this->sourceLanguage = Locale::getPrimaryLanguage(Yii::$app->sourceLanguage);
        }
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
    protected function getAttributeName($name)
    {
        return $this->_attributes[$name];
    }

    /**
     * @return bool
     */
    public function isSourceLanguage()
    {
        return $this->language === $this->sourceLanguage;
    }
}