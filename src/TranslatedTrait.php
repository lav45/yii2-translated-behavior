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
        if (parent::isAttributeChanged($name, $identical) === true) {
            return true;
        }
        $attributeName = $this->getTranslateAttributeName($name);
        return $this->getTranslation()->isAttributeChanged($attributeName, $identical);
    }

    /**
     * Loads default values from database table schema
     *
     * You may call this method to load default values after creating a new instance:
     *
     * ```php
     * // class Customer extends \yii\db\ActiveRecord
     * $customer = new Customer();
     * $customer->loadDefaultValues();
     * ```
     *
     * @param bool $skipIfSet whether existing value should be preserved.
     * This will only set defaults for attributes that are `null`.
     * @return $this the model instance itself.
     */
    public function loadDefaultValues($skipIfSet = true)
    {
        $this->getTranslation()->loadDefaultValues($skipIfSet);
        return parent::loadDefaultValues($skipIfSet);
    }
}