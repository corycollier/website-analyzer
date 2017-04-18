<?php

namespace WebsiteAnalyzer;

class ResultList extends \ArrayObject
{
    public function getMetrics($type)
    {
        $results = [];
        foreach ($this as $key => $value) {
            $results[] = $value->getMetric($type);
        }
        return $results;
    }
}
