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
            'title' => 'test for the create new post',
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
        $model->title = 'new title';

        $this->assertTrue($model->save(false));

        $dataSet = $this->getConnection()->createDataSet(['post_lang']);
        $expectedDataSet = $this->createFlatXMLDataSet(__DIR__ . '/data/testEditPost.xml');
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testEditTranslate()
    {
        /** @var Post $model */
        $model = Post::findOne(1);
        $model->setLanguage('ru');
        $model->title = 'new title';

        $this->assertTrue($model->save(false));

        $dataSet = $this->getConnection()->createDataSet(['post_lang']);
        $expectedDataSet = $this->createFlatXMLDataSet(__DIR__ . '/data/testEditTranslate.xml');
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testHasTranslateRelations()
    {
        /** @var Post $model */
        $model = Post::findOne(1);
        $data = array_keys($model->hasTranslate);
        $expectedData = ['en', 'ru'];
        $this->assertEquals($data, $expectedData);
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

        Yii::$app->language = 'fr-FR';

        /** @var Post $model */
        $model = Post::findOne(1);
        $this->assertTrue(count($model->currentTranslate) == 1);

        $data = array_keys($model->currentTranslate);
        $expectedData = ['en'];
        $this->assertEquals($data, $expectedData);
    }
}