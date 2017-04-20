<?php

require 'vendor/autoload.php';

use WebsiteAnalyzer\ListBuilder;

ini_set('error_log', 'errors.log');

// Define all of the constants
$urls = array_map('trim', file('data/sites.txt'));
$builder = new ListBuilder();
$results = $builder
    ->process($urls)
    ->getResults();

print_r($results->getMetrics('technology-stack'));
print_r($results->getMetrics('css-complexity'));
print_r($results->getMetrics('dns-data'));
print_r($results->getMetrics('whois-data'));
