<?php
namespace Tests\Nebulosar\Unit;

use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use Exception;
use Nebulosar\Codeception\CoverageChecker\CoverageReporter;

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
        $this->makeEmptyExcept(CoverageReporter::class, '__construct', [
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
        $reporter = $this->makeEmptyExcept(CoverageReporter::class, '__construct', [
            'init' => Expected::exactly(5, $config['coverage'])
        ]);
        $reporter->__construct($config, ['coverage-xml' => true]);
        $reporter->__construct($config, ['coverage-html' => true]);
        $reporter->__construct($config, ['coverage-text' => true]);
        $reporter->__construct($config, ['coverage-crap4j' => true]);
        $reporter->__construct($config, ['coverage-phpunit' => true]);
    }

    public function testConstructDisabled(): void
    {
        $config = [
            'coverage' => [
                'enabled' => false,
            ]
        ];
        $this->makeEmptyExcept(CoverageReporter::class, '__construct', [
            'init' => Expected::never()
        ])->__construct($config, []);

        $config = [
            'coverage' => [
                'enabled' => true,
            ]
        ];
        $this->makeEmptyExcept(CoverageReporter::class, '__construct', [
            'init' => Expected::never()
        ])->__construct($config, []);
    }

    public function testInit(): void
    {

    }

    public function testCheckCoverage(): void
    {

    }
}
