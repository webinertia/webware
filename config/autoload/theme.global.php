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
 * Theme configuration, keyed by theme name and then by the template address it overrides.
 *
 * The App module's own `templates.map` carries the `default` theme, so these are only the
 * overrides: every address the `ims` theme does not list keeps resolving from `default`. Set
 * `active` to `default` to render the baseline instead, and clear the config cache when changing it.
 */

return [
    'theme' => [
        'active' => 'default',
        'themes' => [
            'ims' => [
                'layout::default'           => __DIR__ . '/../../src/App/templates/ims/layout/default.phtml',
                'body::default'             => __DIR__ . '/../../src/App/templates/ims/body/default.phtml',
                'app::home-page'            => __DIR__ . '/../../src/App/templates/ims/app/home-page.phtml',
                'error::404'                => __DIR__ . '/../../src/App/templates/ims/error/404.phtml',
                'error::error'              => __DIR__ . '/../../src/App/templates/ims/error/error.phtml',
                'user::login'               => __DIR__ . '/../../src/App/templates/ims/user/login.phtml',
                'user::registration'        => __DIR__ . '/../../src/App/templates/ims/user/registration.phtml',
                'user::resend-verification' => __DIR__ . '/../../src/App/templates/ims/user/resend-verification.phtml',
                'user::verify-email'        => __DIR__ . '/../../src/App/templates/ims/user/verify-email.phtml',
                'user::admin-widget'        => __DIR__ . '/../../src/App/templates/ims/user/admin-widget.phtml',
                'user::list-users'          => __DIR__ . '/../../src/App/templates/ims/user/list-users.phtml',
                'user::partials/user-row'   => __DIR__ . '/../../src/App/templates/ims/user/partials/user-row.phtml',
                'user::update-user-modal'   => __DIR__ . '/../../src/App/templates/ims/user/update-user-modal.phtml',
                'acl::admin-acl'            => __DIR__ . '/../../src/App/templates/ims/acl/admin-acl.phtml',
                'acl::admin-resources'      => __DIR__ . '/../../src/App/templates/ims/acl/admin-resources.phtml',
                'acl::admin-roles'          => __DIR__ . '/../../src/App/templates/ims/acl/admin-roles.phtml',
                'acl::admin-widget'         => __DIR__ . '/../../src/App/templates/ims/acl/admin-widget.phtml',
                'acl::partials/add-role-modal'        => __DIR__ . '/../../src/App/templates/ims/acl/partials/add-role-modal.phtml',
                'acl::partials/delete-rule-modal'     => __DIR__ . '/../../src/App/templates/ims/acl/partials/delete-rule-modal.phtml',
                'acl::partials/edit-role-modal'       => __DIR__ . '/../../src/App/templates/ims/acl/partials/edit-role-modal.phtml',
                'acl::partials/protect-route-wizard'  => __DIR__ . '/../../src/App/templates/ims/acl/partials/protect-route-wizard.phtml',
                'admin::dashboard'          => __DIR__ . '/../../src/App/templates/ims/admin/dashboard.phtml',
            ],
        ],
        'assets' => [
            // The IMS stylesheet and scripts, served from public/theme/ims/ (see D-014).
            'ims' => [
                'custom.css'   => 'css/custom.css',
                'app.js'       => 'js/app.js',
                'messenger.js' => 'js/system.messenger.js',
            ],
        ],
    ],
];
