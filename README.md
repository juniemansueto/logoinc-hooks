![Logoinc Hooks](https://raw.githubusercontent.com/juniemansueto/logoinc-hooks/master/logo.png)

<p align="center">
<a href="https://travis-ci.org/larapack/logoinc-hooks"><img src="https://travis-ci.org/larapack/logoinc-hooks.svg?branch=master" alt="Build Status"></a>
<a href="https://styleci.io/repos/76975411/shield?style=flat"><img src="https://styleci.io/repos/76975411/shield?style=flat" alt="Build Status"></a>
<a href="https://packagist.org/packages/larapack/logoinc-hooks"><img src="https://poser.pugx.org/larapack/logoinc-hooks/downloads.svg?format=flat" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/larapack/logoinc-hooks"><img src="https://poser.pugx.org/larapack/logoinc-hooks/v/stable.svg?format=flat" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/larapack/logoinc-hooks"><img src="https://poser.pugx.org/larapack/logoinc-hooks/license.svg?format=flat" alt="License"></a>
</p>

Made with ❤️ by [Junie Mansueto](https://marktopper.com)

# Logoinc Hooks

[Hooks](https://github.com/larapack/hooks) system integrated into [logoinc](https://github.com/ilogo/logoinc).

# Installation

Install using composer:

```
composer require larapack/logoinc-hooks
```

Then add the service provider to the configuration (optional on Laravel 5.5+):

```php
'providers' => [
    Larapack\LogoincHooks\logoincHooksServiceProvider::class,
],
```

In order for logoinc to automatically check for updates of hooks, add the following to your console kernel:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('hook:check')->sundays()->at('03:00');
}
```

That's it! You can now visit your logoinc admin panel and see a new menu item called `Hooks` have been added.
