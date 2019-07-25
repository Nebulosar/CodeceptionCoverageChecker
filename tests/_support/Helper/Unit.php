<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Exception;
use Nebulosar\CodeCeptCodeCov\CodeCovPrinter;
use PHPUnit\Util\Printer;
use ReflectionClass;
use ReflectionException;

class Unit extends Module
{
    /**
     * Call a private / protected method
     * @param object $class - The class that holds the method
     * @param string $methodName - The name of the method to call
     * @param array $params - The parameters for the method to call
     * @return mixed - The result of the invoked method
     */
    final public function callMethod(object $class, string $methodName, array $params = [])
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
     * Makes a CodeCovPrinter with a mocked Util/Printer
     * @param int $invocationsOfPrinter - The times the mocked printer gets invoked
     * @param bool $noColors - Whether to use console colors
     * @return CodeCovPrinter - The printer under test
     * @throws Exception - On Stub::makeEmpty
     */
    final public function makePrinter(int $invocationsOfPrinter = 0, bool $noColors = false): CodeCovPrinter
    {
        $mockPrinter = Stub::makeEmpty(Printer::class, [
            'write' => Expected::exactly($invocationsOfPrinter)
        ]);
        assert($mockPrinter instanceof Printer);
        return new CodeCovPrinter($mockPrinter, $noColors);
    }
}
