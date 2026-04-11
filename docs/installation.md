# Installation

## Requirements

- PHP 8.2 or higher
- Laravel 11.x or 12.x

## Install via Composer

```bash
composer require php-collective/laravel-djot
```

## Package Discovery

Laravel's package auto-discovery will automatically register:

- The `LaravelDjotServiceProvider`
- The `Djot` facade alias

No manual configuration is required to get started.

## Publishing the Config (optional)

If you want to customize the configuration, publish it to `config/djot.php`:

```bash
php artisan vendor:publish --tag=djot-config
```

## Verify Installation

Add a simple test in any Blade view:

```blade
@djot('*Hello* _world_!')
```

This should render:

```html
<p><strong>Hello</strong> <em>world</em>!</p>
```

## Next Steps

- [Configuration](configuration.md) — customize converter behavior
- [Blade Usage](blade-usage.md) — learn the available directives
- [Djot Syntax](djot-syntax.md) — quick reference for Djot markup
