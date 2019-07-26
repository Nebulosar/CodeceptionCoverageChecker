<?php

namespace Nebulosar\Codeception\CoverageChecker;

use PHPUnit\Util\Printer;
use SebastianBergmann\CodeCoverage\Node\Directory;

abstract class Checker
{
    public static $hasError = false;
    protected $lowLimit;
    protected $highLimit;
    private static $writers = [];

    abstract protected function calculateCoveragePercentage(Directory $report): float;
    abstract protected function getType(): string;

    public function __construct(?string $lowLimit = '60.00', ?string $highLimit = '80.00')
    {
        $this->lowLimit = (float) number_format($lowLimit, 2, '.', '');
        $this->highLimit = (float) number_format($highLimit, 2, '.', '');
    }

    public function check(Printer $printer, Directory $report): void
    {
        $percentage = $this->calculateCoveragePercentage($report);
        $limit = $this->highLimit;
        if ($percentage < $this->lowLimit) {
            $writer = $this->getWriter(ErrorWriter::class, $printer);
            $limit = $this->lowLimit;
            self::$hasError = true;
        } elseif ($percentage < $this->highLimit) {
            $writer = $this->getWriter(WarningWriter::class, $printer);
        } else {
            $writer = $this->getWriter(SuccessWriter::class, $printer);
        }
        $limit = sprintf('%01.2F%%', $limit);
        $percentage = sprintf('%01.2F%%', $percentage);
        $writer->write($this->getType(), $limit, $percentage);
    }

    protected function calculatePercentage(int $tested, int $total): float
    {
        $percentage = 100;
        if ($total > 0) {
            $percentage = ($tested / $total) * 100;
        } elseif ($total == 0) {
            return $percentage = 0;
        }
        return (float) number_format($percentage, 2, '.', '');
    }

    protected function getWriter(string $type, Printer $printer): Writer
    {
        if (!isset(self::$writers[$type])) {
            self::$writers[$type] = new $type($printer);
        }
        return self::$writers[$type];
    }
}
