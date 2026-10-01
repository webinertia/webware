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

namespace AppTest;

use App\Http\RequestHandler\HomePageHandler;
use App\RouteProvider;
use Mezzio\MiddlewareFactoryInterface;
use Mezzio\Router\Route;
use Mezzio\Router\RouteCollectorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;

use function is_array;

#[CoversClass(RouteProvider::class)]
#[CoversMethod(RouteProvider::class, 'registerRoutes')]
final class RouteProviderTest extends TestCase
{
    #[Test]
    public function registersASingleGetRouteForTheHomePage(): void
    {
        $middleware = $this->createStub(MiddlewareInterface::class);

        /** @var list<array<class-string>> $prepared */
        $prepared = [];

        $factory = $this->createStub(MiddlewareFactoryInterface::class);
        $factory->method('prepare')
            ->willReturnCallback(
                static function (mixed $pipeline) use (&$prepared, $middleware): MiddlewareInterface {
                    if (is_array($pipeline)) {
                        /** @var array<class-string> $pipeline */
                        $prepared[] = $pipeline;
                    }

                    return $middleware;
                },
            );

        /** @var list<array{string, string|null}> $registered */
        $registered = [];

        $collector = $this->createMock(RouteCollectorInterface::class);
        $collector->expects($this->once())
            ->method('get')
            ->willReturnCallback(
                static function (string $path, MiddlewareInterface $mw, ?string $name = null) use (
                    &$registered,
                ): Route {
                    $registered[] = [$path, $name];

                    return new Route($path, $mw, ['GET'], $name);
                },
            );

        new RouteProvider()->registerRoutes($collector, $factory);

        self::assertSame([['/', 'app.home']], $registered);
        self::assertSame([[HomePageHandler::class]], $prepared);
    }
}
