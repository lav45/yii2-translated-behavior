yii2-translated-behavior
===================

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![Latest Stable Version](https://poser.pugx.org/lav45/yii2-translated-behavior/v/stable)](https://packagist.org/packages/lav45/yii2-translated-behavior)
[![License](https://poser.pugx.org/lav45/yii2-translated-behavior/license)](https://packagist.org/packages/lav45/yii2-translated-behavior)
[![Total Downloads](https://poser.pugx.org/lav45/yii2-translated-behavior/downloads)](https://packagist.org/packages/lav45/yii2-translated-behavior)
[![Build Status](https://travis-ci.org/LAV45/yii2-translated-behavior.svg?branch=master)](https://travis-ci.org/LAV45/yii2-translated-behavior)
[![Test Coverage](https://codeclimate.com/github/LAV45/yii2-translated-behavior/badges/coverage.svg)](https://codeclimate.com/github/LAV45/yii2-translated-behavior/coverage)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lav45/yii2-translated-behavior/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lav45/yii2-translated-behavior/)
[![Code Climate](https://codeclimate.com/github/LAV45/yii2-translated-behavior/badges/gpa.svg)](https://codeclimate.com/github/LAV45/yii2-translated-behavior)

Translated Behavior это Yii2 расширение для ActiveRecord моделей, которое поможет вам добавить возможность перевода любой сущности.

Вы можете ознакомиться с [DEMO](https://yii2-translated-behavior.lav45.com) 

## Установка

Предпочтительный способ установить это расширение через [composer](http://getcomposer.org/download/).

Можно установить из консоли

```
$ composer require "lav45/yii2-translated-behavior"
```

или добавить

```
"lav45/yii2-translated-behavior": "1.4.*"
```

в ```require``` разделе в `composer.json` файл.

## Настройка

Сначала вы должны переместить все атрибуты, которые требуются для перевода в отдельной таблице. Например, представьте, что мы хотите сохранить перевод названия и описание от вашей post сущности. Ваши схемы таблиц следует привести к следующему виду:
```
    +--------------+        +--------------+       +-------------------+
    |     post     |        |     post     |       |     post_lang     |
    +--------------+        +--------------+       +-------------------+
    | id           |        | id           |       | post_id           |
    | title        |  --->  | created_at   |   +   | lang_id           |
    | description  |        | updated_at   |       | title             |
    | updated_at   |        +--------------+       | description       |
    | created_at   |                               +-------------------+
    +--------------+

```

После того, как вы изменили схему таблиц, теперь нам нужно определить отношение в наших `ActiveRecord` объектах и добавить поведение:

### Post
```php
use yii\db\ActiveRecord;
use lav45\translate\TranslatedTrait;
use lav45\translate\TranslatedBehavior;

/**
 * ...
 * @property string $title
 * @property string $description
 */
class Post extends ActiveRecord
{
    use TranslatedTrait;

    public function rules()
    {
        return [
            // ...

            [['title'], 'required'],
            [['title'], 'string', 'max' => 128],

            [['description'], 'required'],
            [['description'], 'string'],
        ];
    }
    
    public function behaviors()
    {
        return [
            [
                'class' => TranslatedBehavior::className(),
                'translateRelation' => 'postLangs', // Указываем имя связи в которой будут храниться переводы
//                'languageAttribute' => 'lang_id' // Поле из таблицы post_lang в котором будет храниться язык перевода
                'translateAttributes' => [
                    'title',
                    'description',
                ]
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            // ...
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    public function getPostLangs()
    {
        return $this->hasMany(PostLang::className(), ['post_id' => 'id']);
    }
}
```

### Language model

#### migrate

[migrate/m151220_112320_lang.php](migrate/m151220_112320_lang.php)

Применить можно с помощью команды в консоли:
```
~$ yii migrate/up --migrationPath=vendor/lav45/yii2-translated-behavior/migrate
```

#### Lang ActiveRecord модель привожу полностью

[\lav45\translate\models\Lang](src/models/Lang.php)

## Использование

### Backend

backend/config/bootstrap.php
```php
Yii::$container->set('lav45\translate\TranslatedBehavior', [
    'language' => isset($_GET['lang_id']) ? $_GET['lang_id'] : null
]);
```

backend/controllers/PostController.php
```php
namespace backend\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;

use common\models\Post;
use common\models\Lang;

class PostController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Post::find()
                ->with([
                    'currentTranslate', // загружаем связанные данные с текущим перевод
                    'hasTranslate' // нужна для отображения статуса была ли переведена страница
                ]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'langList' => Lang::getList(),
        ]);
    }
// ...
}
```

backend/view/post/index.php
```php
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'lav45\translate\grid\ActionColumn',
                'languages' => $langList,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}'
            ],
        ],
    ]);
    ?>
```
В результате после сознания новой страницы получим нескалько кнопок для редактирования контента на разный языках

![Translate button](images/translate_button.png)


Так можно получить текущий язык из модели
```php
/**
 * @var $this yii\web\View
 * @var $model common\models\Page
 */

$this->title = 'Create Post ( ' . $model->language . ' )';
```

### Frontend

frontend/config/main.php
```php
use lav45\translate\models\Lang;

return [
    'components' => [
        'urlManager' => [
            'class' => 'lav45\translate\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\web\UrlRule', // Если не нужно подставлять язык, можно использовать базовый класс
                    'pattern' => '',
                    'route' => 'post/index',
                ],
                [
                    'pattern' => '<_lang:' . Lang::PATTERN . '>/<id:\d+>',
                    'route' => 'post/view',
                ],
                [
                    'pattern' => '<_lang:' . Lang::PATTERN . '>',
                    'route' => 'post/index',
                ]
            ],
        ],
    ],
];
```

frontend/controllers/PostController.php
```php
namespace frontend\controllers;

use yii\web\Controller;
use common\models\Post;
use lav45\translate\models\Lang;

class PostController extends Controller
{
    public function behaviors()
    {
        return [
            [
              // ContentNegotiator будет отпределять из URL или настроек браузера язык и устанавливать его в
              // Yii::$app->language, каторый использует класс TranslatedBehavior как язык перевода
                'class' => 'yii\filters\ContentNegotiator',
                'languages' => Lang::getLocaleList()
            ],
        ];
    }
```

или можно добавить сразу для всех контроллеров, для этого нужна добавить в `frontend/config/bootstrap.php`

```php
\yii\base\Event::on('yii\base\Controller', 'beforeAction', function($event) {
    /** @var yii\filters\ContentNegotiator $negotiator */
    $negotiator = Yii::createObject([
        'class' => 'yii\filters\ContentNegotiator',
        'languages' => \common\models\Lang::getLocaleList(),
    ]);
    /** @var yii\base\ActionEvent $event */
    $negotiator->attach($event->action);
    $negotiator->negotiate();
});
```

## Лицензия

**yii2-translated-behavior** выпускается под BSD 3-Clause лицензией. Подробную информацию можно найти в `LICENSE.md`.
