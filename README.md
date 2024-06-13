# Crawl and compare
Script  for crawling stage / production and compare output for differences in ouput
Had to test project before deploy if some other urls are not impacted by my changes
So it crawls and cashes result, and next time crawl it compares the page status with previous one.
So its possible to see if mine, your changes fucked up some other page

## Requirements
- php 8.x
- composer

## Setup
- Clone the repository
- check if you have php installed by running `php -v`
- check if you have composer installed by running `composer -v`
- cppy settings.example.php to settings.php and update the settings
- change values in settings.php to match your environment
- run composer install in project directory `composer install`

## Examples

For example if you just want to see if there is some url with 500 response
`php crawl.php | grep "Status: 500"`

## Version  
1.0.3

## Ides for next releases
- More meta data
  - Add time stats, to compare loading time, if suddenly some url loads much longer as previously
  - Add size stats, to compare if size differ this could be another red flag
- Possibility ontinue previous crawl not register as new crawl so skipp the alreadycashed items
- Add possibility to recrawl the urls that in previous crawl was with some particular status
- Add config, list of esential links, so they would be crawled/tested first, alspo probably have to split, report with esential and full, so it would be possible to check essentials as soon as possible
- Add some settings or regex for list of files for ignoring urls not to crawl.
- store different crawls in different dirs for example www.example.com in on dir other.example.test in other dir
- add info header in csv about site crawled
- avarage time / estimate / compare to previous carwl time
- several threads at the same time
- stats about 200 / 500 / 404

## Change log

### 1.0.3
- added settings for opening failed crawls in firefox as they apper 
- added limiter to the opened links

### 1.0.2
- added coloring to the cli output to have red if status of request 500 or 0


### 1.0.1
- added so that previous crawl report would not be overwritten
- fixed "No data" even if there was previous flag
- in parallel save to json 
- added settings for how many history crawls put in csv/json
- move used argent to the settings 

### 1.0.0
initial version, mvp, crawls page by given url and generates csv with current status response and previous crawl state.


