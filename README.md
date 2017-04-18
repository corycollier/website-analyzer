# Website Analyzer
[![Build Status](https://travis-ci.org/corycollier/website-analyzer.svg?branch=master)](https://travis-ci.org/corycollier/website-analyzer)

This library allows a user to run through a list of websites, and determine which ones provide a positive response, and if so, aggregate a bunch of information about them.

## Usage

```php
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

print_r($results->getMetrics('technology-stack'));
print_r($results->getMetrics('css-complexity'));
print_r($results->getMetrics('dns-data'));

```
