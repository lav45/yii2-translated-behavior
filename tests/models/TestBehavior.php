<?php

namespace tests\models;

use yii\base\Behavior;

/**
 * Class TestBehavior
 * @package tests\models
 *
 * @property string $data
 */
class TestBehavior extends Behavior
{
    private $_data;

    public $testProperty = 'OK';

    public function testMethod()
    {
        return 'OK';
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setData($value)
    {
        $this->_data = $value;
    }
}