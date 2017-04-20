<?php

namespace WebsiteAnalyzer\Metrics;

use WebsiteAnalyzer\Result;

class WhoisData implements MetricsInterface
{
    protected $agent;
    protected $data;

    public function __construct()
    {
        $this->agent = new \Whois();
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getAgent()
    {
        return $this->agent;
    }

    public function calculate(Result $subject)
    {
        $domain = $this->getDomainName($subject);
        $agent = $this->getAgent();
        $result = $agent->lookup($domain);
        $this->setData(array_intersect_key($result, [
            'regrinfo' => null,
            'regyinfo' => null,
        ]));
        return $this;
    }

    public function getDomainName(Result $subject)
    {
        $uri = $subject->getUri();

        $uri = strtr($uri, [
            'http://' => '',
            'https://' => '',
            '//' => '',
        ]);

        $parts = explode('/', $uri);
        $domain = $parts[0];
        return strtolower($domain);
    }

    public function getType()
    {
        return 'whois-data';
    }

    public function __debugInfo()
    {
        return [
            'data' => $this->data,
        ];
    }
}
