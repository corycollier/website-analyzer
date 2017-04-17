<?php

namespace WebsiteAnalyzer\Metrics;

use WebsiteAnalyzer\Result;

interface MetricsInterface
{
    public function calculate(Result $result);

    public function report();

    public function getType();
}
