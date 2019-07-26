<?php

namespace Tests\Nebulosar\Unit\Checker;

use Exception;
use Nebulosar\Codeception\CoverageChecker\MethodChecker;

class MethodCheckerTest extends CheckerTest
{
    /**
     * @var string
     */
    protected $checkerClass = MethodChecker::class;

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
