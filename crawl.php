<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

$crawl = new Lauzis\CrawlAndCompareHtml\Crawl();

$crawlId = $crawl->getCrawlId();
$crawlCsvPath = __DIR__ . '/output/output.' . $crawlId . '.csv';

if (!file_exists(dirname($crawlCsvPath))) {
    mkdir(dirname($crawlCsvPath));
}

if (file_exists($crawlCsvPath)) {
    unlink($crawlCsvPath);
}
touch($crawlCsvPath);

$header = ['url'];
for ($i = 0; $i < $historyLookup; $i++) {
    $header[] = "History status " . (-$historyLookup + $i);
}
$header[] = 'current crawl status';

$fp = fopen($crawlCsvPath, 'w');
fputcsv($fp, $header);
$rows = [];
$essentialUrlsFinished = false;
$essentialUrlsStarted = false;
$essentialUrlsJsonReportOpened = false;
foreach ($sitesToCompare as $item) {
    //TODO in paralel for each item
    $counter = 0;
    $openedLinksCounter = 0;
    while ((count($item['essentialUrlsToCrawl']) > 0 || count($item['toCrawl'])) > 0 && $hardLimit > $counter) {
        if (count($item['essentialUrlsToCrawl']) > 0) {
            $essentialUrlsStarted = true;
            $url = \Lauzis\CrawlAndCompareHtml\CrawlHelpers::stripTrailingSlashes($item['rootUrl']).array_pop($item['essentialUrlsToCrawl']);
            if (count($item['essentialUrlsToCrawl']) == 0) {
                $essentialUrlsFinished = true;
            }
        } else {
            $url = array_pop($item['toCrawl']);
            if (in_array($url, $item['crawled'])) {
                continue;
            }
        }
        $counter++;
        echo "Crawling... ".($essentialUrlsStarted && !$essentialUrlsFinished ? " essential: ".count($item['essentialUrlsToCrawl']) : "") . "  crawled:" . count($item['crawled']) . "  backlog: " . count($item['toCrawl']) . "\n";
        echo "--------------------------------\n";
        echo "Crawling: " . $url . "\n";
        $data = $crawl->crawlUrl($url);
        $item['crawled'][] = $url;
        $item['toCrawl'] = array_unique(array_merge($item['toCrawl'], $data->getLinks()));

        \Lauzis\CrawlAndCompareHtml\CrawlHelpers::colorLog("Crawled: " . $url . " Status: " . $data->status, $data->status);

        $row = [$url];
        for ($i = 0; $i < $historyLookup; $i++) {
            $prevCrawlId = $crawl->getCrawlId() - ($i + 1);
            $prevCrawl = new Lauzis\CrawlAndCompareHtml\Crawl($prevCrawlId, onlyFromCache: true);

            $prevCrawlData = $prevCrawl->crawlUrl($url);
            $prevCrawlStatus = $prevCrawlData->status ?? "No Data";
            $row[] = $prevCrawlStatus;
        }
        if ($autoOpenUrlInBrowser && $openedLinksCounter < $autoOpenUrlInBrowserLimit && in_array($data->status, $autoOpenUrlInBrowserIfStatusIs)) {
            try {
                $openedLinksCounter++;
                exec($autoOpenUrlInBrowserCommand . ' ' . $url);
            } catch (\Exception $exception) {
                print_r($exception);
            }
        }
        $row[] = $data->status;

        $rows[] = $row;

        fputcsv($fp, $row);

        $essentialPath = $essentialUrlsStarted && !$essentialUrlsFinished ? ".essential" : "";
        file_put_contents($crawlCsvPath . $essentialPath.".json", json_encode($rows));
        if ($essentialUrlsStarted && $essentialUrlsFinished && !$essentialUrlsJsonReportOpened) {
            $essentialUrlsJsonReportOpened = true;
            exec($autoOpenUrlInBrowserCommand ." ". $crawlCsvPath . ".essential.json");
        }
    }
    print ("Crawled: " . $data->url . " with status: " . $data->status . "\n");
    if ($sleepTime) {
        sleep($sleepTime);
    }
}

fclose($fp);