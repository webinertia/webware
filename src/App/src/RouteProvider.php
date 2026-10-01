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

namespace App;

use App\Container\Configuration;
use Mezzio\Exception\InvalidMiddlewareException;
use Mezzio\MiddlewareFactoryInterface;
use Mezzio\Router\RouteCollectorInterface;
use Mezzio\Router\RouteProviderInterface;
use Override;

/**
 * The App module's routes.
 *
 * A single hardcoded route: the home page. Its path and its name are literals
 * because the module owns them outright — register this class under
 * `router.route-providers` and mezzio's RouteCollectorDelegator calls
 * registerRoutes() before the collector is handed to the router.
 */
final readonly class RouteProvider implements RouteProviderInterface
{
    /**
     * @throws InvalidMiddlewareException when the prepared pipeline is not middleware
     */
    #[Override]
    public function registerRoutes(
        RouteCollectorInterface $routeCollector,
        MiddlewareFactoryInterface $middlewareFactory,
    ): void {
        $routeCollector->get(
            path      : '/',
            middleware: $middlewareFactory->prepare([
                Http\RequestHandler\HomePageHandler::class,
            ]),
            name      : Configuration::getRouteNamePrefix() . 'home',
        )->setOptions([
            'navigation' => 'main',
            'label'      => 'Home',
            'icon'       => 'bi-house-fill',
            'parent'     => null,
            'order'      => 10,
        ]);
    }
}
