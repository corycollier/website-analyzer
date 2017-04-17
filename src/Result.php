<?php

namespace WebsiteAnalyzer;

use WebsiteAnalyzer\Metrics\MetricsInterface;

class Result
{
    protected $metrics;
    protected $uri;
    protected $body;
    protected $headers;
    protected $status;

    public function __construct($data = [])
    {
        $defaults = $this->getDefaults();
        $data = array_merge($defaults, $data);
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    protected function getDefaults()
    {
        return [
            'body' => '',
            'headers' => [],
            'status' => '',
            'metrics' => '',
            'uri'  => '',
        ];
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function addMetric(MetricsInterface $metric)
    {
        $type = $metric->getType();
        $this->metrics[$type] = $metric;
        return $this;
    }

    public function __sleep()
    {
        return [
            'body',
            'headers',
            'status',
            'metrics',
            'uri',
        ];
    }

    public function __debugInfo()
    {
        return [
            'uri'     => $this->uri,
            // 'body'    => substr($this->body, 0, 200),
            // 'headers' => $this->headers,
            // 'status'  => $this->status,
            'metrics' => $this->metrics,
        ];

    }

}
