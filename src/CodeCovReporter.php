<?php

namespace Nebulosar\CodeCeptCodeCov;

use Codeception\Configuration;
use Codeception\Event\PrintResultEvent;
use Codeception\Events;
use PHPUnit\Framework\CodeCoverageException;
use SebastianBergmann\CodeCoverage\Node\Directory;

class CodeCovReporter extends \Codeception\Platform\Extension
{
    public static $events = [
        Events::RESULT_PRINT_AFTER => 'checkCoverage'
    ];

    protected $_settings = [
        'enabled' => true,
        'low_limit' => '50',
        'high_limit' => '80',
        'check_for' => ['lines']
    ];

    public function __construct($config, $options)
    {
        $config = array_merge($config, Configuration::config());
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
            );
        if ($this->_settings['enabled']) {
            $this->_settings = array_merge($this->_settings, Configuration::config()['coverage']);
        }
        parent::__construct($config, $options);
    }

    public function checkCoverage(PrintResultEvent $event)
    {
        $codeCoverage = $event->getResult()->getCodeCoverage();
        $printer = new CodeCovPrinter($event->getPrinter());
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
                throw new CodeCoverageException();
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
}
