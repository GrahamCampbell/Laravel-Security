<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Security.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
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
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class SecurityTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'security';
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected static function getFacadeClass(): string
    {
        return Facade::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected static function getFacadeRoot(): string
    {
        return Security::class;
    }

    public function testClean(): void
    {
        self::assertSame('<span/>X</span>', Facade::clean('<span/onmouseover=confirm(1)>X</span>'));
        self::assertSame('<p>hi there</p>', Facade::clean('<p>hi there</p>'));
        self::assertSame('<a href="https://styleci.io/">hi there</a>', Facade::clean('<a href="https://styleci.io/">hi there</a>'));
    }

    public function testCleanCustomAttributes(): void
    {
        $this->app->config->set('security.evil.attributes', ['href']);
        self::assertSame('<span/>X</span>', Facade::clean('<span/onmouseover=confirm(1)>X</span>'));
        self::assertSame('<p>hi there</p>', Facade::clean('<p>hi there</p>'));
        self::assertSame('<a >hi there</a>', Facade::clean('<a href="https://styleci.io/">hi there</a>'));
    }

    public function testCleanCustomTags(): void
    {
        $this->app->config->set('security.evil.tags', ['p']);
        self::assertSame('<span/>X</span>', Facade::clean('<span/onmouseover=confirm(1)>X</span>'));
        self::assertSame('&lt;p&gt;hi there&lt;/p&gt;', Facade::clean('<p>hi there</p>'));
        self::assertSame('<a href="https://styleci.io/">hi there</a>', Facade::clean('<a href="https://styleci.io/">hi there</a>'));
    }
}
