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
 * Theme settings. Only the active theme is chosen here; the `default` theme itself (its templates and
 * stylesheet) comes from the App config provider, so deleting this file falls back to `default`.
 * Clear the config cache when changing it.
 */

return [
    'theme' => [
        'active' => 'default',
    ],
];
