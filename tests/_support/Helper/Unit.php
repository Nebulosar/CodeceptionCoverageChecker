<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Exception;
use Nebulosar\Codeception\CoverageChecker\Writer;
use PHPUnit\Util\Printer;
use ReflectionClass;
use ReflectionException;
use SebastianBergmann\CodeCoverage\Node\Directory;
use Unit\ExtendedDirectory;

class Unit extends Module
{
    /**
     * Call a private / protected method
     * @param object $class - The class that holds the method
     * @param string $methodName - The name of the method to call
     * @param array $params - The parameters for the method to call
     * @return mixed - The result of the invoked method
     */
    public function callMethod(object $class, string $methodName, array $params = [])
    {
        try {
            $reflectionClass = new ReflectionClass(get_class($class));
            $method = $reflectionClass->getMethod($methodName);
            $method->setAccessible(true);
            return $method->invokeArgs($class, $params);
        } catch (ReflectionException $e) {
            echo $e;
            return null;
        }
    }

    /**
     * Makes a CodeCovPrinter with a stubbed Util/Printer
     * @param string $class - The class to test
     * @param int $invocationsOfPrinter - The times the mocked printer gets invoked
     * @param bool $noColors - Whether to use console colors
     * @return Writer - The writer under test
     * @throws Exception - On Stub::makeEmpty
     */
    public function makeWriter(string $class, int $invocationsOfPrinter = 0, bool $noColors = false): Writer
    {
        $mockPrinter = Stub::makeEmpty(Printer::class, [
            'write' => Expected::exactly($invocationsOfPrinter)
        ]);
        assert($mockPrinter instanceof Printer);
        return new $class($mockPrinter, $noColors);
    }

    /**
     * Stub a Node/Directory with Stub::make
     * @param $coverage - The percentage of covered code
     * @return object|Directory - The stubbed Directory
     * @throws Exception
     */
    public function makeDirectory(int $coverage): object
    {
        return Stub::make(Directory::class, [
            'getNumExecutedLines' => $coverage,
            'getNumExecutableLines' => 100,
            'getNumTestedClasses' => $coverage,
            'getNumClasses' => 100,
            'getNumTestedMethods' => $coverage,
            'getNumMethods' => 100,
        ]);
    }
}