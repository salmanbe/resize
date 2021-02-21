Perfect Image Resize
====
This easy to use library to uploads, validates, resize, watermark and save images to disk.

[![License](https://img.shields.io/github/license/salmanbe/resize)](https://github.com/salmanbe/resize/blob/master/LICENSE)

Depenencies
------------
You must install following packages before installing this package.

[Intervention Image](https://github.com/Intervention/image)

[Perfect File Name](https://github.com/salmanbe/filename)

Laravel Installation
-------
Install using composer:
```bash
composer require salmanbe/resize
```

There is a service provider included for integration with the Laravel framework. This service should automatically be registered else to register the service provider, add the following to the providers array in `config/app.php`:

```php
Salmanbe\Resize\ResizeServiceProvider::class,
```
You can also add it as a Facade in `config/app.php`:
```php
'Filename' => Salmanbe\Resize\Resize::class,
```
Global Configuration
-----
Run `php artisan vendor:publish --provider="Salmanbe\Resize\ResizeServiceProvider"` to publish configuration file.

Basic Usage
-----

Add `use Salmanbe\Resize\Resize;` or `use Resize;` at top of the class where you want to use it. Then

```php
$image = new Resize($request->image);
```
```php
$image->resize(public_path('pictures/blog/large/'), 768, 400);
```
Full Documentation
-----

[Follow the link for installation, configuration, options and code examples.](https://www.salman.be/api/resize)

Uninstall
-----
First remove `Salmanbe\Resize\ResizeServiceProvider::class,` and 
`'Filename' => Salmanbe\Resize\Resize::class,` from `config/app.php` if it was added.
Then Run `composer remove salmanbe/resize` 

## License

This library is licensed under THE MIT License. Please see [License File](https://github.com/salmanbe/resize/blob/master/LICENSE) for more information.

## Security contact information

To report a security vulnerability, follow [these steps](https://tidelift.com/security).