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
        $metrics = $this->getMetricTypes();

        foreach ($metrics as $type) {
            $metric = $factory->factory($type);
            $metric->calculate($subject);
            $subject->addMetric($metric);
        }
        return $this;
    }

    public function getMetricTypes()
    {
        return [
            'technology-stack',
            'css-complexity',
            'whois-data',
            'dns-data',
        ];
    }

    public function getMetricsFactory()
    {
        return $this->metricsFactory;
    }
}
