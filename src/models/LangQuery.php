<?php

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
