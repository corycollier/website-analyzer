<?php

namespace WebsiteAnalyzer;

use Psr\Http\Message\ResponseInterface;

class ResultFactory
{
    protected $analyzer;

    public function __construct()
    {
        $this->analyzer = new ResultAnalyzer();
    }

    public function getAnalyzer()
    {
        return $this->analyzer;
    }

    public function factory($uri, ResponseInterface $response)
    {
        $result = new Result([
            'headers' => array_change_key_case($response->getHeaders()),
            'status'  => $response->getStatusCode(),
            'body'    => (string)$response->getBody(),
            'uri'     => $uri,
        ]);

        $this->getAnalyzer()->analyze($result);

        return $result;
    }
}
