<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate;

use yii\db\ActiveRecord;

/**
 * Class TranslatedTrait
 * @package lav45\translate
 *
 * @mixin TranslatedBehavior
 */
trait TranslatedTrait
{
    public function transactions()
    {
        return [
            ActiveRecord::SCENARIO_DEFAULT => ActiveRecord::OP_ALL,
        ];
    }

    /**
     * @param string $language
     * @return bool
     */
    public function hasTranslate($language)
    {
        return isset($this['hasTranslate'][$language]);
    }

    public function getAttribute($name)
    {
        $result = parent::getAttribute($name);
        if ($result === null) {
            $name = $this->getTranslateAttributeName($name);
            $result = $this->getTranslation()->getAttribute($name);
        }
        return  $result;
    }

    public function getOldAttribute($name)
    {
        $result = parent::getOldAttribute($name);
        if ($result === null) {
            $name = $this->getTranslateAttributeName($name);
            $result = $this->getTranslation()->getOldAttribute($name);
        }
        return $result;
    }

    /**
     * Returns a value indicating whether the named attribute has been changed
     * or attribute has been changed in translation relation model.
     * @param string $name the name of the attribute.
     * @param boolean $identical whether the comparison of new and old value is made for
     * identical values using `===`, defaults to `true`. Otherwise `==` is used for comparison.
     * @return boolean whether the attribute has been changed
     */
    public function isAttributeChanged($name, $identical = true)
    {
        $result = parent::isAttributeChanged($name, $identical);
        if ($result === false) {
            $name = $this->getTranslateAttributeName($name);
            $result = $this->getTranslation()->isAttributeChanged($name, $identical);
        }
        return $result;
    }
}