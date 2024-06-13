<?php

$essentialUrlsToCrawl = [];
include(__DIR__.'/config.essential.url.list.php');
const USER_AGENTS = ['Mozilla/5.0 (I AM ROBOT)'];

$sitesToCompare = [
    [
        'name' => 'https://example.com/',
        'toCrawl' => [
            'https://linux.com/',
        ],
        'crawled' => [],
        'essentialUrlsToCrawl'=>$essentialUrlsToCrawl,
    ],
];

$compareWith = 'https://test.example.com/';

$hardLimit = 1000;
$sleepTime = 2;
$threads = 6;
//how many previous crawls to look up
$historyLookup = 3;

//open in browser urls with 500/404/0
$autoOpenUrlInBrowser = true;
$autoOpenUrlInBrowserIfStatusIs = [500,404,0];
$autoOpenUrlInBrowserLimit = 20;
$autoOpenUrlInBrowserCommand = "/usr/bin/firefox";