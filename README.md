Laravel Security
================


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/GrahamCampbell/Laravel-Security/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
[![Build Status](https://travis-ci.org/GrahamCampbell/Laravel-Security.png?branch=master)](https://travis-ci.org/GrahamCampbell/Laravel-Security)
[![Latest Version](https://poser.pugx.org/graham-campbell/security/v/stable.png)](https://packagist.org/packages/graham-campbell/security)
[![Total Downloads](https://poser.pugx.org/graham-campbell/security/downloads.png)](https://packagist.org/packages/graham-campbell/security)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security/badges/quality-score.png?s=e927889c4b3b569c6c078a797d37d8a847ad9106)](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security)
[![Still Maintained](http://stillmaintained.com/GrahamCampbell/Laravel-Security.png)](http://stillmaintained.com/GrahamCampbell/Laravel-Security)


## What Is Laravel Security?

Laravel Security is a port of the security class from [Codeigniter 2.1](http://ellislab.com/codeigniter) for [Laravel 4.1](http://laravel.com).  

* Laravel Security was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell).  
* Laravel Security is heavily based on the security class from [Codeigniter 2.1](http://ellislab.com/codeigniter).  
* Laravel Security uses [Travis CI](https://travis-ci.org/GrahamCampbell/Laravel-Security) to run tests to check if it's working as it should.  
* Laravel Security uses [Scrutinizer CI](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Security) to run additional tests and checks.  
* Laravel Security uses [Composer](https://getcomposer.org) to load and manage dependencies.  
* Laravel Security provides a [change log](https://github.com/GrahamCampbell/Laravel-Security/blob/master/CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Security/releases), and a [wiki](https://github.com/GrahamCampbell/Laravel-Security/wiki).  
* Laravel Security is licensed under the Apache License, available [here](https://github.com/GrahamCampbell/Laravel-Security/blob/master/LICENSE.md).  


## System Requirements

* PHP 5.4.7+ or PHP 5.5+ is required.
* You will need [Laravel 4.1](http://laravel.com) because this package is designed for it.  
* You will need [Composer](https://getcomposer.org) installed to load the dependencies of Laravel Security.  


## Installation

Please check the system requirements before installing Laravel Security.  

To get the latest version of Laravel Security, simply require it in your `composer.json` file.

`"graham-campbell/security": "dev-master"`

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel Security is installed, you need to register the service provider. Open up `app/config/app.php` and add the following to the `providers` key.

`'GrahamCampbell\Security\SecurityServiceProvider'`

You can register the Security facade in the `aliases` key of your `app/config/app.php` file if you like.

`'Security' => 'GrahamCampbell\Security\Facades\Security'`


## Updating Your Fork

The latest and greatest source can be found on [GitHub](https://github.com/GrahamCampbell/Laravel-Security).  
Before submitting a pull request, you should ensure that your fork is up to date.  

You may fork Laravel Security:  

    git remote add upstream git://github.com/GrahamCampbell/Laravel-Security.git

The first command is only necessary the first time. If you have issues merging, you will need to get a merge tool such as [P4Merge](http://perforce.com/product/components/perforce_visual_merge_and_diff_tools).  

You can then update the branch:  

    git pull --rebase upstream develop
    git push --force origin <branch_name>

Once it is set up, run `git mergetool`. Once all conflicts are fixed, run `git rebase --continue`, and `git push --force origin <branch_name>`.  


## Pull Requests

Please submit pull requests against the develop branch.  

* Any pull requests made against the master branch will be closed immediately.  
* If you plan to fix a bug, please create a branch called `fix-`, followed by an appropriate name.  
* If you plan to add a feature, please create a branch called `feature-`, followed by an appropriate name.  
* Please indent with 4 spaces rather than tabs, and make sure your code is commented.  


## License

Apache License  

Copyright 2013 Graham Campbell  

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at  

 http://www.apache.org/licenses/LICENSE-2.0  

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.  
