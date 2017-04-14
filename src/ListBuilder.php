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
    const GOOD_SITES = 'good-sites';
    const BAD_SITES = 'bad-sites';
    const RAW_SITES = 'raw-sites';
    const PROCESSED_SITES = 'processed-sites';

    protected $config;
    protected $factory;

    public function __construct($configFile)
    {
        $this->config = $this->getParsedConfig($configFile);
        $this->factory = new WebsiteResultFactory;
    }

    public function process()
    {
        $processed = $this->getSites(self::PROCESSED_SITES);
        if ($processed) {
            print_r($processed);
        }

        $client = new Client;
        $config = $this->getConfig();
        $data = $this->getSites();
        $factory = $this->getFactory();
        foreach ($data as $site) {
            try {
                $response = $client->request('GET', $site);
                $sites[] = $factory->factory($response);
            } catch (ClientException $exception) {

            } catch (ConnectException $exception) {

            } catch (ServerException $exception) {

            } catch (RequestException $exception) {

            }
        }
        $filename = $config['cache']['path'] . '/'. self::PROCESSED_SITES;
        $data = $this->serializeData($sites);
        $this->filePutContents($filename, $data);
    }

    public function getSites($siteType = self::RAW_SITES)
    {
        $config = $this->getConfig();
        if ($siteType == self::RAW_SITES) {
            return explode(PHP_EOL, $this->fileGetContents($config['data']['path']));
        }

        if ($siteType == self::PROCESSED_SITES) {
            $data = $this->fileGetContents($config['cache']['path'] . '/'. self::PROCESSED_SITES);
            return $this->unserializeData($data);
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

    protected function getParsedConfig($configFile)
    {
        return Yaml::parse($this->fileGetContents($configFile));
    }

    protected function serializeData($data)
    {
        return serialize($data);
    }

    protected function unserializeData($data)
    {
        return unserialize($data);
    }

    protected function fileGetContents($filename)
    {
        if (file_exists($filename)) {
            return file_get_contents($filename);
        }
    }

    protected function filePutContents($filename, $data)
    {
        return file_put_contents($filename, $data);
    }
}
