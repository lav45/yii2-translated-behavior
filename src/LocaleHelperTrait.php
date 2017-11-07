<?php
/**
 * Created by PhpStorm.
 * User: loal
 * Date: 04.07.16
 * Time: 15:10
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