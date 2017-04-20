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
            ->setMethods(['calculate', 'getType', 'getData'])
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

    /**
     * @dataProvider providerGetMetricSet
     */
    public function testGetMetricSet($expected, $metrics, $type, $path)
    {
        $sut = $this->getMockBuilder('\WebsiteAnalyzer\ResultList')
            ->disableOriginalConstructor()
            ->setMethods(['getMetrics'])
            ->getMock();

        $metric = $this->getMockBuilder('\WebsiteAnalyzer\Metrics\MetricsInterface')
            ->disableOriginalConstructor()
            ->setMethods(['calculate', 'getType', 'getData'])
            ->getMock();

        $valueMap = [];
        foreach ($metrics as $data) {
            $valueMap[] = [$data];
        }

        $metric->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap($valueMap));

        $sut->expects($this->once())
            ->method('getMetrics')
            ->with($type)
            ->will($this->returnValue([$metric]));

        $result = $sut->getMetricSet($path);
        $this->assertEquals($expected, $result);

    }

    public function providerGetMetricSet()
    {
        $metrics = [
            'test.com' => [
                'some' => [
                    'key' => [
                        'has' => [
                            'a value',
                        ]
                    ]
                ],
                'numbers' => [
                    'value 0',
                    'value 1',
                    'value 2',
                ]
            ]
        ];

        // @todo figure out what's wrong with the expected values here. this sucks.
        return [
            'simple test' => [
                'expected' => [['a value']],
                'metrics' => $metrics,
                'type' => 'the-metric',
                'path' => 'the-metric.some.key.has',
            ],
            'number test' => [
                'expected' => [[
                    'value 0',
                    'value 1',
                    'value 2',
                ]],
                'metrics' => $metrics,
                'type' => 'the-metric',
                'path' => 'the-metric.numbers',
            ],
            'number 1 test' => [
                'expected' => ['value 1'],
                'metrics' => $metrics,
                'type' => 'the-metric',
                'path' => 'the-metric.numbers.1',
            ],
        ];
    }
}
