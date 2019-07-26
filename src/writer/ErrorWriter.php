<?php

namespace Nebulosar\Codeception\CoverageChecker;


class ErrorWriter extends Writer
{
    private $color = self::COLORS['red'];

    public function write(string $type, string $limit, string $linePercentage): void
    {
        $title = ' ERROR: ' . ucfirst($type) . ' coverage lower than threshold ';
        $title = $this->formatLine(self::COLORS['header'], strlen($title), $title);
        $lines = [
            '  ' . 'Threshold: ' . $limit,
            '  ' . 'Coverage: ' . $linePercentage,
        ];
        $this->output($this->formatMessage($title, $lines, $this->color));
    }
}