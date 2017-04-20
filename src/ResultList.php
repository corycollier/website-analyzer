<?php

namespace WebsiteAnalyzer;

use Illuminate\Support\Arr;

class ResultList extends \ArrayObject
{
    public function getMetrics($type)
    {
        $results = [];
        foreach ($this as $key => $value) {
            $uri = $value->getUri();
            $results[$uri] = $value->getMetric($type);
        }
        return $results;
    }

    public function getMetricSet($path)
    {
        $parts   = explode('.', $path);
        $metric  = array_shift($parts);
        $path    = implode('.', $parts);
        $metrics = $this->getMetrics($metric);
        $results = [];
        
        foreach ($metrics as $uri => $metric) {
            $data = $metric->getData();
            $results[$uri] = Arr::get($data, $path);
        }
        return $results;
    }
}
