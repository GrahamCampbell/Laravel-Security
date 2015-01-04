Laravel Security
================

Laravel Security was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and is a port of the security class from [CodeIgniter 3](http://ellislab.com/codeigniter) for [Laravel 4.1/4.2](http://laravel.com). This package is best used wrapped in my [Laravel Binput](https://github.com/GrahamCampbell/Laravel-Binput) package. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Security/releases), [license](LICENSE), [demo](http://demo.grahamjcampbell.co.uk), [api docs](http://docs.grahamjcampbell.co.uk), and [contribution guidelines](CONTRIBUTING.md).

![Laravel Security](https://cloud.githubusercontent.com/assets/2829600/4432291/c10d5dd4-468c-11e4-9617-75271f185f63.PNG)

<p align="center">
<a href="https://travis-ci.org/GrahamCampbell/Laravel-Security"><img src="https://img.shields.io/travis/GrahamCampbell/Laravel-Security/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security"><img src="https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/GrahamCampbell/Laravel-Security/releases"><img src="https://img.shields.io/github/release/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Latest Version"></img></a>
</p>

## Installation

[PHP](https://php.net) 5.4+ or [HHVM](http://hhvm.com) 3.3+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Security, simply add the following line to the require block of your `composer.json` file:

```
"graham-campbell/security": "~2.0"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel Security is installed, you need to register the service provider. Open up `app/config/app.php` and add the following to the `providers` key.

* `'GrahamCampbell\Security\SecurityServiceProvider'`

You can register the Security facade in the `aliases` key of your `app/config/app.php` file if you like.

* `'Security' => 'GrahamCampbell\Security\Facades\Security'`

#### Looking for a laravel 5 compatable version?

Checkout the [master branch](https://github.com/GrahamCampbell/Laravel-Security/tree/master), installable by requiring `"graham-campbell/security": "~3.0"`.


## Configuration

Laravel Security supports optional configuration.

To get started, first publish the package config file:

```bash
$ php artisan config:publish graham-campbell/security
```

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

This class contains no public methods of interest. This class should be added to the providers array in `app/config/app.php`. This class will setup ioc bindings.

##### Further Information

Feel free to check out the [API Documentation](http://docs.grahamjcampbell.co.uk) for Laravel Security.

You may see an example of implementation in [Laravel Binput](https://github.com/GrahamCampbell/Laravel-Binput).


## License

Laravel Security is licensed under [The MIT License (MIT)](LICENSE).

Laravel Security contains code taken from CodeIgniter, also licensed under [The MIT License (MIT)](CODEIGNITER).
