<?php

namespace Lauzis\CrawlAndCompareHtml;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Lauzis\CrawlAndCompareHtml\CrawledData;

class Crawl
{
    private CrawledData $data;
    private int $crawlId;

    private bool $onlyFromCache = false;

    public function __construct(?int $cacheId = null, bool $onlyFromCache = false)
    {
        $this->crawlId = $cacheId ?? 0;
        if (!$cacheId) {
            $cashedData = CrawlCache::get("crawlId");
            if (!$cashedData) {
                CrawlCache::set("crawlId", 0);
            }
            $this->crawlId = $cashedData + 1;
            CrawlCache::set("crawlId", $this->crawlId);
        }
        if($onlyFromCache){
            $this->onlyFromCache = true;
        }
        $this->data = new CrawledData();
    }

    private function reset(): void
    {
        $this->data = new CrawledData();
    }


    private function getCacheKey(string $url, ?int $crawlId = null): string
    {
        $cacheKey = ($crawlId ? $crawlId : $this->crawlId) . '/' . md5($url);
        return $cacheKey;
    }

    private function makeRequest(): \StdClass
    {
        $url = $this->data->url;
        $user_agent = USER_AGENTS[0];

        $options = array(

            CURLOPT_CUSTOMREQUEST => "GET",        //set request type post or get
            CURLOPT_POST => false,        //set to GET
            CURLOPT_USERAGENT => $user_agent, //set user agent
//            CURLOPT_COOKIEFILE => "cookie.txt", //set cookie file
//            CURLOPT_COOKIEJAR => "cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => false,     // follow redirects
            CURLOPT_ENCODING => "",       // handle all encodings
            CURLOPT_AUTOREFERER => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT => 120,      // timeout on response
            CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $status = $header['http_code'];

        $returnData = new \StdClass();
        $returnData->url = $url;
        $returnData->content = $content;
        $returnData->status = $status;
        return $returnData;
    }

    public function crawlUrl($url)
    {
        $cacheKey = $this->getCacheKey($url);

        $data = CrawlCache::get($cacheKey);
        if ($data) {
            return $data;
        }
        if($this->onlyFromCache){
            return $this->data;
        }
        $this->reset();
        $this->data->url = $url;

        try {
            $requestData = $this->makeRequest();
            $this->data->content = $requestData->content;
            $this->data->status = $requestData->status;
            $this->getUrls();

            $this->data->crawlDate = time();
            CrawlCache::set($cacheKey, $this->data);

        } catch (Exception $e) {

        }

        return $this->data;
    }

    public function getUrls(): void
    {
        $pattern = '/<a\s+(?:[^>]*?\s+)?href="([^"]*)"/';
        preg_match_all($pattern, $this->data->content, $matches);
        $this->data->setLinks($matches[1]);
    }

    public function compareWith($url, $crawlId)
    {
        $cacheKey = $this->getCacheKey($url, $crawlId);
        $data = CrawlCache::get($cacheKey);

    }

    public function getCrawlId(): int
    {
        return $this->crawlId;
    }

}