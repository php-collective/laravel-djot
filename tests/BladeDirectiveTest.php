<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot\Tests;

use Illuminate\Support\Facades\Blade;

class BladeDirectiveTest extends TestCase
{
    public function testDjotDirectiveRendersHtml(): void
    {
        $compiled = Blade::compileString('@djot($content)');
        $content = 'Hello *world*!';

        ob_start();
        eval('?>' . $compiled);
        /** @var string $output */
        $output = ob_get_clean();

        $this->assertStringContainsString('<strong>world</strong>', $output);
    }

    public function testDjotDirectiveUsesSafeMode(): void
    {
        $compiled = Blade::compileString('@djot($content)');
        $content = '[Click](javascript:alert(1))';

        ob_start();
        eval('?>' . $compiled);
        /** @var string $output */
        $output = ob_get_clean();

        $this->assertStringNotContainsString('javascript:', $output);
    }

    public function testDjotRawDirectiveBypassesSafeMode(): void
    {
        $compiled = Blade::compileString('@djotRaw($content)');
        // Raw HTML shows the bypass; djot blanks dangerous URL schemes in every mode.
        $content = '`<b>raw</b>`{=html}';

        ob_start();
        eval('?>' . $compiled);
        /** @var string $output */
        $output = ob_get_clean();

        $this->assertStringContainsString('<b>raw</b>', $output);
    }

    public function testDjotTextDirectiveOutputsPlainText(): void
    {
        $compiled = Blade::compileString('@djotText($content)');
        $content = 'Hello *world*!';

        ob_start();
        eval('?>' . $compiled);
        /** @var string $output */
        $output = ob_get_clean();

        $this->assertStringContainsString('Hello world!', $output);
        $this->assertStringNotContainsString('<strong>', $output);
    }
}
