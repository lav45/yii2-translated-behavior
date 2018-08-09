<?php
/**
 * @link https://github.com/LAV45/yii2-translated-behavior
 * @copyright Copyright (c) 2015 LAV45!
 * @author Alexey Loban <lav451@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace lav45\translate\models;

use yii\di\Instance;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;

/**
 * Class LangCache
 * @package lav45\translate\models
 */
class LangCache extends Lang
{
    /**
     * @var string
     */
    public static $cacheKey = 'LangCache::cacheKey';
    /**
     * @var string|array|CacheInterface
     */
    public $cache = 'cache';

    /**
     * @return object|CacheInterface
     */
    public function getCache()
    {
        return Instance::ensure($this->cache, CacheInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $this->invalidateCache();
    }

    /**
     * @inheritdoc
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!empty($changedAttributes)) {
            $this->invalidateCache();
        }
    }

    /**
     * Invalidate cache for all cached lists
     */
    public function invalidateCache()
    {
        TagDependency::invalidate($this->getCache(), static::$cacheKey);
    }

    /**
     * @return TagDependency
     */
    public static function getDependency()
    {
        return new TagDependency(['tags' => static::$cacheKey]);
    }

    /**
     * @param bool $active default false
     * @return array
     */
    public static function getList($active = false)
    {
        return static::getDb()->cache(function() use ($active) {
            return parent::getList($active);
        }, null, static::getDependency());
    }

    /**
     * @param bool $active default true
     * @return array
     */
    public static function getLocaleList($active = true)
    {
        return static::getDb()->cache(function() use ($active) {
            return parent::getLocaleList($active);
        }, null, static::getDependency());
    }
}