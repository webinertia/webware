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

namespace App\Container;

use Webware\Core\Configuration as Config;

/**
 * The application's own component name, the root of every route name it owns.
 *
 * @internal
 */
final readonly class Configuration extends Config
{
    public const string COMPONENT_NAME = 'app';
}
