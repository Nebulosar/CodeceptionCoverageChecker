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

    protected $settings = [
        'enabled' => true,
        'low_limit' => '50',
        'high_limit' => '80',
        'check_for' => ['lines']
    ];

    public function __construct($config, $options)
    {
        $config = array_merge($config, Configuration::config());
        $this->settings['enabled'] =
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
        if ($this->settings['enabled']) {
            $this->settings = array_merge($this->settings, Configuration::config()['coverage']);

            $this->settings['low_limit'] = number_format((float)$this->settings['low_limit'], 2, '.', '');
            $this->settings['high_limit'] = number_format((float)$this->settings['high_limit'], 2, '.', '');
        }
        parent::__construct($config, $options);
    }

    public function checkCoverage(PrintResultEvent $event)
    {
        $codeCoverage = $event->getResult()->getCodeCoverage();
        $printer = new CodeCovPrinter($event->getPrinter());
        if ($this->settings['enabled'] && !empty($codeCoverage)) {
            $percentages = $this->calculatePercentages($codeCoverage->getReport());
            $hasError = false;
            foreach ($percentages as $type => $percentage) {
                if ($percentage['int'] < $this->settings['low_limit']) {
                    $limit = $this->settings['low_limit'];
                    $severity = Severity::ERROR;
                    $hasError = true;
                } elseif ($percentage['int'] < $this->settings['high_limit']) {
                    $limit = $this->settings['low_limit'];
                    $severity = Severity::WARNING;
                } else {
                    $limit = $this->settings['high_limit'];
                    $severity = Severity::SUCCESS;
                }
                $printer->write($type, $limit, $percentage['string'], $severity);
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

        return number_format($percentage, 2, '.', '');
    }

    private function calculatePercentages(Directory $report)
    {
        $percentages = [];
        foreach ($this->settings['check_for'] as $checkType) {
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
