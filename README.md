# Laravel temporal key.

[![Packagist License](https://img.shields.io/packagist/l/yaroslawww/laravel-temporal-key?color=%234dc71f)](https://github.com/yaroslawww/laravel-temporal-key/blob/main/LICENSE.md)
[![Packagist Version](https://img.shields.io/packagist/v/yaroslawww/laravel-temporal-key)](https://packagist.org/packages/yaroslawww/laravel-temporal-key)
[![Total Downloads](https://img.shields.io/packagist/dt/yaroslawww/laravel-temporal-key)](https://packagist.org/packages/yaroslawww/laravel-temporal-key)
[![Build Status](https://scrutinizer-ci.com/g/yaroslawww/laravel-temporal-key/badges/build.png?b=main)](https://scrutinizer-ci.com/g/yaroslawww/laravel-temporal-key/build-status/main)
[![Code Coverage](https://scrutinizer-ci.com/g/yaroslawww/laravel-temporal-key/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/yaroslawww/laravel-temporal-key/?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yaroslawww/laravel-temporal-key/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/yaroslawww/laravel-temporal-key/?branch=main)

Create temporal random string key in database for any purposes. Key will be removed after expiration time or after max
retrieve attempts.

## Installation

Install the package via composer:

```shell
composer require yaroslawww/laravel-temporal-key
```

Optionally you can publish the config file with:

```shell
php artisan vendor:publish --provider="TemporalKey\ServiceProvider" --tag="config"
```

Migrate database

```shell
php artisan migrate
```

## Usage

### Default usage

Create key:

```php
$key = \TemporalKey\Manager\TmpKey::create()->key()
// Customize default expiration datetime
$key = \TemporalKey\Manager\TmpKey::create(validUntil: \Carbon\Carbon::now()->addDay())->key()
// Add metadata
$key = \TemporalKey\Manager\TmpKey::create(['email' => 'user@email.com'])->key()
// Customise custom maximal retrieve count.
$key = \TemporalKey\Manager\TmpKey::create(usageMax: 22)->key()
```

Retrieve key

```php
$temporalKey = \TemporalKey\Manager\TmpKey::find('testkey');

$temporalKey?->key();
$temporalKey?->meta();
```

### Crete custom key type

```php
use TemporalKey\Manager\TmpKey;

class ImagePreviewTmpKey extends TmpKey
{
    public static string $type = 'image-preview';
    public static int $defaultValidSeconds = 60 * 60;
    public static int $defaultUsageMax = 0; // unlimited
}

$key = ImagePreviewTmpKey::create()->key()
$temporalKey = ImagePreviewTmpKey::find('testkey');
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/) 
