<?php

namespace Lauzis\CrawlAndCompareHtml;


class CrawledData
{
    public string $content = '';
    private array $links = [];
    public int $status = 0;
    public int $crawlDate = 0;
    public string $url = '';

    public function setData($data): void
    {
        $this->content = $data['content'];
        $this->setLinks($data['links']);
        $this->status = $data['status'];
        $this->crawlDate = $data['crawlDate'];
    }

    public function getData(): array
    {
        return [
            'content' => $this->content,
            'links' => $this->links,
            'status' => $this->status,
            'crawlDate' => $this->crawlDate
        ];
    }

    private function getDomainFromUrl($url)
    {
        $urlParts = parse_url($url);
        return $urlParts['scheme'] . '://' . $urlParts['host'];
    }

    private function urlStartsWithLetter($url)
    {
        return preg_match('/^[a-zA-Z]/', $url);
    }

    private function startsWithDoubleSlashes($url)
    {
        return preg_match('/^\/\//', $url);
    }

    private function startsWithSingleSlash($url)
    {
        return preg_match('/^\//', $url);
    }

    private function startsWithHttp($url)
    {
        return preg_match('/^http/', $url);
    }

    private function startsWithHttps($url)
    {
        return preg_match('/^https/', $url);
    }


    private function startsMailTo($url)
    {
        return preg_match('/^mailto:/', $url);
    }

    private function startsWithHash($url)
    {
        return preg_match('/^#/', $url);
    }


    /*
     * Skipping outgoing files, fixing links that starts with / or //
     * And some other cases  that we need to ignore or skip
     * For example # links, mailto links, etc
     */
    public function fixLinks(): void
    {
        $this->links = array_map(function ($link) {

            if (empty(trim($link))) {
                return $this->url;
            }
            if ($this->startsWithHash($link)) {
                return $this->url;
            }
            if ($this->startsMailTo($link)) {
                return $this->url;
            }
            $domain = $this->getDomainFromUrl($this->url);
            $domainOfLink = $domain;
            if ($this->startsWithHttps($link) || $this->startsWithHttp($link)) {
                $domainOfLink = $this->getDomainFromUrl($link);
            }

            if ($domainOfLink !== $domain) {
                return $this->url;
            }

            if ($this->startsWithHttp($link) || $this->startsWithHttps($link)) {
                return $link;
            }

            if (substr_count($link, $domain) > 0) {
                return $link;
            }

            if ($this->startsWithDoubleSlashes($link)) {
                $link = 'https:' . $link;
                return $link;
            }
            if ($this->startsWithSingleSlash($link)) {
                $link = $this->getDomainFromUrl($this->url) . $link;
                return $link;
            }
            if ($this->urlStartsWithLetter($link)) {
                $link = $this->url . "/" . $link;
                return $link;
            }
            return $link;
        }, $this->links);
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks($links): void
    {
        $this->links = $links;
        $this->fixLinks();
    }
}