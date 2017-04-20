<?php

namespace WebsiteAnalyzer\Metrics;

use WebsiteAnalyzer\Result;

interface MetricsInterface
{
    public function calculate(Result $result);

    public function getType();

    public function getData();
}
