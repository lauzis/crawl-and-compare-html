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
for($i=0; $i < $historyLookup; $i++){
    $header[]="History status ".(-$historyLookup+$i);
}
$header[] = 'current crawl status';

$fp = fopen($crawlCsvPath, 'w');
fputcsv($fp, $header);
$rows = [];
foreach ($sitesToCompare as $item) {
    //TODO in paralel for each item
    $counter = 0;
    while (count($item['toCrawl']) > 0 && $hardLimit > $counter) {
        $url = array_pop($item['toCrawl']);
        if (in_array($url, $item['crawled'])) {
            continue;
        }
        $counter++;
        echo "Items to crawl: " . count($item['crawled']) . " / " . count($item['toCrawl']) . "\n";
        echo "--------------------------------\n";
        echo "Crawling: " . $url . "\n";
        $data = $crawl->crawlUrl($url);
        $item['crawled'][] = $url;
        $item['toCrawl'] = array_unique(array_merge($item['toCrawl'], $data->getLinks()));
        echo "Crawled: " . $url . " Status: " . $data->status . "\n";

        $row = [$url];
        for($i=0; $i<$historyLookup; $i++){
            $prevCrawlId = $crawl->getCrawlId() - ($i+1);
            $prevCrawl = new Lauzis\CrawlAndCompareHtml\Crawl($prevCrawlId, onlyFromCache: true);

            $prevCrawlData = $prevCrawl->crawlUrl($url);
            $prevCrawlStatus = $prevCrawlData->status ?? "No Data";
            $row[] = $prevCrawlStatus;
        }
        $row[] = $data->status;

        $rows[] = $row;

        fputcsv($fp, $row);
        file_put_contents($crawlCsvPath.".json",json_encode($rows));
    }
    print ("Crawled: " . $data->url . " with status: " . $data->status . "\n");
    if ($sleepTime) {
        sleep($sleepTime);
    }
}

fclose($fp);