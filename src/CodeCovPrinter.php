<?php
namespace Nebulosar\CodeCeptCodeCov;

use \PHPUnit\Util\Printer;

class CodeCovPrinter
{
    protected $colors = [
        'green' => "\x1b[30;42m",
        'yellow' => "\x1b[30;43m",
        'red' => "\x1b[37;41m",
        'reset' => "\x1b[0m",
    ];

    protected $printer;

    public function __construct(Printer $printer)
    {
        $this->printer = $printer;
    }

    public function write(string $message): void {
        $this->printer->write($message);
    }

    public function formatError($type, $linePercentage)
    {
        $type = ucfirst($type);
        $output = PHP_EOL . PHP_EOL;
        $lineOutput = 'ERROR: ' . PHP_EOL
            . $type . ' coverage lower than low threshold' . PHP_EOL
            . 'Threshold: ' . $this->_settings['low_limit'] . '%' . PHP_EOL
            . 'Coverage: ' . $linePercentage;
        $padding = strlen($lineOutput);
        $output .= $this->format($this->colors['red'], $padding, $lineOutput);
        return $output . PHP_EOL;
    }

    public function formatWarning($type, $linePercentage)
    {
        $type = ucfirst($type);
        $output = PHP_EOL . PHP_EOL;
        $lineOutput = 'WARNING: ' . PHP_EOL
            . $type . ' coverage lower than high threshold' . PHP_EOL
            . 'Threshold: ' . $this->_settings['high_limit'] . '%' . PHP_EOL
            . 'Coverage: ' . $linePercentage;
        $padding = strlen($lineOutput);
        $output .= $this->format($this->colors['yellow'], $padding, $lineOutput);
        return $output . PHP_EOL;
    }

    public function formatSuccess($type, $linePercentage): string
    {
        $type = ucfirst($type);
        $output = PHP_EOL . PHP_EOL;
        $lineOutput = 'SUCCESS: ' . PHP_EOL
            . $type . ' coverage higher than high threshold' . PHP_EOL
            . 'Threshold: ' . $this->_settings['high_limit'] . '%' . PHP_EOL
            . 'Coverage: ' . $linePercentage;
        $padding = strlen($lineOutput);
        $output .= $this->format($this->colors['green'], $padding, $lineOutput);
        return $output . PHP_EOL;
    }

    public function format($color, $padding, $string)
    {
        $reset = $color ? $this->colors['reset'] : '';
        return $color . str_pad($string, $padding) . $reset . PHP_EOL;
    }

}