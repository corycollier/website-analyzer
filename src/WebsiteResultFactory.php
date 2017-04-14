<?php

namespace WebsiteAnalyzer;

use Psr\Http\Message\ResponseInterface;

class WebsiteResultFactory
{
    public function factory(ResponseInterface $response)
    {
        $result = new WebsiteResult([
            'headers' => $response->getHeaders(),
            'status' => $response->getStatusCode(),
            'body' => (string)$response->getBody(),
        ]);

        $result->analyze();

        return $result;

    }
}
