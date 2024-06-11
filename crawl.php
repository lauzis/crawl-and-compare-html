<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';
const OUTPUT_CSV = __DIR__ . '/output/output.csv';

$crawl = new Lauzis\CrawlAndCompareHtml\Crawl();

if (!file_exists(dirname(OUTPUT_CSV))) {
    mkdir(dirname(OUTPUT_CSV));
}

if (file_exists(OUTPUT_CSV)) {
    unlink(OUTPUT_CSV);
}
touch(OUTPUT_CSV);

$header = ['url', 'prev crawl status', 'status'];
$fp = fopen(OUTPUT_CSV, 'w');
fputcsv($fp, $header);
foreach ($sitesToCompare as $item) {
    //TODO in paralel for each item
    $counter = 0;
    while (count($item['toCrawl']) > 0 && $hardLimit > $counter) {
        $url = array_pop($item['toCrawl']);
        if (in_array($url, $item['crawled'])) {
            continue;
        }
        $counter++;
        echo "Items to crawl: ".count($item['crawled']) ." / " . count($item['toCrawl']) . "\n";
        echo "--------------------------------\n";
        echo "Crawling: " . $url . "\n";
        $data = $crawl->crawlUrl($url);
        $item['crawled'][] = $url;
        $item['toCrawl'] = array_merge($item['toCrawl'], $data->getLinks());
        echo "Crawled: " . $url ." Status: ".$data->status . "\n";

        $prevCrawlId = $crawl->getCrawlId() - 1;
        $prevCrawl = new Lauzis\CrawlAndCompareHtml\Crawl($prevCrawlId, onlyFromCache: true);
        $prevCrawlData = $prevCrawl->crawlUrl($url);
        $prevCrawlStatus = $prewCrawlData->status ?? "No Data";

        $row = [$url, $prevCrawlStatus, $data->status];

        fputcsv($fp, $row);
    }
    $data = $crawl->crawlUrl($item);
    print ("Crawled: " . $data->url . " with status: " . $data->status . "\n");
    if ($sleepTime) {
        sleep($sleepTime);
    }
}

fclose($fp);