# Website Analyzer
[![Build Status](https://travis-ci.org/corycollier/website-analyzer.svg?branch=master)](https://travis-ci.org/corycollier/website-analyzer)
This library allows a user to run through a list of websites, and determine which ones provide a positive response, and if so, what technology stack they're running on .

## Usage

```php
require 'vendor/autoload.php';

use WebsiteAnalyzer\ListBuilder;

// Define all of the constants
$urls = array_map('trim', file('data/test.txt'));
$builder = new ListBuilder();
$results = $builder
    ->process($urls)
    ->getResults();

file_put_contents('tmp/processed', serialize($results));

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

```
