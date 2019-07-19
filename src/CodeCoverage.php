<?php

use Codeception\Configuration;
use Codeception\Event\PrintResultEvent;
use Codeception\Events;
use PHPUnit\Framework\Exception;
use SebastianBergmann\CodeCoverage\Node\Directory;

class CodeCoverage extends \Codeception\Platform\Extension
{
    public static $events = [
        Events::RESULT_PRINT_AFTER => 'checkCoverage'
    ];
    private $_colors = [
        'green' => "\x1b[30;42m",
        'yellow' => "\x1b[30;43m",
        'red' => "\x1b[37;41m",
        'reset' => "\x1b[0m",
    ];
    protected $_settings = [
        'enabled' => true,
        'low_limit' => '50',
        'high_limit' => '80',
        'check_for' => ['lines']
    ];

    public function __construct($config, $options)
    {
        $config = Configuration::config();
        $this->_settings['enabled'] =
            isset($config['coverage']) &&
            isset($config['coverage']['enabled']) &&
            $config['coverage']['enabled'] == true &&
            (
                $options['coverage'] !== false ||
                $options['coverage-xml'] !== false ||
                $options['coverage-html'] !== false ||
                $options['coverage-text'] !== false ||
                $options['coverage-crap4j'] !== false ||
                $options['coverage-phpunit'] !== false
            )
        ;
        if ($this->_settings['enabled']) {
            $this->_settings = array_merge($this->_settings, Configuration::config()['coverage']);
        }
        parent::__construct($config, $options);
    }

    public function checkCoverage(PrintResultEvent $event)
    {
        $codeCoverage = $event->getResult()->getCodeCoverage();
        $printer = $event->getPrinter();
        if ($this->_settings['enabled'] && !empty($codeCoverage)) {
            $percentages = $this->calculatePercentages($codeCoverage->getReport());
            $hasError = false;
            foreach ($percentages as $type => $percentage) {
                if ($percentage['int'] < $this->_settings['low_limit']) {
                    $printer->write($this->formatError($type, $percentage['string']));
                    $hasError = true;
                } elseif ($percentage['int'] < $this->_settings['high_limit']) {
                    $printer->write($this->formatWarning($type, $percentage['string']));
                } else {
                    $printer->write($this->formatSuccess($type, $percentage['string']));
                }
            }
            if ($hasError) {
                throw new Exception();
            }
        }
    }

    private function percentage($a, $b, $asString = false, $fixedWidth = false)
    {
        if ($asString && $b == 0) {
            return '';
        }

        $percentage = 100;
        if ($b > 0) {
            $percentage = ($a / $b) * 100;
        }

        if ($asString) {
            $format = $fixedWidth ? '%6.2F%%' : '%01.2F%%';
            return sprintf($format, $percentage);
        }

        return $percentage;
    }

    private function calculatePercentages(Directory $report)
    {
        $percentages = [];
        foreach ($this->_settings['check_for'] as $checkType) {
            switch (strtolower($checkType)) {
                case 'classes':
                case 'class':
                    $percentages['class'] = [
                        'int' => $this->percentage(
                            $report->getNumTestedClasses(),
                            $report->getNumClasses()
                        ),
                        'string' => $this->percentage(
                            $report->getNumTestedClasses(),
                            $report->getNumClasses(),
                            true
                        )
                    ];
                    break;
                case 'methods':
                case 'method':
                    $percentages['method'] = [
                        'int' => $this->percentage(
                            $report->getNumTestedMethods(),
                            $report->getNumMethods()
                        ),
                        'string' => $this->percentage(
                            $report->getNumTestedMethods(),
                            $report->getNumMethods(),
                            true
                        )
                    ];
                    break;
                case 'lines':
                case 'line':
                    $percentages['line'] = [
                        'int' => $this->percentage(
                            $report->getNumExecutedLines(),
                            $report->getNumExecutableLines()
                        ),
                        'string' => $this->percentage(
                            $report->getNumExecutedLines(),
                            $report->getNumExecutableLines(),
                            true
                        )
                    ];
                    break;
            }
        }
        return $percentages;
    }

    private function formatError($type, $linePercentage)
    {
        $type = ucfirst($type);
        $output = PHP_EOL . PHP_EOL;
        $lineOutput = 'ERROR: ' . PHP_EOL
            . $type . ' coverage lower than low threshold' . PHP_EOL
            . 'Threshold: ' . $this->_settings['low_limit'] . '%' . PHP_EOL
            . 'Coverage: ' . $linePercentage;
        $padding = strlen($lineOutput);
        $output .= $this->format($this->_colors['red'], $padding, $lineOutput);
        return $output . PHP_EOL;
    }

    private function formatWarning($type, $linePercentage)
    {
        $type = ucfirst($type);
        $output = PHP_EOL . PHP_EOL;
        $lineOutput = 'WARNING: ' . PHP_EOL
            . $type . ' coverage lower than high threshold' . PHP_EOL
            . 'Threshold: ' . $this->_settings['high_limit'] . '%' . PHP_EOL
            . 'Coverage: ' . $linePercentage;
        $padding = strlen($lineOutput);
        $output .= $this->format($this->_colors['yellow'], $padding, $lineOutput);
        return $output . PHP_EOL;
    }

    private function formatSuccess($type, $linePercentage)
    {
        $type = ucfirst($type);
        $output = PHP_EOL . PHP_EOL;
        $lineOutput = 'SUCCESS: ' . PHP_EOL
            . $type . ' coverage higher than high threshold' . PHP_EOL
            . 'Threshold: ' . $this->_settings['high_limit'] . '%' . PHP_EOL
            . 'Coverage: ' . $linePercentage;
        $padding = strlen($lineOutput);
        $output .= $this->format($this->_colors['green'], $padding, $lineOutput);
        return $output . PHP_EOL;
    }

    private function format($color, $padding, $string)
    {
        $reset = $color ? $this->_colors['reset'] : '';
        return $color . str_pad($string, $padding) . $reset . PHP_EOL;
    }
}
