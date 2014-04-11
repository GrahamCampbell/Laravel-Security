Laravel Security
================


[![Build Status](https://img.shields.io/travis/GrahamCampbell/Laravel-Security/master.svg)](https://travis-ci.org/GrahamCampbell/Laravel-Security)
[![Coverage Status](https://img.shields.io/coveralls/GrahamCampbell/Laravel-Security/master.svg)](https://coveralls.io/r/GrahamCampbell/Laravel-Security)
[![Software License](https://img.shields.io/badge/license-Apache%202.0-brightgreen.svg)](https://github.com/GrahamCampbell/Laravel-Security/blob/master/LICENSE.md)
[![Latest Version](https://img.shields.io/github/release/GrahamCampbell/Laravel-Security.svg)](https://github.com/GrahamCampbell/Laravel-Security/releases)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security/badges/quality-score.png?s=e927889c4b3b569c6c078a797d37d8a847ad9106)](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/b2322c14-a2ef-4074-9b53-1be96faba85c/mini.png)](https://insight.sensiolabs.com/projects/b2322c14-a2ef-4074-9b53-1be96faba85c)


## This Code Comes With A Demo

#### Feel free to take a look at the [demo](http://demo.mineuk.com/) if you'd like to test it out first.


## What Is Laravel Security?

Laravel Security is a port of the security class from [Codeigniter 2.1](http://ellislab.com/codeigniter) for [Laravel 4.1](http://laravel.com). Try the [demo](http://demo.mineuk.com/).

* Laravel Security was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell).
* Laravel Security is heavily based on the security class from [Codeigniter 2.1](http://ellislab.com/codeigniter).
* Laravel Security uses [Travis CI](https://travis-ci.org/GrahamCampbell/Laravel-Security) with [Coveralls](https://coveralls.io/r/GrahamCampbell/Laravel-Security) to check everything is working.
* Laravel Security uses [Scrutinizer CI](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security) and [SensioLabsInsight](https://insight.sensiolabs.com/projects/b2322c14-a2ef-4074-9b53-1be96faba85c) to run additional checks.
* Laravel Security uses [Composer](https://getcomposer.org) to load and manage dependencies.
* Laravel Security provides a [change log](https://github.com/GrahamCampbell/Laravel-Security/blob/master/CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Security/releases), and [api docs](http://grahamcampbell.github.io/Laravel-Security).
* Laravel Security is licensed under the Apache License, available [here](https://github.com/GrahamCampbell/Laravel-Security/blob/master/LICENSE.md).


## System Requirements

* PHP 5.4.7+ or HHVM 3.0+ is required.
* You will need [Laravel 4.1](http://laravel.com) because this package is designed for it.
* You will need [Composer](https://getcomposer.org) installed to load the dependencies of Laravel Security.


## Installation

Please check the system requirements before installing Laravel Security.

To get the latest version of Laravel Security, simply require `"graham-campbell/security": "1.1.*@dev"` in your `composer.json` file. You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel Security is installed, you need to register the service provider. Open up `app/config/app.php` and add the following to the `providers` key.

* `'GrahamCampbell\Security\SecurityServiceProvider'`

You can register the Security facade in the `aliases` key of your `app/config/app.php` file if you like.

* `'Security' => 'GrahamCampbell\Security\Facades\Security'`


## Configuration

Laravel Security requires no configuration. Just follow the simple install instructions and go!


## Usage

**Classes\Security**

This is the class of most interest. It is bound to the ioc container as `'security'` and can be accessed using the `Facades\Security` facade. There is one public method of interest.

The `'clean'` method will parse a string removing xss vulnerabilities. This parsing is strongly based on the security class from [Codeigniter 2.1](http://ellislab.com/codeigniter).

**Facades\Security**

This facade will dynamically pass static method calls to the `'security'` object in the ioc container which by default is the `Classes\Security` class.

**SecurityServiceProvider**

This class contains no public methods of interest. This class should be added to the providers array in `app/config/app.php`. This class will setup ioc bindings.

**Further Information**

Feel free to check out the [API Documentation](http://grahamcampbell.github.io/Laravel-Security
) for Laravel Security. You may see an example of implementation in [Laravel Binput](https://github.com/GrahamCampbell/Laravel-Binput).


## Updating Your Fork

Before submitting a pull request, you should ensure that your fork is up to date.

You may fork Laravel Security:

    git remote add upstream git://github.com/GrahamCampbell/Laravel-Security.git

The first command is only necessary the first time. If you have issues merging, you will need to get a merge tool such as [P4Merge](http://perforce.com/product/components/perforce_visual_merge_and_diff_tools).

You can then update the branch:

    git pull --rebase upstream master
    git push --force origin <branch_name>

Once it is set up, run `git mergetool`. Once all conflicts are fixed, run `git rebase --continue`, and `git push --force origin <branch_name>`.


## Pull Requests

Please review these guidelines before submitting any pull requests.

* When submitting bug fixes, check if a maintenance branch exists for an older series, then pull against that older branch if the bug is present in it.
* Before sending a pull request for a new feature, you should first create an issue with [Proposal] in the title.
* Please follow the [PSR-2 Coding Style](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) and [PHP-FIG Naming Conventions](https://github.com/php-fig/fig-standards/blob/master/bylaws/002-psr-naming-conventions.md).


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
