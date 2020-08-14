<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Security.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Security\Facades;

use GrahamCampbell\Security\Facades\Security as Facade;
use GrahamCampbell\SecurityCore\Security;
use GrahamCampbell\TestBenchCore\FacadeTrait;
use GrahamCampbell\Tests\Security\AbstractTestCase;

/**
 * This is the security facade test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class SecurityTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected function getFacadeAccessor()
    {
        return 'security';
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected function getFacadeClass()
    {
        return Facade::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return Security::class;
    }

    public function testClean()
    {
        $this->assertSame('<span/>X</span>', Facade::clean('<span/onmouseover=confirm(1)>X</span>'));
        $this->assertSame('<p>hi there</p>', Facade::clean('<p>hi there</p>'));
        $this->assertSame('<a href="https://styleci.io/">hi there</a>', Facade::clean('<a href="https://styleci.io/">hi there</a>'));
    }

    public function testCleanCustomAttributes()
    {
        $this->app->config->set('security.evil.attributes', ['href']);
        $this->assertSame('<span/>X</span>', Facade::clean('<span/onmouseover=confirm(1)>X</span>'));
        $this->assertSame('<p>hi there</p>', Facade::clean('<p>hi there</p>'));
        $this->assertSame('<a >hi there</a>', Facade::clean('<a href="https://styleci.io/">hi there</a>'));
    }

    public function testCleanCustomTags()
    {
        $this->app->config->set('security.evil.tags', ['p']);
        $this->assertSame('<span/>X</span>', Facade::clean('<span/onmouseover=confirm(1)>X</span>'));
        $this->assertSame('&lt;p&gt;hi there&lt;/p&gt;', Facade::clean('<p>hi there</p>'));
        $this->assertSame('<a href="https://styleci.io/">hi there</a>', Facade::clean('<a href="https://styleci.io/">hi there</a>'));
    }
}
