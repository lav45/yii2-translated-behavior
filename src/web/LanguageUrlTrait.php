<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate\web;

use Yii;
use Locale;

trait LanguageUrlTrait
{
    /**
     * @var string
     */
    public $languageParam = '_lang';
    /**
     * @var string set this language if exist in url params. If not set, it will use the value of
     * [[\yii\base\Application::language]].
     */
    private $_language;

    /**
     * @return string
     */
    protected function getLanguage()
    {
        if ($this->_language === null) {
            $this->_language = Locale::getPrimaryLanguage(Yii::$app->language);
        }
        return $this->_language;
    }

    /**
     * @param array|string $params
     * @return mixed
     */
    public function checkLanguageParams($params)
    {
        if (is_array($params) && empty($params[$this->languageParam])) {
            $params[$this->languageParam] = $this->getLanguage();
        }
        return $params;
    }
}