<?php

require 'vendor/autoload.php';

use WebsiteAnalyzer\ListBuilder;

ini_set('error_log', 'errors.log');

// Define all of the constants
$urls = array_map('trim', file('data/test.txt'));
$builder = new ListBuilder();
$results = $builder
    ->process($urls)
    ->getResults();

file_put_contents('tmp/processed', serialize($results));

print_r($results->getMetrics('technology-stack'));
print_r($results->getMetrics('css-complexity'));
print_r($results->getMetrics('dns-data'));

die;
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
