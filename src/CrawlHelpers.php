<?php

namespace Lauzis\CrawlAndCompareHtml;

class CrawlHelpers
{

    /*
     *  source from https://stackoverflow.com/questions/34034730/how-to-enable-color-for-php-cli
     */
    public static function colorLog(string $str, int|string $type = '')
    {
        switch ($type) {
            case 500:
            case 'error': //error
                echo "\033[31m$str \033[0m\n";
                break;
            case 'success': //success
                echo "\033[32m$str \033[0m\n";
                break;
            case 0:
            case 'warning': //warning
                echo "\033[33m$str \033[0m\n";
                break;
            case 'info': //info
                echo "\033[36m$str \033[0m\n";
                break;
            default:
                # code...
                echo "$str \n";
                break;
        }
    }

    public static function stripTrailingSlashes(string $str): string
    {
        return rtrim($str, '/');
    }

}