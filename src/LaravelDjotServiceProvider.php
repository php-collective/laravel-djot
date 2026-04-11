<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use PhpCollective\LaravelDjot\Service\DjotConverter;
use PhpCollective\LaravelDjot\Service\DjotConverterInterface;
use PhpCollective\LaravelDjot\Service\DjotManager;
use PhpCollective\LaravelDjot\Service\ExtensionFactory;

class LaravelDjotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/djot.php', 'djot');

        $this->app->singleton(ExtensionFactory::class, static fn (): ExtensionFactory => new ExtensionFactory());

        $this->app->singleton(DjotManager::class, function (Container $app): DjotManager {
            /** @var \Illuminate\Contracts\Config\Repository $configRepository */
            $configRepository = $app->make(ConfigRepository::class);
            /** @var array{converters?: array<string, array<string, mixed>>, cache?: array{enabled?: bool, store?: string|null}} $config */
            $config = $configRepository->get('djot', []);

            /** @var \PhpCollective\LaravelDjot\Service\ExtensionFactory $factory */
            $factory = $app->make(ExtensionFactory::class);

            $cache = null;
            if (!empty($config['cache']['enabled'])) {
                /** @var \Illuminate\Contracts\Cache\Factory $cacheFactory */
                $cacheFactory = $app->make(CacheFactory::class);
                $cache = $cacheFactory->store($config['cache']['store'] ?? null);
            }

            $converters = [];
            foreach ($config['converters'] ?? [] as $name => $converterConfig) {
                $converters[$name] = $this->buildConverter($converterConfig, $cache, $factory);
            }

            if ($converters === []) {
                $converters['default'] = new DjotConverter(cache: $cache);
            }

            return new DjotManager($converters);
        });

        $this->app->alias(DjotManager::class, 'djot');

        $this->app->bind(DjotConverterInterface::class, static function (Container $app): DjotConverterInterface {
            /** @var \PhpCollective\LaravelDjot\Service\DjotManager $manager */
            $manager = $app->make(DjotManager::class);

            return $manager->converter('default');
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/djot.php' => $this->app->configPath('djot.php'),
        ], 'djot-config');

        $this->registerBladeDirectives();
    }

    private function registerBladeDirectives(): void
    {
        Blade::directive('djot', static function (string $expression): string {
            return "<?php echo app(\PhpCollective\LaravelDjot\Service\DjotManager::class)->toHtml({$expression}); ?>";
        });

        Blade::directive('djotRaw', static function (string $expression): string {
            return "<?php echo app(\PhpCollective\LaravelDjot\Service\DjotManager::class)->toHtmlRaw({$expression}); ?>";
        });

        Blade::directive('djotText', static function (string $expression): string {
            return "<?php echo e(app(\PhpCollective\LaravelDjot\Service\DjotManager::class)->toText({$expression})); ?>";
        });
    }

    /**
     * @param array<string, mixed> $config
     * @param \Illuminate\Contracts\Cache\Repository|null $cache
     * @param \PhpCollective\LaravelDjot\Service\ExtensionFactory $factory
     */
    private function buildConverter(
        array $config,
        ?CacheRepository $cache,
        ExtensionFactory $factory,
    ): DjotConverter {
        $extensions = [];
        $extConfigs = $config['extensions'] ?? [];
        if (is_array($extConfigs)) {
            foreach ($extConfigs as $extConfig) {
                if (is_string($extConfig)) {
                    $normalized = $extConfig;
                } elseif (is_array($extConfig)) {
                    /** @var array<string, mixed> $normalized */
                    $normalized = $extConfig;
                } else {
                    continue;
                }
                $extension = $factory->create($normalized);
                if ($extension !== null) {
                    $extensions[] = $extension;
                }
            }
        }

        $softBreakMode = $config['soft_break_mode'] ?? null;

        return new DjotConverter(
            safeMode: (bool)($config['safe_mode'] ?? true),
            significantNewlines: (bool)($config['significant_newlines'] ?? false),
            softBreakMode: is_string($softBreakMode) ? $softBreakMode : null,
            xhtml: (bool)($config['xhtml'] ?? false),
            cache: $cache,
            extensions: $extensions,
        );
    }
}
