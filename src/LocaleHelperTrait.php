<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate;

use Locale;

trait LocaleHelperTrait
{
    /**
     * @var \Closure
     */
    public $primaryLanguage;

    /**
     * @param string $locale `en-EN`, `ru-RU`
     * @return string en or ru
     */
    public function getPrimaryLanguage($locale)
    {
        if ($this->primaryLanguage !== null && is_callable($this->primaryLanguage)) {
            return call_user_func($this->primaryLanguage, $locale);
        }
        return extension_loaded('intl') ?
            Locale::getPrimaryLanguage($locale) : substr($locale, 0, 2);
    }
}