# Website Analyzer
This library allows a user to run through a list of websites, and determine which ones provide a positive response, and if so, what technology stack they're running on .

## Usage
```yaml
cache:
  path: tmp
  type: serialize
data:
  path: data/sites.txt

```

```php
use WebsiteAnalyzer\ListBuilder;

$builder = new ListBuilder('test.yml');

$builder
  ->clear() // clear any previous cache
  ->process() // process the sites
  ->dump() // dump out the results on screen;
);
```
