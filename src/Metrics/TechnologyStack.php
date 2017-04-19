<?php

namespace WebsiteAnalyzer\Metrics;

use WebsiteAnalyzer\Result;

class TechnologyStack implements MetricsInterface
{
    const MIN_SCORE_FOR_POSITIVE_RESULT = 3;
    protected $backend;
    protected $webserver;
    protected $languages;

    public function getType()
    {
        return 'technology-stack';
    }

    public function calculate(Result $subject)
    {
        $this->parseLanguages($subject)
            ->parseBackend($subject)
            ->parseWebserver($subject);

        return $this;
    }

    protected function parseLanguages(Result $subject)
    {
        $headers = $subject->getHeaders();

        if (isset($headers['set-cookie'])) {
            foreach ($headers['set-cookie'] as $cookie) {
                if (strpos($cookie, 'PHPSESSID') === 0) {
                    $this->setLanguage('php', 'unknown');
                }
            }
        }

        if (isset($headers['x-generator'])) {
            foreach ($headers['x-generator'] as $header) {
                $header = strtolower($header);
                if (strpos($header, 'wordpress') !== false) {
                    $this->setLanguage('php', 'unknown');
                }
                if (strpos($header, 'drupal') !== false) {
                    $this->setLanguage('php', 'unknown');
                }
            }
        }

        if (isset($headers['x-powered-by'])) {
            $parts = explode('/', $headers['x-powered-by'][0]);
            if (!isset($parts[1])) {
                $parts[] = 'unknown';
            }
            $this->setLanguage($parts[0], $parts[1]);
        }

        if (! $this->languages) {
            print_r($headers);
        }

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
        $flag = false;

        foreach ($patterns as $type => $pattern) {
            if (!$this->isMatch($body, $pattern)) {
                continue;
            }
            $flag = true;
            $this->setBackend($type);
        }

        if (! $flag) {
            $this->setBackend('static');
        }

        return $this;
    }

    protected function getTypePatterns()
    {
        return [
            'Wordpress' => [
                'wordpress',
                'wp-content/themes',
                'wp-content/plugins',
                'wp-admin/',
                'wp-includes/',
            ],
            'Drupal' => [
                'drupal',
                'sites/all/themes',
                'sites/all/modules',
                'sites/default/files',
                'core/themes/stable',
                'core/assets/vendor/',
                'themes/custom',
            ],
            'Dot Net Nuke' => [
                'js/dnncore.js',
                'dnnforge',
                'desktopmodules',
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

    protected function setLanguage($language, $version)
    {
        $this->languages[$language] = $version;
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
