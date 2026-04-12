# Laravel Djot

[![CI](https://github.com/php-collective/laravel-djot/actions/workflows/ci.yml/badge.svg)](https://github.com/php-collective/laravel-djot/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/packagist/php-v/php-collective/laravel-djot)](https://packagist.org/packages/php-collective/laravel-djot)
[![License](https://img.shields.io/packagist/l/php-collective/laravel-djot)](LICENSE)

[Djot](https://github.com/php-collective/djot-php) markup language integration for Laravel — Blade directives, services, validation, and caching.

## Installation

```bash
composer require php-collective/laravel-djot
```

The service provider and `Djot` facade alias are auto-discovered via Laravel's package discovery.

Optionally publish the config:

```bash
php artisan vendor:publish --tag=djot-config
```

## Usage

### Blade Directives

```blade
{{-- Safe by default - XSS protection enabled --}}
@djot($article->body)

{{-- For trusted content only - no XSS protection --}}
@djotRaw($trustedContent)

{{-- Plain text output (escaped) --}}
@djotText($article->body)
```

### Facade

```php
use PhpCollective\LaravelDjot\Facades\Djot;

$html = Djot::toHtml($source);
$text = Djot::toText($source);
$raw  = Djot::toHtmlRaw($trustedSource);
```

### Dependency Injection

```php
use PhpCollective\LaravelDjot\Service\DjotConverterInterface;
use PhpCollective\LaravelDjot\Service\DjotManager;

class ArticleController
{
    public function __construct(
        private DjotConverterInterface $djot,
        private DjotManager $manager,
    ) {}

    public function show(Article $article): View
    {
        return view('article.show', [
            'html' => $this->djot->toHtml($article->body),
            'text' => $this->djot->toText($article->body),
            'docs' => $this->manager->toHtml($article->body, 'docs'),
        ]);
    }
}
```

## Configuration

```php
// config/djot.php
return [
    'converters' => [
        // Default has safe_mode: true (XSS protection enabled)
        'default' => [
            'safe_mode' => true,
        ],

        // For trusted content (admin, CMS)
        'trusted' => [
            'safe_mode' => false,
        ],
    ],
    'cache' => [
        'enabled' => false,
        'store' => null,
    ],
];
```

### Multiple Converter Profiles

Use different configurations for different contexts:

```blade
{{-- Default is safe --}}
@djot($comment->body)

{{-- Use named converter for trusted content --}}
{!! Djot::toHtml($article->body, 'trusted') !!}

{{-- Or use @djotRaw for quick trusted rendering --}}
@djotRaw($article->body)
```

### Safe Mode

Safe mode is *enabled by default* for XSS protection. Disable only for trusted content:

```php
'converters' => [
    'trusted' => [
        'safe_mode' => false,
    ],
],
```

### Extensions

Enable [djot-php extensions](https://github.com/php-collective/djot-php) per converter:

```php
'converters' => [
    'default' => [
        'extensions' => [
            ['type' => 'autolink'],
            ['type' => 'smart_quotes'],
            [
                'type' => 'heading_permalinks',
                'symbol' => '#',
                'position' => 'after',
            ],
        ],
    ],
    'with_mentions' => [
        'extensions' => [
            [
                'type' => 'mentions',
                'user_url_template' => 'https://github.com/{username}',
            ],
            'table_of_contents',
        ],
    ],
],
```

Available extensions:

- `admonition` - Admonition blocks (note, tip, warning, danger, etc.)
- `autolink` - Auto-convert URLs to clickable links
- `code_group` - Transform code-group divs into tabbed interfaces
- `default_attributes` - Add default attributes to elements by type
- `external_links` - Configure external link behavior (target, rel)
- `frontmatter` - Parse YAML/TOML/JSON frontmatter blocks
- `heading_level_shift` - Shift heading levels up/down
- `heading_permalinks` - Add anchor links to headings
- `heading_reference` - Link to headings with `[text](#heading)` syntax
- `inline_footnotes` - Convert spans with class to inline footnotes
- `mentions` - Convert @username to profile links
- `mermaid` - Render Mermaid diagram code blocks
- `semantic_span` - Convert spans to `<kbd>`, `<dfn>`, `<abbr>` elements
- `smart_quotes` - Convert straight quotes to typographic quotes
- `table_of_contents` - Generate TOC from headings
- `tabs` - Tabbed content blocks (CSS or ARIA mode)
- `wikilinks` - Support `[[Page Name]]` wiki-style links

See [Extensions documentation](https://php-collective.github.io/laravel-djot/extensions/) for detailed configuration options.

### Validation Rule

Validate that a field contains valid Djot markup:

```php
use PhpCollective\LaravelDjot\Rules\ValidDjot;

$request->validate([
    'body' => ['required', 'string', new ValidDjot()],
]);
```

## Documentation

Full documentation: **[php-collective.github.io/laravel-djot](https://php-collective.github.io/laravel-djot/)**

- [Installation](https://php-collective.github.io/laravel-djot/guide/installation)
- [Configuration](https://php-collective.github.io/laravel-djot/guide/configuration)
- [Blade Usage](https://php-collective.github.io/laravel-djot/guide/blade-usage)
- [Service Usage](https://php-collective.github.io/laravel-djot/guide/service-usage)
- [Validation](https://php-collective.github.io/laravel-djot/guide/validation)
- [Safe Mode](https://php-collective.github.io/laravel-djot/guide/safe-mode)
- [Extensions](https://php-collective.github.io/laravel-djot/extensions/)
- [Caching](https://php-collective.github.io/laravel-djot/guide/caching)
- [Djot Syntax](https://php-collective.github.io/laravel-djot/guide/djot-syntax)

## Demo Application

See the [laravel-djot-demo](https://github.com/php-collective/laravel-djot-demo) for a complete example application.

## What is Djot?

[Djot](https://djot.net) is a modern light markup language created by John MacFarlane (author of CommonMark/Pandoc). It offers cleaner syntax and more features than Markdown while being easier to parse.

Learn more about Djot syntax at [djot.net](https://djot.net).
