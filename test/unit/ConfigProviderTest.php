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

namespace AppTest;

use App\Acl\RuleSeeds;
use App\ConfigProvider;
use App\Http\RequestHandler\Container\HomePageHandlerFactory;
use App\Http\RequestHandler\HomePageHandler;
use App\RouteProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Core\AclInterface;

use function dirname;

#[CoversClass(ConfigProvider::class)]
#[CoversMethod(ConfigProvider::class, '__invoke')]
final class ConfigProviderTest extends TestCase
{
    #[Test]
    public function providesDependenciesRoutesAndTemplates(): void
    {
        $app =
            dirname(
                path  : __DIR__,
                levels: 2,
            ) . '/src/App/src';

        $expected = [
            'dependencies'      => [
                'factories'  => [
                    HomePageHandler::class => HomePageHandlerFactory::class,
                ],
                'invokables' => [
                    RouteProvider::class => RouteProvider::class,
                    RuleSeeds::class     => RuleSeeds::class,
                ],
            ],
            'router'            => [
                'route-providers' => [
                    RouteProvider::class,
                ],
            ],
            'templates'         => [
                'map'    => [
                    'layout::default' => "{$app}/../templates/default/layout/default.phtml",
                    'body::default'   => "{$app}/../templates/default/body/default.phtml",
                    'app::home-page'  => "{$app}/../templates/default/app/home-page.phtml",
                    'error::404'      => "{$app}/../templates/default/error/404.phtml",
                    'error::error'    => "{$app}/../templates/default/error/error.phtml",
                ],
                'paths'  => [
                    'app'   => ["{$app}/../templates/default/app"],
                    'error' => ["{$app}/../templates/default/error"],
                ],
                'layout' => 'layout::default',
            ],
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
                    RuleSeeds::class,
                ],
            ],
        ];

        self::assertSame($expected, new ConfigProvider()->__invoke());
    }
}
