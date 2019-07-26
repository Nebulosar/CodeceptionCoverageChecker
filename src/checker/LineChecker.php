<?php

namespace Nebulosar\Codeception\CoverageChecker;

use SebastianBergmann\CodeCoverage\Node\Directory;

class LineChecker extends Checker
{

    public function calculateCoveragePercentage(Directory $report): float
    {
        return $this->calculatePercentage(
            $report->getNumExecutedLines(),
            $report->getNumExecutableLines()
        );
    }

    protected function getType(): string
    {
        return 'line';
    }
}
