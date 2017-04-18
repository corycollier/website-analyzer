<?php

namespace WebsiteAnalyzer;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;

class ListBuilder
{
    protected $config;
    protected $factory;
    protected $list;
    protected $results;

    public function __construct()
    {
        $this->factory = new ResultFactory();
    }

    public function process($uris)
    {
        $sites   = [];
        $factory = $this->getFactory();
        $count   = count($uris);
        $client  = new Client();
        foreach ($uris as $i => $uri) {
            try {
                $response = $client->request('GET', $uri);
                $sites[] = $factory->factory($uri, $response);
            } catch (ClientException $exception) {

            } catch (ConnectException $exception) {

            } catch (ServerException $exception) {

            } catch (RequestException $exception) {

            }
            $this->reportProgress($i, $count);
        }

        $this->setResults($sites);
        return $this;
    }

    public function setResults($data)
    {
        $this->results = $data;
        return $this;
    }

    public function getResults($data)
    {
        return $this->results;
    }

    public function reportProgress($step, $total)
    {
        $perc = floor(($step / $total) * 100);
        $left = 100 - $perc;
        $write = sprintf("\033[0G\033[2K[%'={$perc}s>%-{$left}s] - $perc%% - $step/$total", "", "");
        fwrite(STDERR, $write);
    }

    protected function getFactory()
    {
        return $this->factory;
    }

    protected function getConfig()
    {
        return $this->config;
    }
}
