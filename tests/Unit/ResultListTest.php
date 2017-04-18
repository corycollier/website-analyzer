<?php

namespace WebsiteAnalyzer\Tests\Unit;

use WebsiteAnalyzer\ResultList;

class ResultListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerGetMetrics
     */
    public function testGetMetrics($type)
    {
        $metric = $this->getMockBuilder('\WebsiteAnalyzer\Metrics\MetricsInterface')
            ->disableOriginalConstructor()
            ->setMethods(['calculate', 'getType'])
            ->getMock();

        $result = $this->getMockBuilder('\WebsiteAnalyzer\Result')
            ->disableOriginalConstructor()
            ->setMethods(['getMetric'])
            ->getMock();

        $result->expects($this->any())
            ->method('getMetric')
            ->with($this->equalTo($type))
            ->will($this->returnValue($metric));

        $sut = new ResultList([
            'key' => $result,
        ]);

        $result = $sut->getMetrics($type);
    }

    public function providerGetMetrics()
    {
        return [
            'simple test' => [
                'type' => 'type value',
            ],
        ];
    }
}
