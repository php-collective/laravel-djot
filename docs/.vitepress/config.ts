import { defineConfig } from 'vitepress'
import { readFileSync } from 'fs'
import { dirname, resolve } from 'path'
import { fileURLToPath } from 'url'

const __dirname = dirname(fileURLToPath(import.meta.url))

// Load custom Djot grammar for syntax highlighting
const djotGrammar = JSON.parse(
  readFileSync(resolve(__dirname, 'grammars/djot.tmLanguage.json'), 'utf-8')
)

export default defineConfig({
  title: 'Laravel Djot',
  description: 'Djot markup language integration for Laravel — Blade directives, services, validation, and caching',

  base: '/laravel-djot/',

  head: [
    ['link', { rel: 'icon', href: '/laravel-djot/favicon.svg', type: 'image/svg+xml' }],
  ],

  markdown: {
    languages: [
      {
        ...djotGrammar,
        name: 'djot',
        aliases: ['dj', 'Djot'],
      },
    ],
  },

  themeConfig: {
    logo: '/logo.svg',

    nav: [
      { text: 'Guide', link: '/guide/', activeMatch: '/guide/' },
      { text: 'Extensions', link: '/extensions/', activeMatch: '/extensions/' },
      {
        text: 'Links',
        items: [
          { text: 'Playground', link: 'https://sandbox.dereuromark.de/sandbox/djot' },
          { text: 'Demo App', link: 'https://github.com/php-collective/laravel-djot-demo' },
          { text: 'djot-php', link: 'https://php-collective.github.io/djot-php/' },
          { text: 'Symfony Bundle', link: 'https://php-collective.github.io/symfony-djot/' },
          { text: 'Djot Spec', link: 'https://djot.net/' },
          { text: 'Changelog', link: 'https://github.com/php-collective/laravel-djot/releases' },
          { text: 'Packagist', link: 'https://packagist.org/packages/php-collective/laravel-djot' },
          { text: 'Issues', link: 'https://github.com/php-collective/laravel-djot/issues' },
        ],
      },
    ],

    sidebar: {
      '/guide/': [
        {
          text: 'Introduction',
          items: [
            { text: 'Getting Started', link: '/guide/' },
            { text: 'Installation', link: '/guide/installation' },
            { text: 'Configuration', link: '/guide/configuration' },
          ],
        },
        {
          text: 'Usage',
          items: [
            { text: 'Blade Directives', link: '/guide/blade-usage' },
            { text: 'Service Usage', link: '/guide/service-usage' },
            { text: 'Validation', link: '/guide/validation' },
          ],
        },
        {
          text: 'Advanced',
          items: [
            { text: 'Safe Mode', link: '/guide/safe-mode' },
            { text: 'Caching', link: '/guide/caching' },
            { text: 'Djot Syntax', link: '/guide/djot-syntax' },
          ],
        },
      ],
      '/extensions/': [
        {
          text: 'Extensions',
          link: '/extensions/',
        },
      ],
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/php-collective/laravel-djot' },
    ],

    search: {
      provider: 'local',
    },

    editLink: {
      pattern: 'https://github.com/php-collective/laravel-djot/edit/master/docs/:path',
      text: 'Edit this page on GitHub',
    },

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright PHP Collective',
    },
  },
})
