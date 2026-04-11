<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot\Service;

use Djot\DjotConverter as BaseDjotConverter;
use Djot\Node\Document;
use Djot\Renderer\PlainTextRenderer;
use Djot\Renderer\SoftBreakMode;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class DjotConverter implements DjotConverterInterface
{
    private BaseDjotConverter $converter;

    private PlainTextRenderer $textRenderer;

    /**
     * @param bool $safeMode
     * @param bool $significantNewlines
     * @param string|null $softBreakMode
     * @param bool $xhtml
     * @param \Illuminate\Contracts\Cache\Repository|null $cache
     * @param array<\Djot\Extension\ExtensionInterface> $extensions
     */
    public function __construct(
        bool $safeMode = true,
        bool $significantNewlines = false,
        ?string $softBreakMode = null,
        bool $xhtml = false,
        private ?CacheRepository $cache = null,
        array $extensions = [],
    ) {
        $this->converter = new BaseDjotConverter(
            xhtml: $xhtml,
            safeMode: $safeMode,
            significantNewlines: $significantNewlines,
            softBreakMode: $softBreakMode !== null ? SoftBreakMode::from($softBreakMode) : null,
        );
        $this->textRenderer = new PlainTextRenderer();

        foreach ($extensions as $extension) {
            $this->converter->addExtension($extension);
        }
    }

    public function toHtml(string $djot): string
    {
        if ($this->cache !== null) {
            $cacheKey = 'laravel_djot_html_' . hash('xxh3', $djot);

            /** @var string|null $cached */
            $cached = $this->cache->get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }

            $html = $this->converter->convert($djot);
            $this->cache->forever($cacheKey, $html);

            return $html;
        }

        return $this->converter->convert($djot);
    }

    public function toText(string $djot): string
    {
        $document = $this->converter->parse($djot);

        return $this->textRenderer->render($document);
    }

    public function parse(string $djot): Document
    {
        return $this->converter->parse($djot);
    }

    public function getConverter(): BaseDjotConverter
    {
        return $this->converter;
    }
}
