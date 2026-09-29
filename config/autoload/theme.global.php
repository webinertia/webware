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

/*
 * Theme configuration, keyed by theme name and then by the template address it overrides.
 *
 * The App module's own `templates.map` carries the `default` theme, so these are only the
 * overrides: every address the `ims` theme does not list keeps resolving from `default`. Set
 * `active` to `default` to render the baseline instead, and clear the config cache when changing it.
 */

return [
    'theme' => [
        'active' => 'ims',
        'themes' => [
            'ims' => [
                'layout::default' => __DIR__ . '/../../src/App/templates/ims/layout/default.phtml',
                'body::default'   => __DIR__ . '/../../src/App/templates/ims/body/default.phtml',
                'app::home-page'  => __DIR__ . '/../../src/App/templates/ims/app/home-page.phtml',
                'error::404'      => __DIR__ . '/../../src/App/templates/ims/error/404.phtml',
                'error::error'    => __DIR__ . '/../../src/App/templates/ims/error/error.phtml',
            ],
        ],
    ],
];
