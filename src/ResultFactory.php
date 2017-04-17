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

    public function factory(ResponseInterface $response)
    {
        $result = new Result([
            'headers' => $response->getHeaders(),
            'status'  => $response->getStatusCode(),
            'body'    => (string)$response->getBody(),
        ]);

        $this->getAnalyzer()->analyze($result);

        return $result;
    }
}
