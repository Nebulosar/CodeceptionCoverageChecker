<?php

namespace Tests\Nebulosar\Unit;

use Codeception\Event\PrintResultEvent;
use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use Exception;
use Nebulosar\Codeception\CoverageChecker;
use PHPUnit\Framework\CodeCoverageException;
use PHPUnit\Framework\TestResult;
use PHPUnit\Util\Printer;
use SebastianBergmann\CodeCoverage\CodeCoverage;

class CodeCovReporterTest extends Unit
{

    /**
     * @throws Exception
     */
    public function testConstructEnabled(): void
    {
        $config = [
            'coverage' => [
                'enabled' => true,
                'check' => true,
            ]
        ];
        $options = [
            'coverage' => true
        ];
        $this->makeEmptyExcept(CoverageChecker::class, '__construct', [
            'init' => Expected::once($config['coverage'])
        ])->__construct($config, $options);
    }

    /**
     * @throws Exception
     */
    public function testConstructEnabledOtherOptions(): void
    {
        $config = [
            'coverage' => [
                'enabled' => true,
                'check' => true,
            ]
        ];
        $reporter = $this->makeEmptyExcept(CoverageChecker::class, '__construct', [
            'init' => Expected::exactly(5, $config['coverage'])
        ]);
        $reporter->__construct($config, ['coverage-xml' => true]);
        $reporter->__construct($config, ['coverage-html' => true]);
        $reporter->__construct($config, ['coverage-text' => true]);
        $reporter->__construct($config, ['coverage-crap4j' => true]);
        $reporter->__construct($config, ['coverage-phpunit' => true]);
    }

    /**
     * @throws Exception
     */
    public function testConstructDisabled(): void
    {
        $config = [
            'coverage' => [
                'enabled' => false,
            ]
        ];
        $this->makeEmptyExcept(CoverageChecker::class, '__construct', [
            'init' => Expected::never()
        ])->__construct($config, []);

        $config = [
            'coverage' => [
                'enabled' => true,
            ]
        ];
        $this->makeEmptyExcept(CoverageChecker::class, '__construct', [
            'init' => Expected::never()
        ])->__construct($config, []);
    }

    /**
     * @throws Exception
     */
    public function testCheckCoverage(): void
    {
        $coverage = new CodeCoverage();
        $result = new TestResult();
        $result->setCodeCoverage($coverage);
        $event = $this->make(PrintResultEvent::class, [
            'getResult' => $result,
            'getPrinter' => $this->makeEmpty(Printer::class),
        ]);
        $config = [
            'coverage' => [
                'enabled' => true,
                'check' => [
                    'classes' => [
                        'low_limit' => 70,
                        'high_limit' => 90,
                    ],
                    'methods' => [
                        'low_limit' => 70,
                        'high_limit' => 90,
                    ],
                    'lines' => [
                        'low_limit' => 70,
                        'high_limit' => 90,
                    ]
                ],
            ]
        ];
        $options = [
            'coverage' => true
        ];
        $this->throwException(new CodeCoverageException());
        $reporter = new CoverageChecker($config, $options);
        try {
            $reporter->checkCoverage($event);
            $this->fail('This test should throw an CodeCoverageException');
        } catch (CodeCoverageException $e) {
            $this->assertTrue(true);
        }
    }
}
