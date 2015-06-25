<?php

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
use GrahamCampbell\Security\Security;
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
     * Get the facade route.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return Security::class;
    }
}
