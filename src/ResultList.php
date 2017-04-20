<?php

namespace WebsiteAnalyzer;

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
}
