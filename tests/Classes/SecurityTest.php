<?php

/**
 * This file is part of Laravel Security by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Tests\Security\Classes;

use GrahamCampbell\Security\Classes\Security;
use GrahamCampbell\TestBench\Classes\AbstractTestCase;

/**
 * This is the security test class.
 *
 * @package    Laravel-Security
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Security/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Security
 */
class SecurityTest extends AbstractTestCase
{
    public function testCleanString()
    {
        $security = $this->getSecurity();

        $return = $security->clean('test');

        $this->assertEquals('test', $return);
    }

    public function testCleanArray()
    {
        $security = $this->getSecurity();

        $return = $security->clean(array('test'));

        $this->assertEquals(array('test'), $return);
    }

    protected function getSecurity()
    {
        return new Security();
    }
}
