<?php
/**
 * Created by PhpStorm.
 * User: lav45
 * Date: 23.09.15
 * Time: 0:49
 */

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