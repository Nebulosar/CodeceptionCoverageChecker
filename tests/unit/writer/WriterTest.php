<?php

namespace Tests\Nebulosar\Unit\Writer;

use Codeception\Test\Unit;

class WriterTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    /**
     * @var string
     */
    protected $color = "\x1b[0m";
    /**
     * @var string
     */
    protected $writerClass = null;

    public function _before(): void
    {
        if (!isset($this->writerClass)) {
            if (get_class($this) !== 'Tests\Nebulosar\Unit\Writer\WriterTest') {
                $this->fail('Extend of WriterTest should have set the $writerClass variable!');
            } else {
                $this->markTestSkipped('WriterTest does not need to be tested by itself.');
            }
        }
        parent::_before();
    }

    public function testFormatWithPadding(): void
    {
        $color = $this->color;
        $string = 'Spam eggs';
        $extraPadding = 2;
        $writer = $this->tester->makeWriter($this->writerClass);
        $result = $this->tester->callMethod($writer, 'formatLine', [$color, strlen($string) + $extraPadding, $string]);
        $this->assertEquals($color . $string . str_repeat(' ', $extraPadding) . $color, $result);
    }

    public function testFormatWithPlainColorName(): void
    {
        $color = ['name' => 'reset', 'code' => $this->color];
        $string = 'Spam eggs';
        $writer = $this->tester->makeWriter($this->writerClass);
        $result = $this->tester->callMethod($writer, 'formatLine', [$color['name'], 0, $string]);
        $this->assertEquals($color['code'] . $string . $color['code'], $result);
    }

    public function testFormatWithWrongPlainColorName(): void
    {
        $color = ['name' => 'nonExistingColor', 'code' => $this->color];
        $string = 'Spam eggs';
        $writer = $this->tester->makeWriter($this->writerClass);
        $result = $this->tester->callMethod($writer, 'formatLine', [$color['name'], 0, $string]);
        $this->assertEquals($string, $result);
    }

    public function testFormatWithoutColor(): void
    {
        $color = $this->color;
        $string = 'Spam eggs';
        $writer = $this->tester->makeWriter($this->writerClass, 0, true);
        $result = $this->tester->callMethod($writer, 'formatLine', [$color, 0, $string]);
        $this->assertEquals($string, $result);
    }

    public function testFormatMessage(): void
    {
        $title = 'Spam eggs';
        $lines = [
            'Ham and',
            'bacon',
        ];
        $color = $this->color;
        $writer = $this->tester->makeWriter($this->writerClass);
        $result = $this->tester->callMethod($writer, 'formatMessage', [$title, $lines, $color]);
        $this->assertStringContainsString($title . PHP_EOL, $result);
        $this->assertStringContainsString($color . $lines[0], $result);
        $this->assertStringContainsString($color . $lines[1], $result);
    }
}
