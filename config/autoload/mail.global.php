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

use Webware\Mailer\Adapter\AdapterInterface;
use Webware\Mailer\Adapter\PhpMailer;

return [
    'dependencies' => [
        'aliases' => [
            AdapterInterface::class => PhpMailer::class,
        ],
    ],
    // Mailpit from compose.yml, published to the host.
    AdapterInterface::class => [
        'useSmtp'  => true,
        'host'     => '127.0.0.1',
        'port'     => 1025,
        'smtpAuth' => false,
    ],
];
