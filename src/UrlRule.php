<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate;

use Yii;

class UrlRule extends \yii\web\UrlRule
{
    public $languageParam = '_lang';

    public function createUrl($manager, $route, $params)
    {
        if (!isset($params[$this->languageParam]) && strpos($this->name, "<{$this->languageParam}:") !== false) {
            $params[$this->languageParam] = substr(Yii::$app->language, 0, 2);
        }
        return parent::createUrl($manager, $route, $params);
    }
}