# Website Analyzer
This library allows a user to run through a list of websites, and determine which ones provide a positive response, and if so, what technology stack they're running on .

## Usage
```php
use WebsiteAnalyzer\ListBuilder;

$builder = new ListBuilder([
  'store_type' => 'file'
  'file_store' => 'path-to-raw-sites.txt',
  'cache_dir' => 'tmp',
]);

$builder->process();

$goodSites = $builder->getSites(ListBuilder::GOOD_SITES);
$badSites = $builder->getSites(ListBuilder::BAD_SITES);
$rawSites = $builder->getSites(ListBuilder::RAW_SITES);
```
