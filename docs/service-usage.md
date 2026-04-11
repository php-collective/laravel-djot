# Service Usage

## Basic Injection

Inject the converter interface:

```php
use PhpCollective\LaravelDjot\Service\DjotConverterInterface;

class ArticleController extends Controller
{
    public function __construct(
        private DjotConverterInterface $djot,
    ) {}

    public function show(Article $article): View
    {
        $html = $this->djot->toHtml($article->body);
        $plainText = $this->djot->toText($article->body);

        return view('article.show', [
            'article' => $article,
            'bodyHtml' => $html,
            'bodyText' => $plainText,
        ]);
    }
}
```

## Available Methods

### `toHtml(string $djot): string`

Converts Djot markup to HTML.

```php
$html = $this->djot->toHtml('*Hello* _world_!');
// <p><strong>Hello</strong> <em>world</em>!</p>
```

### `toText(string $djot): string`

Converts Djot markup to plain text.

```php
$text = $this->djot->toText('*Hello* _world_!');
// Hello world!
```

### `parse(string $djot): Document`

Parses Djot markup into an AST (Abstract Syntax Tree). Useful for advanced manipulation.

```php
use Djot\Node\Document;

$document = $this->djot->parse('# Heading');
```

## Using Multiple Profiles

Inject the `DjotManager` to access all registered profiles:

```php
use PhpCollective\LaravelDjot\Service\DjotManager;

class CommentService
{
    public function __construct(
        private DjotManager $djot,
    ) {}

    public function renderUserComment(string $text): string
    {
        return $this->djot->toHtml($text); // default profile (safe mode)
    }

    public function renderAdminContent(string $text): string
    {
        return $this->djot->toHtml($text, 'trusted');
    }

    public function renderTrustedInline(string $text): string
    {
        return $this->djot->toHtmlRaw($text);
    }
}
```

You can also grab a specific converter directly:

```php
$docsConverter = $this->djot->converter('docs');
$html = $docsConverter->toHtml($content);
```

## Use Cases

### Notifications / Mail

```php
use PhpCollective\LaravelDjot\Service\DjotConverterInterface;

class NewsletterMail extends Mailable
{
    public function __construct(private string $djotBody)
    {
    }

    public function build(DjotConverterInterface $djot): self
    {
        return $this->html($djot->toHtml($this->djotBody))
            ->text($djot->toText($this->djotBody));
    }
}
```

### Search Indexing

```php
class SearchIndexer
{
    public function __construct(
        private DjotConverterInterface $djot,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function indexArticle(Article $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'content' => $this->djot->toText($article->body),
            'html' => $this->djot->toHtml($article->body),
        ];
    }
}
```

### API Response

```php
public function show(Article $article): JsonResponse
{
    return response()->json([
        'id' => $article->id,
        'title' => $article->title,
        'body_raw' => $article->body,
        'body_html' => $this->djot->toHtml($article->body),
        'body_text' => $this->djot->toText($article->body),
    ]);
}
```

## Next Steps

- [Configuration](configuration.md) — set up multiple profiles
- [Safe Mode](safe-mode.md) — protect against XSS
