# Safe Mode

Safe mode is *enabled by default* to provide XSS protection. This protects against malicious content in user comments, forum posts, or any input from external sources.

## Default Behavior

The `@djot` directive uses safe mode by default. For trusted content (admin/CMS), use `@djotRaw` or a named converter with `safe_mode: false`.

```blade
{{-- Safe by default - use for any content --}}
@djot($content)

{{-- Explicit raw - only for trusted content you control --}}
@djotRaw($article->body)
```

## When to Disable Safe Mode

| Content Source | Directive to Use |
|----------------|------------------|
| User comments | `@djot` (default) |
| Forum posts | `@djot` (default) |
| External API data | `@djot` (default) |
| User profile descriptions | `@djot` (default) |
| Admin/editor content | `@djotRaw` or named converter |
| CMS content (trusted editors) | `@djotRaw` or named converter |

**Rule of thumb:** Only use `@djotRaw` when you fully control and trust the content, or when it has been sanitized (e.g. via HTMLPurifier).

## What Safe Mode Does

When enabled (the default), safe mode:

1. **Sanitizes URLs** — blocks `javascript:`, `data:`, and other dangerous protocols
2. **Removes raw HTML** — strips any embedded HTML/scripts
3. **Validates links** — ensures URLs are safe

### Example: Dangerous Link

Input:

```djot
[Click me](javascript:alert('XSS'))
```

With `@djot` (safe mode, default):

```html
<p><a href="">Click me</a></p>
```

With `@djotRaw` (no safe mode):

```html
<p><a href="javascript:alert('XSS')">Click me</a></p>
```

## Using Named Converters

For more control, define named converter profiles:

```php
// config/djot.php
return [
    'converters' => [
        'default' => [
            'safe_mode' => true,
        ],
        'trusted' => [
            'safe_mode' => false,
        ],
        'docs' => [
            'safe_mode' => false,
            'extensions' => [
                'table_of_contents',
                'heading_permalinks',
            ],
        ],
    ],
];
```

```blade
@djot($comment->text)
{!! Djot::toHtml($article->body, 'trusted') !!}
{!! Djot::toHtml($documentation->content, 'docs') !!}
```

## Security Recommendations

1. **Use the default** — `@djot` is safe by default; use it everywhere
2. **Explicit trust** — only use `@djotRaw` for content you control
3. **Validate before storing** — safe mode helps at render time, but validate input too with the `ValidDjot` rule
4. **Review trusted content** — even "trusted" content should be reviewed

## More Information

For advanced safe mode options (custom blocked schemes, strict mode), see the [php-collective/djot safe mode documentation](https://php-collective.github.io/djot-php/guide/safe-mode).

## Next Steps

- [Configuration](configuration.md) - set up converter profiles
- [Service Usage](service-usage.md) - use in PHP code
