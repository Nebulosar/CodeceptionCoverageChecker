<?php
namespace Tests\Nebulosar\Unit\Writer;

use Nebulosar\Codeception\CoverageChecker\ErrorWriter;

class ErrorWriterTest extends WriterTest
{
    protected $writerClass = ErrorWriter::class;

    public function testWriteMethod(): void
    {
        $writer = $this->tester->makeWriter($this->writerClass, 1);
        $writer->write('Lines', '80%', '40%');
    }
}
