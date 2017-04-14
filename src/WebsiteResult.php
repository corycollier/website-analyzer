<?php

namespace WebsiteAnalyzer;

class WebsiteResult
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

    public function analyze()
    {
        $body = $this->getBody();
        if ($this->isStatic($body)) {
            $this->setType('static');
        }
        if ($this->isWordpress($body)) {
            $this->setType('wordpress');
        }
        if ($this->isDrupal($body)) {
            $this->setType('drupal');
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

    protected function isDrupal($contents) {
      $result = preg_match('/Drupal/', $contents);
      return $result;
    }

    protected function isWordpress($contents) {
      $result = preg_match('/Wordpress/', $contents);
      return $result;
    }

    protected function isStatic($contents) {
      $result = preg_match('/assets\/css/', $contents);
      return $result;
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
