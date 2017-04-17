<?php

namespace WebsiteAnalyzer;

use WebsiteAnalyzer\Metrics\MetricsFactory;

class ResultAnalyzer
{
    protected $metricsFactory;

    public function __construct()
    {
        $this->metricsFactory = new MetricsFactory();
    }

    public function analyze(Result $subject)
    {
        $factory = $this->getMetricsFactory();
        $metrics = [
            'technology-stack',
            'css-complexity',
            'dns-data',
        ];

        foreach ($metrics as $type) {
            $metric = $factory->factory($type);
            $metric->calculate($subject);
            $subject->addMetric($metric);
        }
    }

    public function getMetricsFactory()
    {
        return $this->metricsFactory;
    }
}
