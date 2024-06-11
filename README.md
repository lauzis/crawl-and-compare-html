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
1.0.0

## Ides for next releases
- More meta data
  - Add time stats, to compare loading time, if suddenly some url loads much longer as previously
  - Add size stats, to compare if size differ this could be another red flag
- Possibility ontinue previous crawl not register as new crawl so skipp the alreadycashed items


## Change log

### 1.0.0
initial version, mvp, crawls page by given url and generates csv with current status response and previous crawl state.


