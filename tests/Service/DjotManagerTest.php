<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot\Tests\Service;

use InvalidArgumentException;
use PhpCollective\LaravelDjot\Service\DjotConverter;
use PhpCollective\LaravelDjot\Service\DjotManager;
use PHPUnit\Framework\TestCase;

class DjotManagerTest extends TestCase
{
    private DjotManager $manager;

    protected function setUp(): void
    {
        $this->manager = new DjotManager([
            'default' => new DjotConverter(),
            'trusted' => new DjotConverter(safeMode: false),
        ]);
    }

    public function testToHtml(): void
    {
        $html = $this->manager->toHtml('Hello *world*!');

        $this->assertStringContainsString('<strong>world</strong>', $html);
    }

    public function testToHtmlWithNamedConverter(): void
    {
        $html = $this->manager->toHtml('Hello *world*!', 'trusted');

        $this->assertStringContainsString('<strong>world</strong>', $html);
    }

    public function testToHtmlRawBypassesSafeMode(): void
    {
        $html = $this->manager->toHtmlRaw('[Click](javascript:alert(1))');

        $this->assertStringContainsString('href="javascript:alert(1)"', $html);
    }

    public function testToText(): void
    {
        $text = $this->manager->toText('Hello *world*!');

        $this->assertStringContainsString('Hello world!', $text);
    }

    public function testConverterAccessor(): void
    {
        $this->assertSame('default', array_search($this->manager->converter('default'), $this->manager->getConverters(), true));
    }

    public function testUnknownConverterThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Djot converter "missing" not found');

        $this->manager->toHtml('test', 'missing');
    }
}
