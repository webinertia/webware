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

namespace App\Acl;

use App\Container\Configuration;
use Override;
use Webware\Core\Acl\RuleSeed;
use Webware\Core\Acl\RuleSeedProviderInterface;
use Webware\Core\Acl\RuleType;
use Webware\Core\Role;

/**
 * The policy for the routes the application owns: the home page is public.
 *
 * Member and above reach it through role inheritance from Guest, so no other
 * row is needed. Without this row the home route denies every role, and the
 * forbidden handler's redirect to the login page loops.
 *
 * @internal
 */
final readonly class RuleSeeds implements RuleSeedProviderInterface
{
    /**
     * @return list<RuleSeed>
     */
    #[Override]
    public function ruleSeeds(string $adminName): array
    {
        return [
            new RuleSeed(
                type      : RuleType::Allow,
                roleId    : Role::Guest->value,
                resourceId: Configuration::getRouteNamePrefix() . 'home',
            ),
        ];
    }
}
