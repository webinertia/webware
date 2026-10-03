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

return [
    // Idle lifetime in seconds, renewed on every request; overrides php.ini session.gc_maxlifetime (1440).
    'session' => [
        'gc_maxlifetime' => 28800,
    ],
];
