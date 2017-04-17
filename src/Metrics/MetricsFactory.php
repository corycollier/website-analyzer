<?php

namespace WebsiteAnalyzer\Metrics;

class MetricsFactory
{
    const ERR_BAD_TYPE = 'The given metric type [%s] does not exist';

    public function factory($type)
    {
        $map = [
            'css-complexity' => '\WebsiteAnalyzer\Metrics\CssComplexity',
            'technology-stack' => '\WebsiteAnalyzer\Metrics\TechnologyStack',
            'dns-data' => '\WebsiteAnalyzer\Metrics\DnsData',
        ];

        if (! array_key_exists($type, $map)) {
            throw new RuntimeException(sprintf(self::ERR_BAD_TYPE, $type));
        }

        return new $map[$type];
    }
}
