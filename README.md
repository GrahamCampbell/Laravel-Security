Laravel Security
================


[![Build Status](https://img.shields.io/travis/GrahamCampbell/Laravel-Security/master.svg?style=flat-square)](https://travis-ci.org/GrahamCampbell/Laravel-Security)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Security.svg?style=flat-square)](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Security.svg?style=flat-square)](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security)
[![Software License](https://img.shields.io/badge/license-Apache%202.0-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version](https://img.shields.io/github/release/GrahamCampbell/Laravel-Security.svg?style=flat-square)](https://github.com/GrahamCampbell/Laravel-Security/releases)


## Introduction

Laravel Security was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and is a port of the security class from [CodeIgniter 2.2](http://ellislab.com/codeigniter) for [Laravel 4.1+](http://laravel.com). This package is best used wrapped in my [Laravel Binput](https://github.com/GrahamCampbell/Laravel-Binput) package. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Security/releases), [license](LICENSE.md), [demo](http://demo.grahamjcampbell.co.uk), [api docs](http://docs.grahamjcampbell.co.uk), and [contribution guidelines](CONTRIBUTING.md).


## Installation

[PHP](https://php.net) 5.4+ or [HHVM](http://hhvm.com) 3.2+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Security, simply require `"graham-campbell/security": "~2.0"` in your `composer.json` file. You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel Security is installed, you need to register the service provider. Open up `app/config/app.php` and add the following to the `providers` key.

* `'GrahamCampbell\Security\SecurityServiceProvider'`

You can register the Security facade in the `aliases` key of your `app/config/app.php` file if you like.

* `'Security' => 'GrahamCampbell\Security\Facades\Security'`


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

The `'clean'` method will parse a string removing xss vulnerabilities. This parsing is strongly based on the security class from [CodeIgniter 2.2](http://ellislab.com/codeigniter).

##### Facades\Security

This facade will dynamically pass static method calls to the `'security'` object in the ioc container which by default is the `Security` class.

##### SecurityServiceProvider

This class contains no public methods of interest. This class should be added to the providers array in `app/config/app.php`. This class will setup ioc bindings.

##### Further Information

Feel free to check out the [API Documentation](http://docs.grahamjcampbell.co.uk) for Laravel Security.

You may see an example of implementation in [Laravel Binput](https://github.com/GrahamCampbell/Laravel-Binput).


## License

Apache License

Copyright 2013-2014 Graham Campbell

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.


## CodeIgniter License

Open Software License ("OSL") v 3.0

Copyright (c) 2008 - 2014, EllisLab, Inc.

All rights reserved.

This license is a legal agreement between you and EllisLab Inc. for the use of CodeIgniter Software (the "Software"). By obtaining the Software you agree to comply with the terms and conditions of this license.

Please see the [license agreement](CODEIGNITER.md).
