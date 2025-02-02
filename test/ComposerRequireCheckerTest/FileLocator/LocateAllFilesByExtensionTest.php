<?php

namespace ComposerRequireCheckerTest\FileLocator;

use ArrayObject;
use ComposerRequireChecker\FileLocator\LocateAllFilesByExtension;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ComposerRequireChecker\FileLocator\LocateAllFilesByExtension
 */
class LocateAllFilesByExtensionTest extends TestCase
{
    /** @var LocateAllFilesByExtension */
    private $locator;
    /** @var vfsStreamDirectory */
    private $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->locator = new LocateAllFilesByExtension();
        $this->root = vfsStream::setup();
    }

    public function testLocateFromNoDirectories(): void
    {
        $files = $this->locate([], '.php', null);

        $this->assertCount(0, $files);
    }

    public function testLocateFromASingleDirectory(): void
    {
        $dir = vfsStream::newDirectory('MyNamespaceA')->at($this->root);
        $files = [];
        for ($i = 0; $i < 3; $i++) {
            $files[] = vfsStream::newFile("MyClass$i.php")->at($dir);
        }

        $foundFiles = $this->locate([$dir->url()], '.php', null);

        $this->assertCount(count($files), $foundFiles);
        foreach ($files as $file) {
            $this->assertContains($file->url(), str_replace('\\', '/', $foundFiles));
        }
    }

    /**
     * @param string[] $directories
     * @param string $fileExtension
     * @param array|null $blacklist
     * @return array|\string[]
     */
    private function locate(array $directories, string $fileExtension, ?array $blacklist): array
    {
        return iterator_to_array(($this->locator)(new ArrayObject($directories), $fileExtension, $blacklist));
    }
}
