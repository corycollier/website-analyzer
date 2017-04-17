<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use WebsiteAnalyzer\ListBuilder;

// Define all of the constants
define('UNFILTERED_SITES_FILENAME', 'list-of-sites.txt');
define('GOOD_SITES_FILENAME', 'good-sites.txt');
define('BAD_SITES_FILENAME', 'bad-sites.txt');
define('CATEGORIZED_SITES_FILENAME', 'categorized-sites.txt');

$builder = new ListBuilder('test.yml');
$results = $builder
    // ->clear()
    ->process()
    ->getSites(ListBuilder::PROCESSED_SITES);

$ips = [];
$cssScores = [];
foreach ($results as $result) {
    $uri = $result->getUri();
    $metrics = $result->getMetrics();
    $dnsData = $metrics['dns-data']->getData();
    $ip = $dnsData[0]['address'];
    if (! array_key_exists($ip, $ips)) {
        $ips[$ip] = [];
    }
    $ips[$ip][] = $uri;
    $cssScores[$uri] = $metrics['css-complexity']->getScore();
}

print_r($results);
print_r($ips);
print_r($cssScores);
