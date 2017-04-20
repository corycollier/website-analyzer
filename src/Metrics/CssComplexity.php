<?php

namespace WebsiteAnalyzer\Metrics;

use WebsiteAnalyzer\Result;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class CssComplexity implements MetricsInterface
{
    protected $files;
    protected $score;

    public function getType()
    {
        return 'css-complexity';
    }

    public function getData()
    {
        return $this->getFiles();
    }

    public function calculate(Result $result)
    {
        $client = new Client();
        $filenames = $this->getRemoteFilenames($result);

        foreach ($filenames as $path) {
            $this->handleRemoteFile($client, $result, $path);
        }

        if (!count($this->files)) {
            $this->files = $filenames;
        }

        $this->calculateScore();
    }

    protected function calculateScore()
    {
        foreach ($this->files as $score) {
            $this->score = $this->score + $score;
        }
        return $this;
    }

    public function getScore()
    {
        return $this->score;
    }

    protected function getRemoteFilenames(Result $result)
    {
        $body    = $result->getBody();
        $crawler = new Crawler($body);
        $links   = $this->getHeadLinks($crawler);
        $styles  = $this->getStyleLinks($crawler);
        $urls    = $links + $styles;
        $results = $this->getLocalLinks($urls);
        return $results;
    }

    protected function getLocalLinks(array $urls)
    {
        $results = [];
        foreach ($urls as $url) {
            if ($this->isLocalCss($url)) {
                $results[] = $url;
            }
        }
        return $results;
    }

    protected function getHeadLinks(Crawler $crawler)
    {
        $results = [];
        $links = $crawler->filterXPath('descendant-or-self::html/head/link');
        foreach ($links as $link) {
            $results[] = $link->getAttribute('href');
        }
        return $results;
    }

    protected function getStyleLinks(Crawler $crawler)
    {
        $results = [];
        $styles = $crawler->filterXPath('descendant-or-self::html/head/style');
        foreach ($styles as $style) {
            $matches = [];
            preg_match_all('/@import url\(([^)]*)\);?/', $style->nodeValue, $matches);
            if ($matches[1] && count($matches[1])) {
                foreach ($matches[1] as $url) {
                    $results[] = strtr($url, [
                        '"' => '',
                    ]);
                }
            }
        }
        return $results;
    }

    protected function handleRemoteFile(Client $client, Result $result, $path)
    {
        $length = 0;
        try {
            if (! $this->isLocalCss($path)) {
                return;
            }
            $response = $client->request('GET', $this->getFullPath($result, $path));
            $length = count(explode(PHP_EOL, $response->getBody()));
        } catch (\RuntimeException $exception) {
            error_log($exception->getMessage());
        }

        $this->files[$path] = $length;
        return $this;
    }

    protected function getFullPath(Result $result, $path)
    {
        if (strpos($path, 'http') === 0) {
            return $path;
        }

        if (strpos($path, $result->getUri())) {
            return $path;
        }

        if (substr($path, 0, 2) == '//') {
            return 'http:' .  $path;
        }

        $result = $result->getUri() . '/' . $path;

        return strtr($result, [
            '.com//' => '.com/',
            '.org//' => '.org/',
            '.net//' => '.net/',
        ]);
    }

    protected function isLocalCss($name)
    {
        if (!strpos($name, 'css')) {
            return false;
        }

        $skips = $this->getSkips();

        foreach ($skips as $skip) {
            if (strpos($name, $skip)) {
                return false;
            }
        }

        return true;
    }

    protected function getSkips()
    {
        return [
            'jquery',
            'jcarousel',
            'font-awesome',
            'system.css',

            // known drupal files
            'core/themes/stable/css/',
            'modules/acquia',
            'modules/contrib',
            'modules/node',
            'modules/system',
            'modules/user',
            'modules/aggregator',
            'modules/taxonomy',
            'modules/accordion_blocks',
            'modules/fieldgroup',
            'modules/field',
            'modules/poll',
            'modules/comment',
            'mdoules/calendar',
            'modules/date',
            'modules/views',
            'modules/cck',
            'modules/inline_labels',
            'modules/nice_menus',
            'modules/comment',
            'modules/search',
            'ctools',

            //known wordpress files
            'wp-content/plugins/',

            // common 3rd party libraries
            'reset.css',
            'googleapis.com',
            'bootstrap',
            'views_slideshow',
            'lightbox',
            'swftools',
            'thickbox',
            'farbtastic',
            'logintoboggan',

        ];
    }
}
