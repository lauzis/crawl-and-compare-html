<?php


$sitesToCompare = [
    [
        'name' => 'https://example.com/',
        'toCrawl' => [
            'https://linux.com/',
        ],
        'crawled' => []
    ],
];

$compareWith = 'https://test.example.com/';

$hardLimit = 1000;
$sleepTime = 2;
$threads = 6;