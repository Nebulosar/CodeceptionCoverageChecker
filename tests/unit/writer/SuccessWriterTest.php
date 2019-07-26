<?php
namespace Tests\Nebulosar\Unit\Writer;

use Nebulosar\Codeception\CoverageChecker\SuccessWriter;

class SuccessWriterTest extends WriterTest
{
    protected $writerClass = SuccessWriter::class;

    public function testWriteMethod(): void
    {
        $writer = $this->tester->makeWriter($this->writerClass, 1);
        $writer->write('Lines', '80%', '40%');
    }
}
