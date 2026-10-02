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

use Webware\Core\AclInterface;

/**
 * Wiring entry point for the package.
 *
 * Declared under `extra.laminas.config-provider` in composer.json, so a consumer's
 * config aggregator merges this without any further registration.
 *
 * @internal
 */
final class ConfigProvider
{
    /** @return array<string, mixed> */
    public function getDependencies(): array
    {
        return [
            'factories'  => [
                Http\RequestHandler\HomePageHandler::class =>
                    Http\RequestHandler\Container\HomePageHandlerFactory::class,
            ],
            'invokables' => [
                RouteProvider::class => RouteProvider::class,
                Acl\RuleSeeds::class => Acl\RuleSeeds::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array<string, mixed>
     */
    public function getTemplates(): array
    {
        return [
            'map'    => [
                'layout::default' => __DIR__ . '/../templates/default/layout/default.phtml',
                'body::default'   => __DIR__ . '/../templates/default/body/default.phtml',
                'app::home-page'  => __DIR__ . '/../templates/default/app/home-page.phtml',
                'error::404'      => __DIR__ . '/../templates/default/error/404.phtml',
                'error::error'    => __DIR__ . '/../templates/default/error/error.phtml',
            ],
            'paths'  => [
                'app'   => [__DIR__ . '/../templates/default/app'],
                'error' => [__DIR__ . '/../templates/default/error'],
            ],
            'layout' => 'layout::default',
        ];
    }

    /**
     * Routes are declared by provider class: mezzio's RouteCollectorDelegator
     * resolves each entry from the container and calls registerRoutes() before
     * handing the collector to the router.
     *
     * @return array{route-providers: list<class-string>}
     */
    private function getRouter(): array
    {
        return [
            'route-providers' => [
                RouteProvider::class,
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return [
            'dependencies'      => $this->getDependencies(),
            'router'            => $this->getRouter(),
            'templates'         => $this->getTemplates(),
            'theme'             => [
                'assets' => ['default' => [
                    'theme.css'    => 'css/theme.css',
                    'logo'         => 'img/webware.png',
                    'app.js'       => 'js/app.js',
                    'messenger.js' => 'js/system.messenger.js',
                ]],
            ],
            AclInterface::class => [
                'rule_seed_providers' => [
                    Acl\RuleSeeds::class,
                ],
            ],
        ];
    }
}
