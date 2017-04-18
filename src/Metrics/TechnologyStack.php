<?php

namespace WebsiteAnalyzer\Metrics;

use WebsiteAnalyzer\Result;

class TechnologyStack implements MetricsInterface
{
    const MIN_SCORE_FOR_POSITIVE_RESULT = 3;
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
        $body = strtolower($subject->getBody());
        $patterns = $this->getTypePatterns();

        foreach ($patterns as $type => $pattern) {
            if ($this->isMatch($body, $pattern)) {
                $this->setBackend($type);
            }
        }

        return $this;
    }

    protected function getTypePatterns()
    {
        return [
            'static' => [
                'assets/css',
                'assets/js',
                'modernizr',
                'bootstrap.min.js'
            ],
            'Wordpress' => [
                'wordpress',
                'wp-content/themes',
                'wp-content/plugins'
            ],
            'Drupal' => [
                'drupal',
                'sites/all/themes',
                'sites/all/modules',
                'sites/default/files'
            ],
            'Dot Net Nuke' => [
                'js/dnncore.js',
                'DnnForge',
                'DesktopModules',
                'js/dnn.xml.js',
                'js/dnn.xml.jsparser.js'
            ],
        ];
    }


    protected function isMatch($content, $patterns = [])
    {
        $score = 0;
        foreach ($patterns as $pattern) {
            if (strpos($content, $pattern)) {
                $score++;
            }
            if ($score >= self::MIN_SCORE_FOR_POSITIVE_RESULT) {
                return true;
            }
        }
        return false;
    }

    protected function setWebserver($webserver)
    {
        $this->webserver = $webserver;
    }

    public function setBackend($type)
    {
        $this->backend = $type;
    }
}
