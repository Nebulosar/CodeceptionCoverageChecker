<?php

namespace Nebulosar\CodeCeptCodeCov;

use \PHPUnit\Util\Printer;

class CodeCovPrinter
{
    protected const COLORS = [
        'green' => "\x1b[30;42m",
        'yellow' => "\x1b[30;43m",
        'red' => "\x1b[37;41m",
        'reset' => "\x1b[0m",
        'header' => "\x1b[1;37;40m",
    ];
    protected $noColors;

    protected $printer;

    public function __construct(Printer $printer, bool $noColors = false)
    {
        $this->printer = $printer;
        $this->noColors = $noColors;
    }

    final public function write(string $type, string $limit, string $linePercentage, string $severity): void
    {
        $message = PHP_EOL;
        $message .= $this->$severity($type, $limit, $linePercentage);
        $this->printer->write($message);
    }

    final private function formatError(string $type, string $limit, string $linePercentage): string
    {
        $title = ' ERROR: ' . ucfirst($type) . ' coverage lower than threshold ';
        $title = $this->format(self::COLORS['header'], strlen($title), $title);
        $lines = [
            '  ' . 'Threshold: ' . $limit . '%',
            '  ' . 'Coverage: ' . $linePercentage,
        ];
        return $this->formatMessage($title, $lines, self::COLORS['red']);
    }

    final private function formatWarning(string $type, string $limit, string $linePercentage): string
    {
        $title = ' WARNING: ' . ucfirst($type) . ' coverage lower than threshold ';
        $title = $this->format(self::COLORS['header'], strlen($title), $title);
        $lines = [
            '  ' . 'Threshold: ' . $limit . '%',
            '  ' . 'Coverage: ' . $linePercentage,
        ];
        return $this->formatMessage($title, $lines, self::COLORS['yellow']);

    }

    final private function formatSuccess(string $type, string $limit, string $linePercentage): string
    {
        $title = ' SUCCESS: ' . ucfirst($type) . ' coverage higher than threshold ';
        $title = $this->format(self::COLORS['header'], strlen($title), $title);
        $lines = [
            '  ' . 'Threshold: ' . $limit . '%',
            '  ' . 'Coverage: ' . $linePercentage,
        ];
        return $this->formatMessage($title, $lines, self::COLORS['green']);
    }

    final private function formatMessage(string $title, array $lines, string $color): string
    {
        $lineLength = strlen($title . PHP_EOL . PHP_EOL);
        for ($i = 0; $i < count($lines); $i++) {
            $lineLength = $lineLength < strlen($lines[$i]) ? strlen($lines[$i]) : $lineLength;
            $lines[$i] = $this->format($color, $lineLength - strlen($lines[$i]), $lines[$i]);
        }
        return $title . PHP_EOL . implode(PHP_EOL, $lines) . PHP_EOL;
    }

    final private function format(string $color, int $padding, string $string): string
    {
        if ($this->noColors) {
            $color = '';
        } elseif (strpos($color, "\x1b[") !== 0) {
            $color = isset(self::COLORS[$color]) ? self::COLORS[$color] : '';
        }
        $reset = $color ? self::COLORS['reset'] : '';
        return $color . str_pad($string, $padding) . $reset;
    }
}