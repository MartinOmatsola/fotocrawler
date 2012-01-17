<?php
include_once 'Fotocrawler.php';

error_reporting(0);

//start point
$url = 'http://www.fotocrib.com';

/* 
 * where the harvested images will be stored
 * must be created prior to running this script 
 * must be in the same directory as this script (required for links to function in report)
 * rw permissions must be set for everyone
*/
$storage_dir = 'tmp/'; //trailing slash is absolutely necessary

//must be one of png, jpg or gif
$storage_format = 'png';

/*
 * specifies the max depth of the crawl
 * eg. 0 indicates links will not be crawled
 * 1 indicates links will be followed 1 level deeper etc 
 */
$max_depth = 1; //Dare: try a max depth of 10 using http://www.yahoo.com as your start point

$crawler = new Fotocrawler();
$crawler->crawl($url, $storage_dir, $storage_format, $max_depth);
$crawler->saveReportAsHtml('tmp/report');

header("Location: tmp/report.html");
?>
