# Configuration

## Default Configuration

The package works out of the box with sensible defaults. Safe mode is enabled by default for security.

## Full Configuration Reference

```php
// config/djot.php
return [
    'converters' => [
        'default' => [
            'safe_mode' => true,             // XSS protection (enabled by default)
            'significant_newlines' => false, // Markdown-like line break handling
            'soft_break_mode' => null,       // newline, space or br
            'xhtml' => false,                // XHTML-compatible output
            'extensions' => [],
        ],

        // Add custom profiles as needed
        'trusted' => [
            'safe_mode' => false,
        ],
    ],
    'cache' => [
        'enabled' => false,
        'store' => null, // null = default cache store
    ],
];
```

## Converter Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `safe_mode` | bool | `true` | XSS protection - disable only for trusted content |
| `significant_newlines` | bool | `false` | Allow blocks to interrupt paragraphs without blank lines (markdown-like behavior) |
| `soft_break_mode` | string | `null` | How to render soft breaks: `newline`, `space`, or `br` |
| `xhtml` | bool | `false` | Output XHTML-compatible markup (self-closing tags) |
| `extensions` | array | `[]` | Djot extensions to enable for this profile |

## Converter Profiles

You can define multiple converter profiles for different contexts. Each profile is resolved as its own `DjotConverter` instance.

### Example: Default Safe + Trusted Converter

```php
'converters' => [
    // Default is safe
    'default' => [
        'safe_mode' => true,
    ],

    // For trusted admin/editor content
    'trusted' => [
        'safe_mode' => false,
    ],

    // For documentation with extensions
    'docs' => [
        'safe_mode' => false,
        'extensions' => [
            'table_of_contents',
            'heading_permalinks',
        ],
    ],
],
```

### Using Profiles in Blade

```blade
{{-- Uses 'default' profile (safe mode) --}}
@djot($comment->text)

{{-- Uses 'trusted' profile (no safe mode) --}}
{!! Djot::toHtml($article->body, 'trusted') !!}

{{-- Quick way for trusted content --}}
@djotRaw($article->body)
```

### Using Profiles in Services

```php
use PhpCollective\LaravelDjot\Service\DjotManager;

class ContentService
{
    public function __construct(
        private DjotManager $djot,
    ) {}

    public function renderComment(string $text): string
    {
        return $this->djot->toHtml($text); // default profile
    }

    public function renderArticle(string $text): string
    {
        return $this->djot->toHtml($text, 'trusted');
    }
}
```

## Container Bindings

The package registers the following bindings:

| Binding | Description |
|---------|-------------|
| `PhpCollective\LaravelDjot\Service\DjotManager` | Multi-profile manager (singleton) |
| `djot` | Alias for `DjotManager` |
| `PhpCollective\LaravelDjot\Service\DjotConverterInterface` | Default converter instance |

## Next Steps

- [Safe Mode](safe-mode.md) - understand XSS protection
- [Caching](caching.md) - improve performance with caching
