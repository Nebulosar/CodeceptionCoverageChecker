<?php


namespace Nebulosar\Codeception\CoverageChecker;

class SuccessWriter extends Writer
{
    private $color = self::COLORS['green'];

    public function write(string $type, string $limit, string $linePercentage): void
    {
        $title = ' SUCCESS: ' . ucfirst($type) . ' coverage higher than threshold ';
        $title = $this->formatLine($this->color, strlen($title), $title);
        $lines = [
            '  ' . 'Threshold: ' . $limit,
            '  ' . 'Coverage: ' . $linePercentage,
        ];
        $this->output($this->formatMessage($title, $lines, self::COLORS['reset']));
    }
}
