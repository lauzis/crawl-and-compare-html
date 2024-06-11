<?php

namespace Lauzis\CrawlAndCompareHtml;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CrawlCache
{
    public static function getCacheDir()
    {
        return dirname(__FILE__) . '/../cache';
    }

    public static function get(string $key): null|string|int|CrawledData
    {
        $cache = new FilesystemAdapter(directory: self::getCacheDir());
        $cashedData = $cache->getItem($key);
        if (!$cashedData->isHit()) {
            return null;
        }
        return $cashedData->get();
    }

    public static function set(string $key, null|string|int|CrawledData $data): void
    {
        $cache = new FilesystemAdapter(directory: self::getCacheDir());
        $cashedData = $cache->getItem($key);
        $cashedData->set($data);
        $cache->save($cashedData);
    }
}