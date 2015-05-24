Laravel Security
================

Laravel Security was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and is a port of the security class from [CodeIgniter 3](http://ellislab.com/codeigniter) for [Laravel 5](http://laravel.com). This package is best used wrapped in my [Laravel Binput](https://github.com/GrahamCampbell/Laravel-Binput) package. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Security/releases), [license](LICENSE), [api docs](https://docs.gjcampbell.co.uk), and [contribution guidelines](CONTRIBUTING.md).

![Laravel Security](https://cloud.githubusercontent.com/assets/2829600/4432293/c1126c70-468c-11e4-8552-d0076442bd63.PNG)

<p align="center">
<a href="https://travis-ci.org/GrahamCampbell/Laravel-Security"><img src="https://img.shields.io/travis/GrahamCampbell/Laravel-Security/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security"><img src="https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/GrahamCampbell/Laravel-Security/releases"><img src="https://img.shields.io/github/release/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Latest Version"></img></a>
</p>


## Installation

[PHP](https://php.net) 5.5+ or [HHVM](http://hhvm.com) 3.6+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Security, simply add the following line to the require block of your `composer.json` file:

```
"graham-campbell/security": "~3.1"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel Security is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key.

* `'GrahamCampbell\Security\SecurityServiceProvider'`

You can register the Security facade in the `aliases` key of your `config/app.php` file if you like.

* `'Security' => 'GrahamCampbell\Security\Facades\Security'`


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

Feel free to check out the [API Documentation](https://docs.gjcampbell.co.uk) for Laravel Security.

You may see an example of implementation in [Laravel Binput](https://github.com/GrahamCampbell/Laravel-Binput).


## License

Laravel Security is licensed under [The MIT License (MIT)](LICENSE).

Laravel Security contains code taken from CodeIgniter, also licensed under [The MIT License (MIT)](CODEIGNITER).
