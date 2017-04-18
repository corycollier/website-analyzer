<?php

namespace WebsiteAnalyzer\Metrics;

use WebsiteAnalyzer\Result;
use \Net_DNS2_Resolver;
use \Net_DNS2_Exception;

class DnsData implements MetricsInterface
{
    const ERR_QUERY_FAIL = 'DNS query for [%s] failed with message [%s]';
    protected $resolver;
    protected $data;

    public function __construct()
    {
        $this->resolver = new \Net_DNS2_Resolver([
            'nameservers' => [
                '8.8.8.8',
                '8.8.4.4',
                '208.69.38.205',
            ],
        ]);
    }

    public function calculate(Result $subject)
    {
        $resolver = $this->getResolver();
        try {
            $domain = $this->getDomainName($subject);
            $result = $resolver->query($domain, 'A');
            foreach ($result->answer as $answer) {
                $this->data[] = (array)$answer;
            }
        } catch (\Net_DNS2_Exception $exception) {
            error_log(sprintf(
                self::ERR_QUERY_FAIL, $subject->getUri(), $exception->getMessage()
            ));
        }

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    protected function getDomainName(Result $subject)
    {
        $uri = $subject->getUri();
        $uri = strtr($uri, [
            'http://' => '',
            'https://' => '',
        ]);
        $parts = explode('/', $uri);
        return $parts[0];
    }

    protected function getResolver()
    {
        return $this->resolver;
    }

    public function getType()
    {
        return 'dns-data';
    }

    public function report()
    {
        return '';
    }

    public function __debugInfo()
    {
        return [
            'data' => $this->data,
        ];
    }
}
