<?php

namespace tests;

use Yii;
use yii\db\ActiveQuery;

use tests\models\Post;

/**
 * Class TranslateTest
 * @package tests
 */
class TranslateTest extends DatabaseTestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        Yii::$app->language = Yii::$app->sourceLanguage;
        Yii::$container->set('lav45\translate\TranslatedBehavior', []);
    }

    public function testFindPosts()
    {
        Yii::$container->set('lav45\translate\TranslatedBehavior', [
            'language' => 'ru'
        ]);

        /** @var Post[] $posts */
        $posts = Post::find()
            ->with(['currentTranslate' => function (ActiveQuery $q) {
                $q->asArray();
            }])
            ->all();

        foreach ($posts as $key => $post) {
            $posts[$key] = $post->toArray();
        }

        $data = [
            0 => [
                'id' => 1,
                'title' => 'заголовок первой страницы',
                'description' => 'описание первого поста',
            ],
            1 => [
                'id' => 2,
                'title' => 'title of the second post',
                'description' => 'description of the second post',
            ],
        ];

        $this->assertEquals($data, $posts);
    }

    public function testCreatePost()
    {
        $model = new Post([
            'titleLang' => 'test for the create new post',
            'description' => 'description for the create new post',
        ]);

        $this->assertTrue($model->save(false));

        $dataSet = $this->getConnection()->createDataSet(['post', 'post_lang']);
        $expectedDataSet = $this->createFlatXMLDataSet(__DIR__ . '/data/testCreatePost.xml');
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testDeletePost()
    {
        /** @var Post $model */
        $model = Post::findOne(1);
        $model->delete();

        $dataSet = $this->getConnection()->createDataSet(['post', 'post_lang']);
        $expectedDataSet = $this->createFlatXMLDataSet(__DIR__ . '/data/testDeletePost.xml');
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testEditPost()
    {
        /** @var Post $model */
        $model = Post::findOne(1);
        $model->titleLang = 'new title';

        $this->assertTrue($model->save(false));

        $dataSet = $this->getConnection()->createDataSet(['post_lang']);
        $expectedDataSet = $this->createFlatXMLDataSet(__DIR__ . '/data/testEditPost.xml');
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testEditTranslate()
    {
        /** @var Post $model */
        $model = Post::findOne(1);
        $this->assertTrue($model->isSourceLanguage());
        $model->language = 'ru';
        $this->assertFalse($model->isSourceLanguage());
        $model->titleLang = 'new title';

        $this->assertTrue($model->save(false));

        $dataSet = $this->getConnection()->createDataSet(['post_lang']);
        $expectedDataSet = $this->createFlatXMLDataSet(__DIR__ . '/data/testEditTranslate.xml');
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testAddTranslate()
    {
        /** @var Post $model */
        $model = Post::findOne(2);
        $this->assertFalse($model->isTranslated());

        $this->assertTrue($model->getTranslation('ru')->save());
        $this->assertFalse($model->isTranslated());

        $model->language = 'ru';
        $this->assertTrue($model->isTranslated());
    }

    public function testHasTranslateRelations()
    {
        /** @var Post $model */
        $model = Post::findOne(1);
        $data = array_keys($model->hasTranslate);
        $expectedData = ['en', 'ru'];
        $this->assertEquals($data, $expectedData);

        $this->assertTrue($model->hasTranslate('en'));
        $this->assertFalse($model->hasTranslate('fr'));
    }

    public function testCurrentTranslateRelations()
    {
        Yii::$app->language = 'ru-RU';

        /** @var Post $model */
        $model = Post::findOne(1);
        $this->assertTrue(count($model->currentTranslate) == 2);

        $data = array_keys($model->currentTranslate);
        $expectedData = ['en', 'ru'];
        $this->assertEquals($data, $expectedData);
        $this->assertTrue($model->hasTranslate('ru'));

        Yii::$app->language = 'fr-FR';

        /** @var Post $model */
        $model = Post::findOne(1);
        $this->assertTrue(count($model->currentTranslate) == 1);

        $data = array_keys($model->currentTranslate);
        $expectedData = ['en'];
        $this->assertEquals($data, $expectedData);
        $this->assertFalse($model->hasTranslate('fr'));
    }

    public function testLoadTranslateWithoutCurrentTranslate()
    {
        /** @var Post $model */
        $model = Post::find()
            ->with(['postLangs'])
            ->where(['id' => 1])
            ->one();

        $this->assertEquals(array_keys($model->getRelatedRecords()), ['postLangs']);
        $this->assertEquals($model->titleLang, 'title of the first post');
        $this->assertEquals(array_keys($model->getRelatedRecords()), ['postLangs', 'currentTranslate']);
    }

    public function testCallTranslateMethod()
    {
        $model = new Post;

        $this->assertEquals($model->testMethod(), 'OK');
        $this->assertEquals($model->testProperty, 'OK');

        $expectedData = uniqid();
        $model->testProperty = $expectedData;
        $this->assertEquals($model->testProperty, $expectedData);

        $expectedData = uniqid();
        $model->data = $expectedData;
        $this->assertEquals($model->data, $expectedData);

        $this->assertEquals($model->modelTestMethod(), 'OK');
        $this->assertEquals($model->modelTestProperty, 'OK');

        $expectedData = uniqid();
        $model->modelTestProperty = $expectedData;
        $this->assertEquals($model->modelTestProperty, $expectedData);

        $expectedData = uniqid();
        $model->modelData = $expectedData;
        $this->assertEquals($model->modelData, $expectedData);
    }
}