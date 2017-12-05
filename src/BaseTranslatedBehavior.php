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
    private $language;
    /**
     * @var string the language that the original messages are in. If not set, it will use the value of
     * [[\yii\base\Application::sourceLanguage]].
     */
    private $sourceLanguage;
    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @return string
     */
    public function getLanguage()
    {
        if (empty($this->language)) {
            $this->setLanguage(Yii::$app->language);
        }
        return $this->language;
    }

    /**
     * @param string $locale
     */
    public function setLanguage($locale)
    {
        $this->language = $this->getPrimaryLanguage($locale);
    }

    /**
     * @return string
     */
    public function getSourceLanguage()
    {
        if (empty($this->sourceLanguage)) {
            $this->setSourceLanguage(Yii::$app->sourceLanguage);
        }
        return $this->sourceLanguage;
    }

    /**
     * @param string $locale
     */
    public function setSourceLanguage($locale)
    {
        $this->sourceLanguage = $this->getPrimaryLanguage($locale);
    }

    /**
     * @return array
     */
    public function getTranslateAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes the list of translateAttributes to be translated
     */
    public function setTranslateAttributes(array $attributes)
    {
        $this->attributes = [];
        foreach ((array) $attributes as $key => $value) {
            $key = is_int($key) ? $value : $key;
            $this->attributes[$key] = $value;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function isAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getTranslateAttributeName($name)
    {
        return $this->isAttribute($name) ? $this->attributes[$name] : null;
    }

    /**
     * @return bool
     */
    public function isSourceLanguage()
    {
        return $this->getLanguage() === $this->getSourceLanguage();
    }
}