<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot\Service;

use InvalidArgumentException;

class DjotManager
{
    private ?DjotConverterInterface $rawConverter = null;

    /**
     * @param array<string, \PhpCollective\LaravelDjot\Service\DjotConverterInterface> $converters
     */
    public function __construct(private array $converters)
    {
    }

    /**
     * Convert Djot markup to HTML using the named converter (or default).
     */
    public function toHtml(string $djot, string $converter = 'default'): string
    {
        return $this->getConverter($converter)->toHtml($djot);
    }

    /**
     * Convert Djot markup to HTML without safe mode (trusted content only).
     */
    public function toHtmlRaw(string $djot): string
    {
        return $this->getRawConverter()->toHtml($djot);
    }

    /**
     * Convert Djot markup to plain text using the named converter (or default).
     */
    public function toText(string $djot, string $converter = 'default'): string
    {
        return $this->getConverter($converter)->toText($djot);
    }

    /**
     * Get a named converter instance.
     */
    public function converter(string $name = 'default'): DjotConverterInterface
    {
        return $this->getConverter($name);
    }

    /**
     * @return array<string, \PhpCollective\LaravelDjot\Service\DjotConverterInterface>
     */
    public function getConverters(): array
    {
        return $this->converters;
    }

    private function getConverter(string $name): DjotConverterInterface
    {
        if (!isset($this->converters[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Djot converter "%s" not found. Available converters: %s',
                $name,
                implode(', ', array_keys($this->converters)),
            ));
        }

        return $this->converters[$name];
    }

    private function getRawConverter(): DjotConverterInterface
    {
        if ($this->rawConverter === null) {
            $this->rawConverter = new DjotConverter(safeMode: false);
        }

        return $this->rawConverter;
    }
}
