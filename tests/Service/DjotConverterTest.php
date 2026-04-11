<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot\Tests\Service;

use Djot\Node\Document;
use PhpCollective\LaravelDjot\Service\DjotConverter;
use PHPUnit\Framework\TestCase;

class DjotConverterTest extends TestCase
{
    public function testToHtml(): void
    {
        $converter = new DjotConverter();

        $html = $converter->toHtml('Hello *world*!');

        $this->assertStringContainsString('<strong>world</strong>', $html);
    }

    public function testToHtmlWithEmphasis(): void
    {
        $converter = new DjotConverter();

        $html = $converter->toHtml('Hello _world_!');

        $this->assertStringContainsString('<em>world</em>', $html);
    }

    public function testToText(): void
    {
        $converter = new DjotConverter();

        $text = $converter->toText('Hello *world*!');

        $this->assertStringContainsString('Hello world!', $text);
    }

    public function testSafeModeEnabledByDefault(): void
    {
        $converter = new DjotConverter();

        $html = $converter->toHtml('[Click me](javascript:alert("xss"))');

        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function testSafeModeDisabled(): void
    {
        $converter = new DjotConverter(safeMode: false);

        $html = $converter->toHtml('[Click me](javascript:alert(1))');

        $this->assertStringContainsString('javascript:', $html);
    }

    public function testParseReturnsDocument(): void
    {
        $converter = new DjotConverter();

        $document = $converter->parse('# Heading');

        $this->assertInstanceOf(Document::class, $document);
    }
}
