<?php

namespace WebsiteAnalyzer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;

class ListBuilder
{
    const PROGRESS_BAR = "\"\033[0G\033[2K %d%% - %d/%d";
    protected $factory;
    protected $results;

    public function __construct()
    {
        $this->factory = new ResultFactory();
        $this->results = new ResultList();
    }

    public function process($uris)
    {
        $list   = $this->getResults();
        $factory = $this->getFactory();
        $count   = count($uris);
        $client  = $this->getClient();
        foreach ($uris as $i => $uri) {
            try {
                $response = $client->request('GET', $uri);
                $list[] = $factory->factory($uri, $response);
            } catch (\RuntimeException $exception) {
                error_log($exception->getMessage());
            }

            $this->reportProgress($i, $count);
        }

        $this->setResults($list);
        return $this;
    }

    public function setResults(ResultList $data)
    {
        $this->results = $data;
        return $this;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function getClient()
    {
        return new Client();
    }

    protected function reportProgress($step, $total)
    {
        $perc = floor(($step / $total) * 100);
        $write = sprintf(self::PROGRESS_BAR, $perc, $step, $total);
        fwrite(STDERR, $write);
    }

    public function getFactory()
    {
        return $this->factory;
    }
}



//"\033[0G\033[2K[%'={$perc}s>%-{$left}s] - $perc%% - $step/$total"
// $template = "\"\033[0G\033[2K[%'=%ds>%-%ds] - %d%% - %d/%d";
// sprintf(self::PROGRESS_BAR, $perc, $left, $perc, $step, $total);
