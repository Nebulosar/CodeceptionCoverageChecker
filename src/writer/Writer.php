<?php

namespace Nebulosar\Codeception\CoverageChecker;

use PHPUnit\Util\Printer;

abstract class Writer
{
    protected const COLORS = [
        'green' => "\x1b[30;42m",
        'yellow' => "\x1b[30;43m",
        'red' => "\x1b[37;41m",
        'reset' => "\x1b[0m",
        'header' => "\x1b[1;37;40m",
    ];
    /**
     * @var bool
     */
    public static $noColors = false;

    protected $printer;

    public function __construct(Printer $printer)
    {
        $this->printer = $printer;
    }

    abstract public function write(string $type, string $limit, string $linePercentage): void;

    protected function output(string $message): void
    {
        $this->printer->write($message . PHP_EOL);
    }

    protected function formatMessage(string $title, array $lines, string $color): string
    {
        $lineLength = strlen($title . PHP_EOL . PHP_EOL);
        for ($i = 0; $i < count($lines); $i++) {
            $lineLength = $lineLength < strlen($lines[$i]) ? strlen($lines[$i]) : $lineLength;
            $padding = $lineLength - strlen($lines[$i]);
            $padding = $i === count($lines) -1 ? --$padding : $padding;
            $lines[$i] = $this->formatLine($color, $padding, $lines[$i]);
        }
        return $title . PHP_EOL . implode(PHP_EOL, $lines) . PHP_EOL;
    }

    protected function formatLine(string $color, int $padding, string $string): string
    {
        if (self::$noColors) {
            $color = '';
        } elseif (strpos($color, "\x1b[") !== 0) {
            $color = isset(self::COLORS[$color]) ? self::COLORS[$color] : '';
        }
        $reset = $color ? self::COLORS['reset'] : '';
        return $color . str_pad($string, $padding) . $reset;
    }
}
