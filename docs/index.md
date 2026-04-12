---
layout: home

hero:
  name: Laravel Djot
  text: Djot for Laravel
  tagline: Blade directives, services, validation, and caching for the Djot markup language
  image:
    src: /logo.svg
    alt: Laravel Djot
  actions:
    - theme: brand
      text: Get Started
      link: /guide/
    - theme: alt
      text: Extensions
      link: /extensions/
    - theme: alt
      text: View on GitHub
      link: https://github.com/php-collective/laravel-djot

features:
  - icon: "\u2694\uFE0F"
    title: Blade Directives
    details: "@djot, @djotRaw, @djotText \u2014 render Djot markup directly in your Blade views"
  - icon: "\uD83D\uDD12"
    title: Safe Mode
    details: Built-in XSS protection enabled by default for untrusted user input
  - icon: "\uD83C\uDFAD"
    title: Multiple Profiles
    details: Different converter configurations for different contexts (user content, admin, CMS)
  - icon: "\uD83E\uDDE9"
    title: Extensible
    details: 17 built-in extensions \u2014 autolink, mentions, TOC, heading permalinks, and more
  - icon: "\u2705"
    title: Validation
    details: ValidDjot rule for request validation of Djot markup input
  - icon: "\u26A1"
    title: Caching
    details: Optional output caching via any Laravel cache store for better performance
---
