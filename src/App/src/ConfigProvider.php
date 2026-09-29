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
    /**
     * Returns the templates configuration
     *
     * @return array<string, mixed>
     */
    public function getTemplates(): array
    {
        return [
            'map'            => [
                'layout::default' => __DIR__ . '/../templates/default/layout/default.phtml',
                'body::default'   => __DIR__ . '/../templates/default/body/default.phtml',
                'app::home-page'  => __DIR__ . '/../templates/default/app/home-page.phtml',
                'error::404'      => __DIR__ . '/../templates/default/error/404.phtml',
                'error::error'    => __DIR__ . '/../templates/default/error/error.phtml',
            ],
            'paths'          => [
                'app'   => [__DIR__ . '/../templates/default/app'],
                'error' => [__DIR__ . '/../templates/default/error'],
            ],
            'layout'         => 'layout::default',
        ];
    }

    /** @return array<string, mixed> */
    private function getDependencies(): array
    {
        return [
            'factories' => [],
        ];
    }

    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }
}
