<?php

namespace Tests\Nebulosar\Unit\Checker;

use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use Exception;
use Nebulosar\Codeception\CoverageChecker\Checker;
use Nebulosar\Codeception\CoverageChecker\ErrorWriter;
use Nebulosar\Codeception\CoverageChecker\SuccessWriter;
use Nebulosar\Codeception\CoverageChecker\WarningWriter;
use PHPUnit\Util\Printer;
use UnitTester;

class CheckerTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;
    /**
     * @var string
     */
    protected $checkerClass = null;
    /**
     * @var Checker
     */
    protected $checker = null;

    /**
     * @var Printer (Stubbed)
     */
    private $_mockPrinter = null;

    /**
     * @throws Exception
     */
    public function _before(): void
    {
        if (!isset($this->checkerClass)) {
            if (get_class($this) !== 'Tests\Nebulosar\Unit\Checker\CheckerTest') {
                $this->fail('Extend of CheckerTest should have set the $checkerClass variable!');
            } else {
                $this->markTestSkipped('CheckerTest does not need to be tested by itself.');
            }
        }
        $this->checker = new $this->checkerClass();
        $this->_mockPrinter = $this->makeEmpty(Printer::class);
        parent::_before();
    }

    public function testGetWriter(): void
    {
        $writer = $this->tester->callMethod($this->checker, 'getWriter', [ErrorWriter::class, $this->_mockPrinter]);
        $this->assertInstanceOf(ErrorWriter::class, $writer, 'Writer should be instance of ErrorWriter');
    }

    public function testCalculatePercentageTotalZero(): void
    {
        $percentage = $this->tester->callMethod($this->checker, 'calculatePercentage', [50, 0]);
        $this->assertEquals(0, $percentage, 'With 0 as total, percentage must be 0 too');
    }

    public function testCalculatePercentageTotalLessThanZero(): void
    {
        $percentage = $this->tester->callMethod($this->checker, 'calculatePercentage', [50, -50]);
        $this->assertEquals(100, $percentage, 'With less then 0 as total, percentage must be 100');
    }

    public function testCalculatePercentageTotalGreaterThanZero(): void
    {
        $percentage = $this->tester->callMethod($this->checker, 'calculatePercentage', [50, 100]);
        $this->assertEquals(50, $percentage, 'With greater then 0 as total, percentage must be calculated correctly');
    }

    /**
     * @throws Exception
     */
    public function testCheckErrorWriting(): void
    {
        $limit = 60;
        $coverage = 10;
        $type = $this->tester->callMethod($this->checker, 'getType');
        $writer = $this->makeEmpty(ErrorWriter::class, [
            'write' => Expected::once([
                $type,
                sprintf('%01.2F%%', $limit),
                sprintf('%01.2F%%', $coverage)
            ]),
        ]);
        $checker = $this->make(get_class($this->checker), [
            'calculateCoveragePercentage' => $coverage,
            'lowLimit' => $limit,
            'getWriter' => $writer,
        ]);

        $report = $this->tester->makeDirectory($coverage);
        $checker->check($this->_mockPrinter, $report);
        $this->assertTrue(Checker::$hasError);
        Checker::$hasError = false;
    }

    /**
     * @throws Exception
     */
    public function testCheckWarningWriting(): void
    {
        $lowLimit = 60;
        $highLimit = 80;
        $coverage = 70;
        $type = $this->tester->callMethod($this->checker, 'getType');
        $writer = $this->makeEmpty(WarningWriter::class, [
            'write' => Expected::once([
                $type,
                sprintf('%01.2F%%', $lowLimit),
                sprintf('%01.2F%%', $coverage)
            ]),
        ]);
        $checker = $this->make(get_class($this->checker), [
            'calculateCoveragePercentage' => $coverage,
            'lowLimit' => $lowLimit,
            'highLimit' => $highLimit,
            'getWriter' => $writer,
        ]);

        $report = $this->tester->makeDirectory($coverage);
        $checker->check($this->_mockPrinter, $report);
        $this->assertFalse(Checker::$hasError);
    }

    /**
     * @throws Exception
     */
    public function testCheckSuccessWriting(): void
    {
        $lowLimit = 60;
        $highLimit = 80;
        $coverage = 90;
        $type = $this->tester->callMethod($this->checker, 'getType');
        $writer = $this->makeEmpty(SuccessWriter::class, [
            'write' => Expected::once([
                $type,
                sprintf('%01.2F%%', $highLimit),
                sprintf('%01.2F%%', $coverage)
            ]),
        ]);
        $checker = $this->make(get_class($this->checker), [
            'calculateCoveragePercentage' => $coverage,
            'lowLimit' => $lowLimit,
            'highLimit' => $highLimit,
            'getWriter' => $writer,
        ]);

        $report = $this->tester->makeDirectory($coverage);
        $checker->check($this->_mockPrinter, $report);
        $this->assertFalse(Checker::$hasError);
    }
}
