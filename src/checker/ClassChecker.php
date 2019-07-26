<?php

namespace Nebulosar\Codeception\CoverageChecker;

use SebastianBergmann\CodeCoverage\Node\Directory;

class ClassChecker extends Checker
{

    public function calculateCoveragePercentage(Directory $report): float
    {
        return $this->calculatePercentage(
            $report->getNumTestedClasses(),
            $report->getNumClasses()
        );
    }

    protected function getType(): string
    {
        return 'class';
    }
}
