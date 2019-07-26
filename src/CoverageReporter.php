<?php

namespace Nebulosar\Codeception\CoverageChecker;

use Codeception\Configuration;
use Codeception\Event\PrintResultEvent;
use Codeception\Events;
use Codeception\Exception\ConfigurationException;
use Codeception\Platform\Extension;
use PHPUnit\Framework\CodeCoverageException;

class CoverageReporter extends Extension
{
    public static $events = [
        Events::RESULT_PRINT_AFTER => 'checkCoverage'
    ];
    private $_enabled = true;
    private $_checkers = [];

    /**
     * CoverageChecker constructor.
     * @param array $config - Configuration from codeception.yml file
     * @param array $options - console parameters
     * @throws ConfigurationException
     */
    public function __construct(array $config, array $options)
    {
        $config = array_merge(Configuration::config(), $config);
        $this->_enabled =
            isset($config['coverage']) &&
            isset($config['coverage']['enabled']) &&
            $config['coverage']['enabled'] == true &&
            isset($config['coverage']['check']) &&
            (
                (isset($options['coverage']) && $options['coverage'] !== false) ||
                (isset($options['coverage-xml']) && $options['coverage-xml'] !== false) ||
                (isset($options['coverage-html']) && $options['coverage-html'] !== false) ||
                (isset($options['coverage-text']) && $options['coverage-text'] !== false) ||
                (isset($options['coverage-crap4j']) && $options['coverage-crap4j'] !== false) ||
                (isset($options['coverage-phpunit']) && $options['coverage-phpunit'] !== false)
            );
        if ($this->_enabled) {
            $this->init($config['coverage']);
        }
        parent::__construct($config, $options);
    }

    /**
     * Initializes all the checkers
     * @param array $config - The config of the coverage part of codeception
     */
    protected function init(array $config): void
    {
        foreach ($config['check'] as $checkType => $limits) {
            $lowLimit = isset($limits['low_limit']) ? number_format($limits['low_limit'], 2, '.', '') : null;
            $highLimit = isset($limits['low_limit']) ? number_format($limits['high_limit'], 2, '.', '') : null;
            switch (strtolower($checkType)) {
                case 'classes':
                    $this->_checkers[] = new ClassChecker($lowLimit, $highLimit);
                    break;
                case 'methods':
                    $this->_checkers[] = new MethodChecker($lowLimit, $highLimit);
                    break;
                case 'lines':
                    $this->_checkers[] = new LineChecker($lowLimit, $highLimit);
                    break;
            }
        }
    }

    /**
     * Does the checks on the different type of coverage defined in the codeception.yml
     * @param PrintResultEvent $event - The event that holds the printer and the test results
     */
    public function checkCoverage(PrintResultEvent $event): void
    {
        $codeCoverage = $event->getResult()->getCodeCoverage();
        $printer = $event->getPrinter();
        if ($this->_enabled && !empty($codeCoverage)) {
            $report = $codeCoverage->getReport();
            foreach ($this->_checkers as $checker) {
                $checker->check($printer, $report);
            }
            if (Checker::$hasError) {
                throw new CodeCoverageException();
            }
        }
    }
}
