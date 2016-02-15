yii2-translated-behavior
===================

[![Latest Stable Version](https://poser.pugx.org/lav45/yii2-translated-behavior/v/stable)](https://packagist.org/packages/lav45/yii2-translated-behavior)
[![License](https://poser.pugx.org/lav45/yii2-translated-behavior/license)](https://packagist.org/packages/lav45/yii2-translated-behavior)
[![Total Downloads](https://poser.pugx.org/lav45/yii2-translated-behavior/downloads)](https://packagist.org/packages/lav45/yii2-translated-behavior)
[![Build Status](https://travis-ci.org/LAV45/yii2-translated-behavior.svg?branch=master)](https://travis-ci.org/lav45/yii2-translated-behavior)
[![Code Coverage](https://scrutinizer-ci.com/g/lav45/yii2-translated-behavior/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/lav45/yii2-translated-behavior/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lav45/yii2-translated-behavior/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lav45/yii2-translated-behavior/)

The Translated Behavior is a Yii2 extension for ActiveRecord models, that will help you add the possibility of transferring any entity.

## Installation

The preferred way to install this extension through [composer](http://getcomposer.org/download/).

You can set the console

```
$ php composer.phar require "lav45/yii2-translated-behavior:1.3.*"
```

or add

```
"lav45/yii2-translated-behavior": "1.3.*"
```

in ```require``` section in `composer.json` file.

## Settings

First you have to move all the attributes that are required for translation in a separate table. For example, imagine that we want to save the translation of the title and description of your post being. Your table schema should be brought to the following form:
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

After you change the table schema, we now need to determine the ratio of our `ActiveRecord` objects and adding behavior:

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
                'translateRelation' => 'postLangs', // Specify the name of the connection that will store transfers
//                'languageAttribute' => 'lang_id' // post_lang field from the table that will store the target language
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

Apply with the console command:
```
~$ yii migrate/up --migrationPath=vendor/lav45/yii2-translated-behavior/migrate
```

#### Lang ActiveRecord model cite completely

[\lav45\translate\models\Lang](src/models/Lang.php)

## Using

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
                    'currentTranslate', // loadabing data associated with the current translation
                    'hasTranslate' // need to display the status was translated page
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
                'class' => 'lav45\translate\ActionColumn',
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
As a result, after the creation of a new page will get a few buttons to edit the content in different languages

![Translate button](images/translate_button.png)


So you can get the current language of the model
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
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'ruleConfig' => ['class' => 'lav45\translate\UrlRule'], // This class will be used by default to create a URL
            'rules' => [
                [
                    'class' => 'yii\web\UrlRule', // If there is no need to substitute the language, you can use the base class
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
              // ContentNegotiator will be determined from a URL or browser language settings and install it in
              // Yii::$app->language, which uses the class TranslatedBehavior as language translation
                'class' => 'yii\filters\ContentNegotiator',
                'languages' => Lang::getLocaleList()
            ],
        ];
    }
```

or you can add for all controllers, for this you need to add in `frontend/config/bootstrap.php`

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

## License

**yii2-translated-behavior** it is available under a BSD 3-Clause License. Detailed information can be found in the `LICENSE.md`.
