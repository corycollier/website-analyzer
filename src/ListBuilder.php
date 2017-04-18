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
    const GOOD_SITES      = 'good-sites';
    const BAD_SITES       = 'bad-sites';
    const RAW_SITES       = 'raw-sites';
    const PROCESSED_SITES = 'processed-sites';

    protected $config;
    protected $factory;

    public function __construct($configFile)
    {
        $file          = new File($configFile);
        $config        = $file->load()->getData();
        $this->config  = Yaml::parse($config);
        $this->factory = new ResultFactory();
    }

    public function clear()
    {
        $config  = $this->getConfig();
        $filename = __DIR__ . '/../' . $config['cache']['path'] . '/'. self::PROCESSED_SITES;
        $file = new File($filename);
        $file->delete();
        return $this;
    }

    public function process()
    {
        $processed = $this->getSites(self::PROCESSED_SITES);
        if ($processed) {
            return $this;
        }

        $client  = new Client();
        $sites   = [];
        $config  = $this->getConfig();
        $data    = $this->getSites();
        $factory = $this->getFactory();
        $count   = count($data);
        foreach ($data as $i => $site) {
            try {
                $response = $client->request('GET', $site);
                $sites[] = $factory->factory($site, $response);
            } catch (ClientException $exception) {

            } catch (ConnectException $exception) {

            } catch (ServerException $exception) {

            } catch (RequestException $exception) {

            }
            $this->reportProgress($i, $count, $site);
        }

        $filename = $config['cache']['path'] . '/'. self::PROCESSED_SITES;
        $file = new File($filename, $sites);
        $file->serialize()->save();
        return $this;
    }

    public function reportProgress($step, $total)
    {
        $perc = floor(($step / $total) * 100);
        $left = 100 - $perc;
        $write = sprintf("\033[0G\033[2K[%'={$perc}s>%-{$left}s] - $perc%% - $step/$total", "", "");
        fwrite(STDERR, $write);
    }

    public function dump()
    {
        $sites = $this->getSites(self::PROCESSED_SITES);
        print_r($sites);
        return 0;
    }

    public function getSites($siteType = self::RAW_SITES)
    {
        $config = $this->getConfig();
        $filename = $config['data']['path'];
        if ($siteType == self::PROCESSED_SITES) {
            $filename = $config['cache']['path'] . '/'. self::PROCESSED_SITES;
        }
        $file = new File($filename);
        try {
            return $file->load()->parse()->getData();
        } catch (RuntimeException $exception) {
            return false;
        }
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
