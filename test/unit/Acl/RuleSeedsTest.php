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

namespace AppTest\Acl;

use App\Acl\RuleSeeds;
use App\RouteProvider;
use Mezzio\MiddlewareFactoryInterface;
use Mezzio\Router\Route;
use Mezzio\Router\RouteCollectorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Webware\Core\Acl\RuleSeedProviderInterface;
use Webware\Core\Acl\RuleType;
use Webware\Core\Role;

#[CoversClass(RuleSeeds::class)]
#[CoversMethod(RuleSeeds::class, 'ruleSeeds')]
final class RuleSeedsTest extends TestCase
{
    #[Test]
    public function allowsGuestTheHomeRoute(): void
    {
        $seeds = new RuleSeeds()->ruleSeeds('admin');

        self::assertCount(1, $seeds);
        self::assertSame(RuleType::Allow, $seeds[0]->type);
        self::assertSame(Role::Guest->value, $seeds[0]->roleId);
        self::assertSame('app.home', $seeds[0]->resourceId);
        self::assertNull($seeds[0]->parentResourceId);
    }

    #[Test]
    public function isAPublishableRuleSeedProvider(): void
    {
        self::assertInstanceOf(RuleSeedProviderInterface::class, new RuleSeeds());
    }

    #[Test]
    public function theSeededResourceIsTheRouteTheAppRegisters(): void
    {
        /** @var list<string|null> $names */
        $names = [];

        $collector = $this->createStub(RouteCollectorInterface::class);
        $collector->method('get')
            ->willReturnCallback(
                static function (string $path, MiddlewareInterface $middleware, ?string $name = null) use (
                    &$names,
                ): Route {
                    $names[] = $name;

                    return new Route($path, $middleware, ['GET'], $name);
                },
            );

        $factory = $this->createStub(MiddlewareFactoryInterface::class);
        $factory->method('prepare')->willReturn($this->createStub(MiddlewareInterface::class));

        new RouteProvider()->registerRoutes($collector, $factory);

        self::assertSame([new RuleSeeds()->ruleSeeds('admin')[0]->resourceId], $names);
    }
}
