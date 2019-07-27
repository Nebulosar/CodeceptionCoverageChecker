<?php


namespace Nebulosar\Codeception\CoverageChecker;

class WarningWriter extends Writer
{
    private $color = self::COLORS['yellow'];

    public function write(string $type, string $limit, string $linePercentage): void
    {
        $title = ' WARNING: ' . ucfirst($type) . ' coverage lower than threshold ';
        $title = $this->formatLine($this->color, strlen($title), $title);
        $lines = [
            '  ' . 'Threshold: ' . $limit,
            '  ' . 'Coverage: ' . $linePercentage,
        ];
        $this->output($this->formatMessage($title, $lines, self::COLORS['reset']));
    }
}
