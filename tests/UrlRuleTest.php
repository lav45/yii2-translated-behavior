<?php

namespace tests;

use Yii;
use yii\web\Application;
use lav45\translate\models\Lang;
use PHPUnit\Framework\TestCase;

class UrlRuleTest extends TestCase
{
    protected function mockWebApplication()
    {
        new Application([
            'id' => 'test_app',
            'basePath' => __DIR__,
            'components' => [
                'urlManager' => [
                    'baseUrl' => '',
                    'hostInfo' => 'http://site.com',
                    'scriptUrl' => '/index.php',
                    'showScriptName'  => false,
                    'enablePrettyUrl' => true,
                    'rules' => [
                        [
                            'class' => 'lav45\translate\web\UrlRule',
                            'pattern' => '<_lang:' . Lang::PATTERN . '>',
                            'route' => 'page/index',
                        ],
                        [
                            'class' => 'lav45\translate\web\UrlRule',
                            'pattern' => '<_lang:' . Lang::PATTERN . '>/<name:[\w\-]+>',
                            'route' => 'page/view',
                            'suffix' => '.html',
                        ],
                    ],
                ],
            ]
        ]);
    }

    public function testCreateUrl()
    {
        $tests = [
            '/en' => ['page/index'],
            '/ru' => ['page/index', '_lang' => 'ru'],
            '/ru?param=value' => ['page/index', 'param' => 'value', '_lang' => 'ru'],

            '/en/pageName.html' => ['page/view', 'name' => 'pageName'],
            '/ru/test-page.html' => ['page/view', 'name' => 'test-page', '_lang' => 'ru'],
            '/ru/test-page.html?param=value' => ['page/view', 'name' => 'test-page', '_lang' => 'ru', 'param' => 'value'],

            '/' => '/',
            '/site/index' => ['site/index'],
            '/site/index?param=val' => ['site/index', 'param' => 'val'],
            '/site/index?param=val&_lang=ru' => ['site/index', 'param' => 'val', '_lang' => 'ru'],
        ];

        $this->beginTest($tests);
    }

    /**
     * @param array $tests
     */
    protected function beginTest($tests)
    {
        $this->mockWebApplication();
        $urlManager = Yii::$app->getUrlManager();

        foreach($tests as $result => $params) {
            $this->assertEquals($urlManager->createUrl($params), $result);
        }
    }

}