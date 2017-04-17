<?php

namespace WebsiteAnalyzer;

class Result
{
    protected $type;
    protected $ip;
    protected $uri;
    protected $dns;
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
            'type' => '',
            'ip'   => '',
            'uri'  => '',
            'dns'  => [],
        ];
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function __sleep()
    {
        return [
            // 'body',
            'headers',
            'status',
            'type',
            'ip',
            'uri',
            'dns',
        ];
    }

}
