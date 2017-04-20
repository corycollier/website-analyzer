<?php

namespace WebsiteAnalyzer\Tests\Unit;

use WebsiteAnalyzer\ListBuilder;
use WebsiteAnalyzer\ResultList;

class ListBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $sut = new ListBuilder();
    }

    /**
     * @dataProvider providerProcess
     */
    public function testProcess($uris, $exception = false)
    {
        $sut = $this->getMockBuilder('\WebsiteAnalyzer\ListBuilder')
            ->setMethods(['getFactory', 'getClient', 'setResults'])
            ->getMock();

        $response = $this->getMockBuilder('\GuzzleHttp\Psr7\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getStatusCode', 'getBody', 'getHeaders'])
            ->getMock();

        $factory = $this->getMockBuilder('\WebsiteAnalyzer\ResultFactory')
            ->disableOriginalConstructor()
            ->setMethods(['factory'])
            ->getMock();

        $client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->exactly(count($uris)))
            ->method('request')
            ->will($this->returnValue($response));

        $factory->expects($this->exactly(count($uris)))
            ->method('factory')
            ->will($exception
                ? $this->throwException(new $exception)
                : $this->returnValue(new ResultList([]))
            );

        $sut->expects($this->once())
            ->method('getFactory')
            ->will($this->returnValue($factory));

        $sut->expects($this->once())
            ->method('getClient')
            ->will($this->returnValue($client));

        $sut->expects($this->once())
            ->method('setResults');

        $result = $sut->process($uris);
        $this->assertSame($sut, $result);
    }

    public function providerProcess()
    {

        return [
            'basic test' => [
                'uris'      => [
                    'http://example.com',
                ],
                'exception' => false,
            ],
            'ClientException test' => [
                'uris'      => [
                    'http://example.com',
                ],
                'exception' => '\RuntimeException',
            ],
        ];
        // GuzzleHttp\Exception\ClientException;
        // GuzzleHttp\Exception\ConnectException;
        // GuzzleHttp\Exception\ServerException;
        // GuzzleHttp\Exception\RequestException;
    }

    public function testSetResults()
    {
        $data = new ResultList();
        $sut = new ListBuilder();
        $results = $sut->setResults($data);
        $this->assertSame($sut, $results);
    }

    public function testGetResults()
    {
        $expected = new ResultList();
        $sut = new ListBuilder();
        $result = $sut->getResults();
        $this->assertEquals($expected, $result);
    }

    public function testGetClient()
    {
        $sut = new ListBuilder();
        $result = $sut->getClient();
        $this->assertInstanceOf('\GuzzleHttp\Client', $result);
    }

    public function testGetFactory()
    {
        $sut = new ListBuilder();
        $result = $sut->getFactory();
        $this->assertInstanceOf('\WebsiteAnalyzer\ResultFactory', $result);

    }
}
