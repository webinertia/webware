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

namespace App\Http\RequestHandler;

use Laminas\Diactoros\Exception\InvalidArgumentException;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Renders the home page.
 *
 * The template is an address, not a path: the renderer resolves it, so the
 * active theme decides which file answers it.
 */
final class HomePageHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->template->render(name: 'app::home-page'));
    }
}
