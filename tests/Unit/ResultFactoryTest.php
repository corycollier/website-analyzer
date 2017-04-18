<?php

namespace WebsiteAnalyzer\Tests\Unit;

use WebsiteAnalyzer\ResultFactory;

class ResultFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $sut = new ResultFactory();
    }

    public function testGetAnalyzer()
    {
        $sut = new ResultFactory();
        $result = $sut->getAnalyzer();
        $this->assertInstanceOf('\WebsiteAnalyzer\ResultAnalyzer', $result);
    }

    /**
     * @dataProvider providerFactory
     */
    public function testFactory($uri, $status, $body, $headers)
    {
        $sut = $this->getMockBuilder('\WebsiteAnalyzer\ResultFactory')
            ->disableOriginalConstructor()
            ->setMethods(['getAnalyzer'])
            ->getMock();

        $response = $this->getMockBuilder('\GuzzleHttp\Psr7\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getStatusCode', 'getBody', 'getHeaders'])
            ->getMock();

        $analyzer = $this->getMockBuilder('\WebsiteAnalyzer\ResultAnalyzer')
            ->disableOriginalConstructor()
            ->setMethods(['analyze'])
            ->getMock();

        $analyzer->expects($this->once())->method('analyze');

        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($body));

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue($status));

        $response->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue($headers));

        $sut->expects($this->once())
            ->method('getAnalyzer')
            ->will($this->returnValue($analyzer));

        $result = $sut->factory($uri, $response);
        $this->assertInstanceOf('\WebsiteAnalyzer\Result', $result);
    }

    public function providerFactory()
    {
        return [
            'basic test' => [
                'uri'     => '',
                'status'  => 200,
                'body'    => 'asdf',
                'headers' => [],
            ],
        ];
    }
}
