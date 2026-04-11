<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot\Tests;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Application;
use PhpCollective\LaravelDjot\Facades\Djot;
use PhpCollective\LaravelDjot\Service\DjotConverterInterface;
use PhpCollective\LaravelDjot\Service\DjotManager;

class LaravelDjotServiceProviderTest extends TestCase
{
    private function app(): Application
    {
        $app = $this->app;
        $this->assertNotNull($app);

        return $app;
    }

    public function testManagerIsRegistered(): void
    {
        $this->assertInstanceOf(DjotManager::class, $this->app()->make(DjotManager::class));
    }

    public function testManagerIsAliasedToDjot(): void
    {
        $this->assertInstanceOf(DjotManager::class, $this->app()->make('djot'));
    }

    public function testDefaultConverterIsBound(): void
    {
        $this->assertInstanceOf(DjotConverterInterface::class, $this->app()->make(DjotConverterInterface::class));
    }

    public function testFacadeToHtml(): void
    {
        $html = Djot::toHtml('Hello *world*!');

        $this->assertStringContainsString('<strong>world</strong>', $html);
    }

    public function testFacadeSafeModeDefault(): void
    {
        $html = Djot::toHtml('[Click](javascript:alert(1))');

        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function testFacadeToText(): void
    {
        $text = Djot::toText('Hello *world*!');

        $this->assertStringContainsString('Hello world!', $text);
    }

    public function testDefaultConfigHasSafeMode(): void
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $this->app()->make(ConfigRepository::class);

        $this->assertTrue($config->get('djot.converters.default.safe_mode'));
    }

    public function testMultipleConvertersAreRegistered(): void
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $this->app()->make(ConfigRepository::class);
        $config->set('djot.converters', [
            'default' => ['safe_mode' => true],
            'trusted' => ['safe_mode' => false],
            'docs' => [
                'safe_mode' => false,
                'extensions' => ['autolink', 'smart_quotes'],
            ],
        ]);
        $this->app()->forgetInstance(DjotManager::class);

        /** @var \PhpCollective\LaravelDjot\Service\DjotManager $manager */
        $manager = $this->app()->make(DjotManager::class);

        $this->assertCount(3, $manager->getConverters());
        $this->assertArrayHasKey('default', $manager->getConverters());
        $this->assertArrayHasKey('trusted', $manager->getConverters());
        $this->assertArrayHasKey('docs', $manager->getConverters());
    }

    public function testCacheCanBeEnabled(): void
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $this->app()->make(ConfigRepository::class);
        $config->set('djot.cache.enabled', true);
        $config->set('cache.default', 'array');
        $this->app()->forgetInstance(DjotManager::class);

        /** @var \PhpCollective\LaravelDjot\Service\DjotManager $manager */
        $manager = $this->app()->make(DjotManager::class);

        // First render populates cache, second retrieves
        $first = $manager->toHtml('Hello *world*!');
        $second = $manager->toHtml('Hello *world*!');

        $this->assertSame($first, $second);
        $this->assertStringContainsString('<strong>world</strong>', $first);
    }
}
