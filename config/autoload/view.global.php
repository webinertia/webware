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
 * laminas-view's BasePath helper throws "No base path provided" unless one is configured, and
 * neither `view_manager.base_path` nor `view_helper_config.base_path` was set anywhere in this app,
 * so every layout that calls `$this->basePath(...)` — the ims theme's and the default theme's alike
 * — failed at render time. This app is served from the document root, so the base path is `/`.
 */

return [
    'view_helper_config' => [
        'base_path' => '/',
    ],
];
