<?php
namespace Tests\Nebulosar\Unit\Writer;

use Nebulosar\Codeception\CoverageChecker\WarningWriter;

class WarningWriterTest extends WriterTest
{
    protected $writerClass = WarningWriter::class;

    public function testWriteMethod(): void
    {
        $writer = $this->tester->makeWriter($this->writerClass, 1);
        $writer->write('Lines', '80%', '40%');
    }
}
