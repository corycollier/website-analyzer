<?php

namespace WebsiteAnalyzer\Metrics;

use WebsiteAnalyzer\Result;

class TechnologyStack implements MetricsInterface
{
    protected $backend;
    protected $webserver;


    public function getType()
    {
        return 'technology-stack';
    }

    public function calculate(Result $subject)
    {
        $this->parseBackend($subject)->parseWebserver($subject);

        return $this;
    }

    protected function parseWebserver(Result $subject)
    {
        $headers = $subject->getHeaders();
        if (!array_key_exists('server', $headers)) {
            $headers['server'] = ['unknown'];
        }
        $this->setWebserver(end($headers['server']));
        return $this;
    }

    protected function parseBackend(Result $subject)
    {
        $body = $subject->getBody();
        $headers = $subject->getHeaders();
        if ($this->isStatic($body)) {
            $this->setBackend('static');
        }

        if ($this->isWordpress($body)) {
            $this->setBackend('wordpress');
        }

        if ($this->isDrupal($body)) {
            $this->setBackend('drupal');
        }

        return $this;
    }

    protected function setWebserver($webserver)
    {
        $this->webserver = $webserver;
    }

    public function setBackend($type)
    {
        $this->backend = $type;
    }

    public function report()
    {
        return '';
    }

    protected function isDrupal($contents) {
        $contents = strtolower($contents);
        return (bool) strpos($contents, 'drupal');
    }

    protected function isWordpress($contents) {
        $contents = strtolower($contents);
        return (bool) strpos($contents, 'wordpress');
    }

    protected function isStatic($contents) {
        $contents = strtolower($contents);
        return (bool) strpos($contents, 'assets/css');
    }
}
