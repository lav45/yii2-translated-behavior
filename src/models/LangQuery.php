<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Lang]].
 *
 * @see Lang
 */
class LangQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['status' => Lang::STATUS_ACTIVE]);
    }
}
