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
            ActiveRecord::SCENARIO_DEFAULT => ActiveRecord::OP_INSERT | ActiveRecord::OP_UPDATE,
        ];
    }
}