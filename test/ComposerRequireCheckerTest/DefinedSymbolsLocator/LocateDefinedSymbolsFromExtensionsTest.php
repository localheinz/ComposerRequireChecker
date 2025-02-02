<?php

namespace ComposerRequireCheckerTest\DefinedSymbolsLocator;

use ComposerRequireChecker\DefinedSymbolsLocator\LocateDefinedSymbolsFromExtensions;
use PHPUnit\Framework\TestCase;

class LocateDefinedSymbolsFromExtensionsTest extends TestCase
{

    /**
     * @var LocateDefinedSymbolsFromExtensions
     */
    private $locator;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->locator = new LocateDefinedSymbolsFromExtensions();
    }

    public function testThrowsExceptionForUnknownExtension(): void
    {
        $this->expectException('ComposerRequireChecker\Exception\UnknownExtensionException');
        $this->locator->__invoke(['unknown_extension_name']);
    }

    public function testReturnsFilledArray(): void
    {
        $symbols = $this->locator->__invoke(['Core']);
        $this->assertGreaterThan(1, count($symbols));
        $this->assertIsArray($symbols);
    }

    public function testSymbolsContainConstants(): void
    {
        $symbols = $this->locator->__invoke(['Core']);
        $this->assertTrue(in_array('PHP_VERSION', $symbols));
    }

    public function testSymbolsContainFunctions(): void
    {
        $symbols = $this->locator->__invoke(['Core']);
        $this->assertTrue(in_array('strlen', $symbols));
    }

    public function testSymbolsContainClasses(): void
    {
        $symbols = $this->locator->__invoke(['Core']);
        $this->assertTrue(in_array('stdClass', $symbols));
    }

    public function testPackageNameInterpolation(): void
    {
        $symbols = $this->locator->__invoke(['zend-opcache']);
        $symbols2 = $this->locator->__invoke(['Zend Opcache']);
        $this->assertEquals($symbols, $symbols2);
    }

    public function testCanHandleMultipleExtensions(): void
    {
        $coreSymbols = $this->locator->__invoke(['Core']);
        $standardSymbols = $this->locator->__invoke(['standard']);

        $this->assertGreaterThan(1, count($coreSymbols));
        $this->assertGreaterThan(1, count($standardSymbols));

        $combinedSymbols = $this->locator->__invoke(['Core', 'standard']);
        $this->assertSame(array_merge($coreSymbols, $standardSymbols), $combinedSymbols);
    }
}
