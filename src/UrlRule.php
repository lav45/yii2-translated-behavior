<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate;

use Yii;

/**
 * Class UrlRule
 * @package lav45\translate
 */
class UrlRule extends \yii\web\UrlRule
{
    /**
     * @var string
     */
    public $languageParam = '_lang';
    /**
     * @var string
     */
    private $_language;

    /**
     * @return string
     */
    public function getLanguage()
    {
        if ($this->_language === null) {
            $this->_language = substr(Yii::$app->language, 0, 2);
        }
        return $this->_language;
    }

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        if (!isset($params[$this->languageParam])) {
            $params[$this->languageParam] = $this->getLanguage();
        }
        return parent::createUrl($manager, $route, $params);
    }
}