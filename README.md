Laravel Security
================

Laravel Security was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and is a [voku/anti-xss](https://github.com/voku/anti-xss) wrapper for [Laravel](http://laravel.com), using [graham-campbell/security-core](https://github.com/GrahamCampbell/Security-Core). Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Security/releases), [security policy](https://github.com/GrahamCampbell/Laravel-Security/security/policy), [license](LICENSE), [code of conduct](.github/CODE_OF_CONDUCT.md), and [contribution guidelines](.github/CONTRIBUTING.md).

![Banner](https://user-images.githubusercontent.com/2829600/71477506-68a5a600-27e2-11ea-8c23-84dc5b8e3915.png)

<p align="center">
<a href="https://styleci.io/repos/12090755"><img src="https://styleci.io/repos/12090755/shield" alt="StyleCI Status"></img></a>
<a href="https://travis-ci.org/GrahamCampbell/Laravel-Security"><img src="https://img.shields.io/travis/GrahamCampbell/Laravel-Security/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security"><img src="https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/GrahamCampbell/Laravel-Security/releases"><img src="https://img.shields.io/github/release/GrahamCampbell/Laravel-Security.svg?style=flat-square" alt="Latest Version"></img></a>
</p>


## Installation

Laravel Security requires [PHP](https://php.net) 7.1-7.4. This particular version supports Laravel 5.5-7.

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

There are two config options:

##### Evil attributes

This option (`'evil'`) defines the evil attributes, which will always be stripped from the input.

##### Replacement string

This option (`'replacement'`) defines the replacement string, which will be used to take the place of removed portions of strings where XSS was present.


## Usage

##### Security

This is the class of most interest. It is bound to the ioc container as `'security'` and can be accessed using the `Facades\Security` facade. There is one public method of interest.

The `'clean'` method will parse a string removing XSS vulnerabilities, on a best effort basis.

##### Facades\Security

This facade will dynamically pass static method calls to the `'security'` object in the ioc container which by default is the `Security` class.

##### SecurityServiceProvider

This class contains no public methods of interest. This class should be added to the providers array in `config/app.php`. This class will setup ioc bindings.

##### Further Information

You may see an example of implementation in [Laravel Binput](https://github.com/GrahamCampbell/Laravel-Binput).


## Security

If you discover a security vulnerability within this package, please send an email to Graham Campbell at graham@alt-three.com. All security vulnerabilities will be promptly addressed. You may view our full security policy [here](https://github.com/GrahamCampbell/Laravel-Security/security/policy).


## License

Laravel Security is licensed under [The MIT License (MIT)](LICENSE).


---

<div align="center">
	<b>
		<a href="https://tidelift.com/subscription/pkg/packagist-graham-campbell-security?utm_source=packagist-graham-campbell-security&utm_medium=referral&utm_campaign=readme">Get professional support for Laravel Security with a Tidelift subscription</a>
	</b>
	<br>
	<sub>
		Tidelift helps make open source sustainable for maintainers while giving companies<br>assurances about security, maintenance, and licensing for their dependencies.
	</sub>
</div>
