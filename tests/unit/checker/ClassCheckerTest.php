<?php

namespace Tests\Nebulosar\Unit\Checker;

use Exception;
use Nebulosar\Codeception\CoverageChecker\ClassChecker;

class ClassCheckerTest extends CheckerTest
{
    /**
     * @var string
     */
    protected $checkerClass = ClassChecker::class;

    /**
     * @throws Exception
     */
    public function testCalculateCoveragePercentage(): void
    {
        $coverage = 40;
        $report = $this->tester->makeDirectory($coverage);
        $percentage = $this->tester->callMethod($this->checker, 'calculateCoveragePercentage', [$report]);
        $this->assertEquals($coverage, $percentage, 'Percentage of classes tested should be correctly calculated');
    }
}
