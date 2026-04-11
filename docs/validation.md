# Validation

The package provides a `ValidDjot` rule to validate that a string contains valid Djot markup.

## Basic Usage

```php
use PhpCollective\LaravelDjot\Rules\ValidDjot;

$request->validate([
    'body' => ['required', 'string', new ValidDjot()],
]);
```

### In Form Requests

```php
use Illuminate\Foundation\Http\FormRequest;
use PhpCollective\LaravelDjot\Rules\ValidDjot;

class StoreArticleRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', new ValidDjot()],
        ];
    }
}
```

## Options

### Strict Mode

When enabled, parse warnings are also treated as validation errors.

```php
new ValidDjot(strict: true)
```

Default: `false`.

### Custom Message

Pass a custom message with the `{error}` placeholder to include the parse error detail:

```php
new ValidDjot(message: 'Please enter valid Djot markup: {error}')
```

The `:attribute` placeholder is still replaced by Laravel, so `The :attribute is not valid Djot markup: {error}` works by default.

## What Gets Validated

The rule checks:

1. **Syntax errors** — malformed Djot that cannot be parsed
2. **Parse warnings** (strict mode only) — valid but potentially problematic markup

### Note on Djot Parsing

Djot is designed to be very forgiving — most input will parse without errors. Unlike strict formats like JSON or YAML, Djot typically produces *some* output even from malformed input.

The validation is most useful for:

- Catching encoding issues
- Detecting truncated input
- Strict mode checking for warnings

## Next Steps

- [Blade Usage](blade-usage.md) — render Djot in views
- [Safe Mode](safe-mode.md) — protect against XSS
