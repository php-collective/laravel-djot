# Laravel Djot

Djot markup language integration for Laravel applications.

## What is Djot?

[Djot](https://djot.net) is a modern light markup language created by John MacFarlane, the author of CommonMark and Pandoc. It builds on Markdown's foundation while addressing its complexity issues:

- **Cleaner syntax** — more consistent rules, fewer edge cases
- **More features** — footnotes, definition lists, task lists, math, highlights, and more
- **Easier to parse** — simpler specification, faster implementations
- **Better extensibility** — designed for customization

## Features

This package provides:

- **Blade directives** — `@djot`, `@djotRaw`, `@djotText`
- **Facade** — `Djot::toHtml()`, `Djot::toText()`, `Djot::toHtmlRaw()`
- **Service injection** — `DjotConverterInterface` and `DjotManager` for controllers and services
- **Validation** — `ValidDjot` rule for request validation
- **Multiple profiles** — different configurations for different contexts (e.g. user content vs. admin content)
- **Safe mode** — XSS protection for untrusted input (enabled by default)
- **Caching** — optional caching of rendered output via any Laravel cache store
- **Plain text** — extract plain text for search indexing or previews

## Quick Start

```bash
composer require php-collective/laravel-djot
```

```blade
{{-- In your Blade views --}}
@djot($article->body)
```

```php
// In your services
public function __construct(
    private DjotConverterInterface $djot,
) {}

public function render(string $content): string
{
    return $this->djot->toHtml($content);
}
```

## Documentation

- [Installation](installation.md)
- [Configuration](configuration.md)
- [Blade Usage](blade-usage.md)
- [Service Usage](service-usage.md)
- [Validation](validation.md)
- [Safe Mode](safe-mode.md)
- [Caching](caching.md)
- [Extensions](extensions.md)
- [Djot Syntax](djot-syntax.md)

## Requirements

- PHP 8.2 or higher
- Laravel 11.x or 12.x
