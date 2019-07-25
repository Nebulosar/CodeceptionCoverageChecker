<?php

use Codeception\Test\Unit;
use Nebulosar\CodeCeptCodeCov\CoverageType;
use Nebulosar\CodeCeptCodeCov\Severity;

class CodeCovPrinterTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $printer;
    protected $color = "\x1b[0m";

    final public function testWritingWithAllTypesAndSeverities(): void
    {
        $this->printer = $this->tester->makePrinter(3);
        $this->printer->write(CoverageType::LINES, '80%', '40%', Severity::WARNING);
        $this->printer->write(CoverageType::METHODS, '80%', '40%', Severity::SUCCESS);
        $this->printer->write(CoverageType::CLASSES, '80%', '40%', Severity::ERROR);
    }

    final public function testFormatWithPadding(): void
    {
        $color = $this->color;
        $string = 'Spam eggs';
        $extraPadding = 2;
        $this->printer = $this->tester->makePrinter();
        $result = $this->tester->callMethod($this->printer, 'format', [$color, strlen($string) + $extraPadding, $string]);
        $this->assertEquals($color . $string . str_repeat(' ', $extraPadding) . $color, $result);
    }

    final public function testFormatWithPlainColorName(): void
    {
        $color = ['name' => 'reset', 'code' => $this->color];
        $string = 'Spam eggs';
        $this->printer = $this->tester->makePrinter();
        $result = $this->tester->callMethod($this->printer, 'format', [$color['name'], 0, $string]);
        $this->assertEquals($color['code'] . $string .$color['code'], $result);
    }

    final public function testFormatWithWrongPlainColorName(): void
    {
        $color = ['name' => 'nonExistingColor', 'code' => $this->color];
        $string = 'Spam eggs';
        $this->printer = $this->tester->makePrinter();
        $result = $this->tester->callMethod($this->printer, 'format', [$color['name'], 0, $string]);
        $this->assertEquals($string, $result);
    }

    final public function testFormatWithoutColor(): void
    {
        $color = $this->color;
        $string = 'Spam eggs';
        $this->printer = $this->tester->makePrinter(0, true);
        $result = $this->tester->callMethod($this->printer, 'format', [$color, 0, $string]);
        $this->assertEquals($string, $result);
    }

    final public function testFormatMessage(): void
    {
        $title = 'Spam eggs';
        $lines = [
            'Ham and',
            'bacon',
        ];
        $color = $this->color;
        $this->printer = $this->tester->makePrinter();
        $result = $this->tester->callMethod($this->printer, 'formatMessage', [$title, $lines, $color]);
        $this->assertStringContainsString($title . PHP_EOL, $result);
        $this->assertStringContainsString($color . $lines[0], $result);
        $this->assertStringContainsString($color . $lines[1], $result);
    }

    final public function testFormatError(): void {
        $type = 'Line';
        $limit = '80%';
        $linePercentage = '20%';
        $this->printer = $this->tester->makePrinter();
        $result = $this->tester->callMethod($this->printer, 'formatError', [$type, $limit, $linePercentage]);
        $this->assertStringContainsString('ERROR:', $result);
        $this->assertStringContainsString($type, $result);
        $this->assertStringContainsString('Threshold: ' . $limit . '%', $result);
        $this->assertStringContainsString('Coverage: ' . $linePercentage, $result);
    }

    final public function testFormatWarning(): void {
        $type = 'Line';
        $limit = '80%';
        $linePercentage = '60%';
        $this->printer = $this->tester->makePrinter();
        $result = $this->tester->callMethod($this->printer, 'formatWarning', [$type, $limit, $linePercentage]);
        $this->assertStringContainsString('WARNING:', $result);
        $this->assertStringContainsString($type, $result);
        $this->assertStringContainsString('Threshold: ' . $limit . '%', $result);
        $this->assertStringContainsString('Coverage: ' . $linePercentage, $result);
    }
    final public function testFormatSuccess(): void {
        $type = 'Line';
        $limit = '80%';
        $linePercentage = '85%';
        $this->printer = $this->tester->makePrinter();
        $result = $this->tester->callMethod($this->printer, 'formatSuccess', [$type, $limit, $linePercentage]);
        $this->assertStringContainsString('SUCCESS:', $result);
        $this->assertStringContainsString($type, $result);
        $this->assertStringContainsString('Threshold: ' . $limit . '%', $result);
        $this->assertStringContainsString('Coverage: ' . $linePercentage, $result);
    }
}
