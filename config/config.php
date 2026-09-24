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

use App\ConfigProvider;
use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = [
    'config_cache_path' => 'data/cache/config-cache.php',
];

$aggregator = new ConfigAggregator(
    [
        \Laminas\InputFilter\ConfigProvider::class,
        \Laminas\Filter\ConfigProvider::class,
        \Laminas\Validator\ConfigProvider::class,
        \Mezzio\Authentication\ConfigProvider::class,
        \Phly\EventDispatcher\ConfigProvider::class,
        \Mezzio\Session\Ext\ConfigProvider::class,
        \Mezzio\Session\ConfigProvider::class,
        \Mezzio\LaminasView\ConfigProvider::class,
        \Laminas\View\ConfigProvider::class,
        \Laminas\Hydrator\ConfigProvider::class,
        Laminas\ServiceManager\ConfigProvider::class,
        Mezzio\Router\FastRouteRouter\ConfigProvider::class,
        Mezzio\Helper\ConfigProvider::class,
        Mezzio\Router\ConfigProvider::class,
        Mezzio\ConfigProvider::class,
        Laminas\Diactoros\ConfigProvider::class,
        Laminas\HttpHandlerRunner\ConfigProvider::class,
        \PhpDb\ConfigProvider::class,
        \PhpDb\Mysql\ConfigProvider::class,
        \Webware\Core\ConfigProvider::class,
        \Webware\Console\ConfigProvider::class,
        \Webware\Acl\ConfigProvider::class,
        \Webware\Htmx\ConfigProvider::class,
        \Webware\Log\ConfigProvider::class,
        \Webware\Mailer\ConfigProvider::class,
        \Webware\Message\ConfigProvider::class,
        \Webware\MessageBus\ConfigProvider::class,
        \Webware\UserManager\ConfigProvider::class,
        // Include cache configuration
        new ArrayProvider($cacheConfig),
        // Default App module config
        ConfigProvider::class,
        // Load application config in a pre-defined order in such a way that local settings
        // overwrite global settings. (Loaded as first to last):
        //   - `global.php`
        //   - `*.global.php`
        //   - `local.php`
        //   - `*.local.php`
        new PhpFileProvider(realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'),
        // Load development config if it exists
        new PhpFileProvider(realpath(__DIR__) . '/development.config.php'),
    ],
    $cacheConfig['config_cache_path'],
);

return $aggregator->getMergedConfig();
