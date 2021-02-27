<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate\grid;

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class ActionColumn
 * @package lav45\translate\grid
 */
class ActionColumn extends \yii\grid\ActionColumn
{
    /** @var string */
    public $header = 'Translate';
    /** @var string */
    public $template = '';
    /** @var array */
    public $languages = [];
    /** @var string */
    public $languageAttribute = 'lang_id';
    /** @var bool */
    public $ajax = false;
    /** @var string */
    public $buttonTextTemplate = '<span class="glyphicon glyphicon-pencil"></span> {lang}';
    /** @var string */
    public $successTranslateButtonClass = 'btn-info';
    /** @var string */
    public $notTranslateButtonClass = 'btn-default';
    /** @var array */
    public $buttonOptions = [
        'class' => 'btn btn-xs',
    ];

    /**
     * @inheritdoc
     */
    protected function initDefaultButtons()
    {
        foreach ($this->languages as $lang_id => $lang) {
            $name = "update-{$lang_id}";
            $this->template .= ' {' . $name . '}';
            if (!isset($this->buttons[$name])) {
                $this->buttons[$name] = function($_url, $model, $key) use ($lang, $lang_id) {
                    /** @var \lav45\translate\TranslatedTrait $model */
                    $params = is_array($key) ? $key : ['id' => (string) $key];
                    $params[$this->languageAttribute] = $lang_id;
                    $params[0] = $this->controller ? $this->controller . '/update' : 'update';

                    $url = Url::toRoute($params);

                    $title = "Edit {$lang} version";
                    $options = array_merge([
                        'title' => $title,
                        'aria-label' => $title,
                        'data-pjax' => '0',
                    ], $this->buttonOptions);

                    $color = $model->hasTranslate($lang_id) ?
                        $this->successTranslateButtonClass :
                        $this->notTranslateButtonClass;
                    Html::addCssClass($options, $color);

                    $text = str_replace('{lang}', $lang, $this->buttonTextTemplate);
                    if ($this->ajax) {
                        $options['data-href'] = $url;
                        return Html::button($text, $options);
                    }
                    return Html::a($text, $url, $options);
                };
            }
        }
    }
} 