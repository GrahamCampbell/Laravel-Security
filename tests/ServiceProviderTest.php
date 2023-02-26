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

namespace GrahamCampbell\Tests\Security;

use GrahamCampbell\SecurityCore\Security;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testSecurityIsInjectable(): void
    {
        self::assertIsInjectable(Security::class);
    }
}
