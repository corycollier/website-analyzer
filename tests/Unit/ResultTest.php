<?php

namespace WebsiteAnalyzer\Tests\Unit;

use WebsiteAnalyzer\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $data = [];
        $sut = new Result($data);
    }

    public function testGetMetrics()
    {
        $expected = 'expected value';
        $data = [
            'metrics' => $expected,
        ];
        $sut = new Result($data);
        $result = $sut->getMetrics();
        $this->assertEquals($expected, $result);
    }

    public function testGetUri()
    {
        $expected = 'expected value';
        $data = [
            'uri' => $expected,
        ];
        $sut = new Result($data);
        $result = $sut->getUri();
        $this->assertEquals($expected, $result);
    }

    public function testGetBody()
    {
        $expected = 'expected value';
        $data = [
            'body' => $expected,
        ];
        $sut = new Result($data);
        $result = $sut->getBody();
        $this->assertEquals($expected, $result);
    }

    public function testGetHeaders()
    {
        $expected = 'expected value';
        $data = [
            'headers' => $expected,
        ];
        $sut = new Result($data);
        $result = $sut->getHeaders();
        $this->assertEquals($expected, $result);
    }

    public function testGetStatus()
    {
        $expected = 'expected value';
        $data = [
            'status' => $expected,
        ];
        $sut = new Result($data);
        $result = $sut->getStatus();
        $this->assertEquals($expected, $result);
    }

    public function testAddMetric()
    {
        $sut = new Result([]);
        $type = 'metric-type';
        $metric = $this->getMockBuilder('\WebsiteAnalyzer\Metrics\MetricsInterface')
            ->disableOriginalConstructor()
            ->setMethods(['calculate', 'getType'])
            ->getMock();

        $metric->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($type));

        $result = $sut->addMetric($metric);
        $this->assertSame($sut, $result);

        $metrics = $sut->getMetrics();
        $this->assertTrue(array_key_exists($type, $metrics));
    }

    public function test__debugInfo()
    {
        $expected = [
            'uri'     => '',
            'body'    => false,
            'headers' => [],
            'status'  => '',
            'metrics' => '',
        ];

        $sut = new Result([]);
        $result = $sut->__debugInfo();
        $this->assertEquals($expected, $result);

    }

}
