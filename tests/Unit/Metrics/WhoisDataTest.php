<?php

namespace WebsiteAnalyzer\Tests\Unit\Metrics;

use WebsiteAnalyzer\Metrics\WhoisData;

class WhoisDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerGetDomainName
     */
    public function testGetDomainName($expected, $uri)
    {
        $subject = new WhoisData();
        $model = $this->getMockBuilder('\WebsiteAnalyzer\Result')
            ->disableOriginalConstructor()
            ->setMethods(['getUri'])
            ->getMock();

        $model->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($uri));

        $result = $subject->getDomainName($model);
        $this->assertEquals($expected, $result);
    }

    public function providerGetDomainName()
    {
        return [
            ['test.com',        'http://test.com'],
            ['www.test.com',    'https://www.test.com/go-for-gold/'],
            ['trials.test.com', 'https://trials.test.com'],
            ['www.test.com',    'https://www.test.com/scheduling#/region/central-florida/fh-imaging'],
            ['test.com',        'TeSt.com'],
            ['www.test.org',    'http://www.test.org/landing/specialists/?adsource=test.org']
        ];
    }
}
