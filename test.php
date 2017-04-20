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

print_r($results->getMetricSet('whois-data.regyinfo.registrar'));
print_r($results->getMetricSet('whois-data.regrinfo.domain.nserver'));
