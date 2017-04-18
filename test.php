<?php

require 'vendor/autoload.php';

use WebsiteAnalyzer\ListBuilder;

// Define all of the constants
$urls = array_map('trim', file('data/sites.txt'));
$builder = new ListBuilder();
$results = $builder
    ->process($urls)
    ->getResults();

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
