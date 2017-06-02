# Laravel Referer Redirector

[![Latest Version on Packagist](https://img.shields.io/packagist/v/appstract/laravel-referer-redirector.svg?style=flat-square)](https://packagist.org/packages/appstract/laravel-referer-redirector)
[![Total Downloads](https://img.shields.io/packagist/dt/appstract/laravel-referer-redirector.svg?style=flat-square)](https://packagist.org/packages/appstract/laravel-referer-redirector)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/appstract/laravel-referer-redirector/master.svg?style=flat-square)](https://travis-ci.org/appstract/laravel-referer-redirector)

Manage redirects based on referers. You can add start and end dates so a referer can redirect to different url's in different time periods.

## Installation

You can install the package via composer:

```bash
composer require appstract/laravel-referer-redirector
```

### Provider

Then add the ServiceProvider to your `config/app.php` file:

```php
'providers' => [
    ...

    Appstract\RefererRedirector\RefererRedirectorServiceProvider::class

    ...
];
```

### Publish

By running php artisan vendor:publish --provider="Appstract\RefererRedirector\RefererRedirectorServiceProvider" in your project all files for this package will be published. The file that will be published is a migration. A middleware will be registered.

Run the migration:
``` bash
php artisan migrate
```

## Usage

You need to add a middleware to handle the requests.

Add it to single routes:
```php
Route::get('welcome', function () {
    //
})->middleware('redirect-referer');
```

Add it to route groups:
```php
Route::group(['middleware' => ['redirect-referer']], function () {
    //
});
```

Or add it as global middleware:
```php
protected $middleware = [
    ...

    \Appstract\RefererRedirector\Middleware\RedirectReferer::class,

    ...
];
```

## Console
You can add a new redirect based on referer:
``` bash
php artisan referer:make {referer} {redirect} {--start=} {--end=}
```
This will lead you through the needed steps.


Remove referer-redirect based on referer or ID:
``` bash
php artisan referer:remove {referer}
```

List all referer-redirects:
``` bash
php artisan referer:list
```

## Notes

If you're going to add referer-redirects manually to the database, keep the following in mind:
* referer_url: add without http://, https://, ending /
* redirect_url: the package will make a redirect(redirect_url), so make sure this url exists
* start_date: datetime (Carbon)
* end_date: datetime (Carbon)

## Testing

```bash
$ composer test
```

## Contributing

Contributions are welcome, [thanks to y'all](https://github.com/appstract/laravel-referer-redirector/graphs/contributors) :)

## About Appstract

Appstract is a small team from The Netherlands. We create (open source) tools for webdevelopment and write about related subjects on [Medium](https://medium.com/appstract). You can [follow us on Twitter](https://twitter.com/teamappstract), [buy us a beer](https://www.paypal.me/teamappstract/10) or [support us on Patreon](https://www.patreon.com/appstract).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
