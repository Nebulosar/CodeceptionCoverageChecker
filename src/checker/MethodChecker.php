<?php

namespace Nebulosar\Codeception\CoverageChecker;

use SebastianBergmann\CodeCoverage\Node\Directory;

class MethodChecker extends Checker
{
    public function calculateCoveragePercentage(Directory $report): float
    {
        return $this->calculatePercentage(
            $report->getNumTestedMethods(),
            $report->getNumMethods()
        );
    }

    protected function getType(): string
    {
        return 'method';
    }
}
