<?php

declare(strict_types=1);

/**
 * This file is part of the Webware package.
 *
 * Copyright (c) 2026 Joey Smith <jsmith@webinertia.net>
 * and contributors.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest\Container;

use App\Container\Configuration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Configuration::class)]
final class ConfigurationTest extends TestCase
{
    #[Test]
    public function componentNameRootsEveryRouteNameTheAppOwns(): void
    {
        self::assertSame('app', Configuration::COMPONENT_NAME);
        self::assertSame('app.', Configuration::getRouteNamePrefix());
        self::assertSame('app', Configuration::getRouteSegment());
        self::assertSame('admin.app.', Configuration::getAdminRouteNamePrefix('admin'));
    }
}
