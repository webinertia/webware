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

namespace AppTest\Http\RequestHandler\Container;

use App\Http\RequestHandler\Container\HomePageHandlerFactory;
use App\Http\RequestHandler\HomePageHandler;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(HomePageHandlerFactory::class)]
#[CoversMethod(HomePageHandlerFactory::class, '__invoke')]
final class HomePageHandlerFactoryTest extends TestCase
{
    #[Test]
    public function injectsTheTemplateRenderer(): void
    {
        $template = $this->createStub(TemplateRendererInterface::class);

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')
            ->willReturnMap([
                [TemplateRendererInterface::class, $template],
            ]);

        self::assertInstanceOf(
            HomePageHandler::class,
            (new HomePageHandlerFactory())($container),
        );
    }
}
