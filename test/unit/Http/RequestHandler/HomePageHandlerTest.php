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

namespace AppTest\Http\RequestHandler;

use App\Http\RequestHandler\HomePageHandler;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(HomePageHandler::class)]
#[CoversMethod(HomePageHandler::class, '__construct')]
#[CoversMethod(HomePageHandler::class, 'handle')]
final class HomePageHandlerTest extends TestCase
{
    #[Test]
    public function rendersTheHomePageTemplateAddress(): void
    {
        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects($this->once())
            ->method('render')
            ->with('app::home-page')
            ->willReturn('<html>home</html>');

        $response = new HomePageHandler($template)->handle(
            $this->createStub(ServerRequestInterface::class),
        );

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertSame('<html>home</html>', (string) $response->getBody());
    }
}
