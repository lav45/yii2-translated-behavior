<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate;

use yii\helpers\Url;
use yii\helpers\Html;

/**
 * Class ActionColumn
 * @package lav45\translate
 */
class ActionColumn extends \yii\grid\ActionColumn
{
    public $header = 'Translate';

    public $template = '';

    public $buttons = [];

    public $languages = [];

    public $languageAttribute = 'lang_id';

    public $ajax = true;

    protected function initDefaultButtons()
    {
        foreach($this->languages as $lang_id => $lang) {
            $name = "update-$lang_id";
            $this->template .= ' {' . $name . '}';
            if (!isset($this->buttons[$name])) {
                $this->buttons[$name] = function () use ($lang, $lang_id) {
                    /** @var \yii\db\ActiveRecord|TranslatedTrait $model */
                    $model = func_get_arg(1);
                    $key = func_get_arg(2);

                    $params = is_array($key) ? $key : ['id' => (string) $key];
                    $params[$this->languageAttribute] = $lang_id;
                    $params[0] = $this->controller ? $this->controller . '/update' : 'update';

                    $url = Url::toRoute($params);

                    $color = $model->hasTranslate($lang_id) ? 'info' : 'default';

                    $options = [
                        'class' => "btn btn-xs btn-$color",
                        'title' => "Edit $lang version",
                        'data-pjax' => '0',
                    ];

                    if ($this->ajax) {
                        $options['data-href'] = $url;
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span> ' . $lang, $options);
                    } else {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . $lang, $url, $options);
                    }
                };
            }
        }
    }
} 