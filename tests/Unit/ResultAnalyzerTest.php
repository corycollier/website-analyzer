<?php

namespace WebsiteAnalyzer\Tests\Unit;

use WebsiteAnalyzer\ResultAnalyzer;

class ResultAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $sut = new ResultAnalyzer();
    }

    public function testAnalyze()
    {
        $types = [
            'some-type'
        ];
        $sut = $this->getMockBuilder('\WebsiteAnalyzer\ResultAnalyzer')
            ->disableOriginalConstructor()
            ->setMethods(['getMetricsFactory', 'getMetricTypes'])
            ->getMock();

        $factory = $this->getMockBuilder('\WebsiteAnalyzer\Metrics\MetricsFactory')
            ->disableOriginalConstructor()
            ->setMethods(['factory'])
            ->getMock();

        $metric = $this->getMockBuilder('\WebsiteAnalyzer\Metrics\MetricsInterface')
            ->disableOriginalConstructor()
            ->setMethods(['calculate', 'getType'])
            ->getMock();

        $result = $this->getMockBuilder('\WebsiteAnalyzer\Result')
            ->disableOriginalConstructor()
            ->setMethods(['addMetric'])
            ->getMock();

        $result->expects($this->exactly(count($types)))
            ->method('addMetric')
            ->with($this->equalTo($metric));

        $metric->expects($this->exactly(count($types)))
            ->method('calculate')
            ->with($this->equalTo($result));

        $factory->expects($this->exactly(count($types)))
            ->method('factory')
            // ->with() // return value map?
            ->will($this->returnValue($metric));

        $sut->expects($this->once())
            ->method('getMetricsFactory')
            ->will($this->returnValue($factory));

        $sut->expects($this->once())
            ->method('getMetricTypes')
            ->will($this->returnValue($types));

        $result = $sut->analyze($result);
        $this->assertSame($sut, $result);
    }

    public function testGetMetricTypes()
    {
        $expected = [
            'technology-stack',
            'css-complexity',
            'whois-data',
            'dns-data',
        ];

        $sut = new ResultAnalyzer();
        $result = $sut->getMetricTypes();
        $this->assertEquals($expected, $result);
    }

    public function testGetMetricsFactory()
    {
        $sut = new ResultAnalyzer();
        $result = $sut->getMetricsFactory();
        $this->assertInstanceOf('\WebsiteAnalyzer\Metrics\MetricsFactory', $result);
    }
}
