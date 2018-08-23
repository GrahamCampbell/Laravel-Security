Laravel Security
================

Laravel Security was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and is a port of the security class from [CodeIgniter 3](https://codeigniter.com) for [Laravel 5](http://laravel.com). This package is best used wrapped in my [Laravel Binput](https://github.com/GrahamCampbell/Laravel-Binput) package. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Security/releases), [license](LICENSE), and [contribution guidelines](CONTRIBUTING.md).

![Laravel Security](https://cloud.githubusercontent.com/assets/2829600/4432293/c1126c70-468c-11e4-8552-d0076442bd63.PNG)

<p align="center">
<a href="https://styleci.io/repos/12090755"><img src="https://styleci.io/repos/12090755/shield" alt="StyleCI Status"></img></a>
<a href="https://travis-ci.org/GrahamCampbell/Laravel-Security"><img src="https://img.shields.io/travis/GrahamCampbell/Laravel-Security/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security"><img src="https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/GrahamCampbell/Laravel-Security/releases"><img src="https://img.shields.io/github/release/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Latest Version"></img></a>
</p>


## Installation

Laravel Security requires [PHP](https://php.net) 7.1 or 7.2. This particular version supports Laravel 5.5 - 5.7 only.

To get the latest version, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require graham-campbell/security
```

Once installed, if you are not using automatic package discovery, then you need to register the `GrahamCampbell\Security\SecurityServiceProvider` service provider in your `config/app.php`.

You can also optionally alias our facade:

```php
        'Security' => GrahamCampbell\Security\Facades\Security::class,
```


## Configuration

Laravel Security supports optional configuration.

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish
```

This will create a `config/security.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases.

There is one config option:

##### Evil attributes

This option (`'evil'`) defines the evil attributes and they will be always be removed from the input.


## Usage

##### Security

This is the class of most interest. It is bound to the ioc container as `'security'` and can be accessed using the `Facades\Security` facade. There is one public method of interest.

The `'clean'` method will parse a string removing xss vulnerabilities. This parsing is strongly based on the security class from [CodeIgniter 3](http://www.codeigniter.com/).

##### Facades\Security

This facade will dynamically pass static method calls to the `'security'` object in the ioc container which by default is the `Security` class.

##### SecurityServiceProvider

This class contains no public methods of interest. This class should be added to the providers array in `config/app.php`. This class will setup ioc bindings.

##### Further Information

You may see an example of implementation in [Laravel Binput](https://github.com/GrahamCampbell/Laravel-Binput).


## Security

If you discover a security vulnerability within this package, please send an e-mail to Graham Campbell at graham@alt-three.com. All security vulnerabilities will be promptly addressed.


## License

Laravel Security is licensed under [The MIT License (MIT)](LICENSE).

Laravel Security contains code taken from CodeIgniter, also licensed under [The MIT License (MIT)](CODEIGNITER).
