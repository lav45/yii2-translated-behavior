<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate\web;

/**
 * Class UrlManager
 * @package lav45\translate\web
 */
class UrlManager extends \yii\web\UrlManager
{
    use LanguageUrlTrait;

    /**
     * @var array
     */
    public $ruleConfig = ['class' => 'lav45\translate\web\UrlRule'];

    /**
     * @inheritdoc
     */
    public function createUrl($params)
    {
        $params = $this->checkLanguageParams($params);
        return parent::createUrl($params);
    }
}