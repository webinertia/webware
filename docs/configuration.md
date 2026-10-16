# Configuration Reference

Everything this application must be configured with, where each value is read from, and how to
override a value a component supplies.

This is the configuration of record for the application skeleton. Keys a component owns are
documented here only to the depth the application needs, with a link to the component's own
reference where one exists.

## How the config array is assembled

`config/config.php` merges every source into one array with `laminas-config-aggregator`. Later
sources win, so a later source overrides an earlier one key by key:

1. The component `ConfigProvider`s, in the order listed in `config/config.php`. Order matters
   where two providers own the same service name, and the list carries inline comments marking
   the places where it does.
2. `App\ConfigProvider`, this application's own wiring.
3. `config/autoload/{{,*.}global,{,*.}local}.php`, which expands to `global.php`, then
   `*.global.php`, then `local.php`, then `*.local.php`.
4. `config/development.config.php`, which exists only in development mode.

Two consequences worth knowing before you look for a value:

- Any key a component supplies can be overridden from `config/autoload/`, because the autoload
  files load after every provider.
- Development mode loads last, so it always wins. It forces `debug` on and the config cache off.

### Committed versus local files

`config/autoload/.gitignore` ignores `local.php` and `*.local.php`.

| File pattern | Tracked | Use it for |
|---|---|---|
| `*.global.php` | yes | defaults that apply to every host |
| `*.local.php` | **no** | credentials and host-specific values |
| `development.config.php` | **no** | generated from `development.config.php.dist` by `dev:mode` |

A value that differs per host, or that is a secret, belongs in a `*.local.php` file. Anything in a
`*.global.php` is committed and must be safe to publish.

## Required configuration

Six things have no working default. Without them the application either does not boot or does not
serve a page.

| What | Key | File |
|---|---|---|
| Database connection | `PhpDb\Adapter\AdapterInterface::class` | `config/autoload/mysql.local.php` |
| Debug flag | `debug` | `config/autoload/webware.global.php` |
| View base path | `view_helper_config.base_path` | `config/autoload/view.global.php` |
| User URLs and emails | `Webware\Core\UserInterface::class` | `config/autoload/user.global.php` |
| Mail transport | `Webware\Mailer\Adapter\AdapterInterface::class` | `config/autoload/mail.global.php` |
| Session lifetime | `session.gc_maxlifetime` | `config/autoload/session.global.php` |

### Database connection

`config/autoload/mysql.local.php` is **not in git**, and no `.dist` template ships for it, so a
fresh clone has no database configuration at all. Create the file with this shape:

```php
<?php

declare(strict_types=1);

use Laminas\ServiceManager\Factory\InvokableFactory;
use PhpDb\Adapter\AdapterInterface;
use PhpDb\Adapter\Profiler\Profiler;
use PhpDb\Adapter\Profiler\ProfilerInterface;
use PhpDb\Mysql\Pdo\Driver;

return [
    AdapterInterface::class => [
        'driver'     => Driver::class,
        'connection' => [
            'dbname'   => 'webware_app',
            'host'     => '127.0.0.1',
            'port'     => '3306',
            'user'     => 'webware_app',
            'password' => 'webware_app',
        ],
    ],
    'dependencies'          => [
        'aliases'   => [
            ProfilerInterface::class => Profiler::class,
        ],
        'factories' => [
            Profiler::class => InvokableFactory::class,
        ],
    ],
];
```

The `ProfilerInterface` alias and the `Profiler` factory are part of the requirement, not
decoration: the Tracy panel integration resolves them.

The values above match the `mysql` service in `compose.yml`, which publishes port 3306 to the
host. The host address is `127.0.0.1` so the connection resolves when PHP runs on the host; from
inside the `tooling` container the service name `mysql` is the address.

The database, user and password are all named `webware_app` deliberately. Component test suites
use a database named `webware` and may drop it, so the application is kept out of that name.

The connection is required for more than the user pages. `PhpDb\Session\SessionProvider` is
aggregated after the two Mezzio session providers so it can re-point `SessionPersistenceInterface`
at the database, which means sessions are database backed. An anonymous visit needs a working
connection, not only a signed-in one.

### Debug flag

`config/pipeline.php` reads `$container->get('config')['debug']` to decide whether Traccio or
Mezzio's `ErrorHandler` is the outermost middleware. The key has to exist. `development.config.php`
sets it true; a host that is not in development mode sets it false.

### View base path

`laminas-view`'s `BasePath` helper throws `No base path provided` unless one is configured, and
neither `view_manager.base_path` nor `view_helper_config.base_path` is set anywhere in the
installed components. Every layout calls `$this->basePath(...)`, so without this key no page
renders. This application is served from the document root, so the value is `/`.

### User URLs and emails

Three keys under `Webware\Core\UserInterface::class`, read by webware-usermanager:

| Key | Type | Notes |
|---|---|---|
| `base_url` | `string` | absolute URL the verification links are built from |
| `verification_email_subject` | `string` | subject line of the verification email |
| `verification_token_ttl` | `int` | token lifetime in seconds; `86400` is one day |

webware-usermanager supplies a fourth key in the same array, `login_path`, and this application
does not override it.

### Mail transport

`Webware\Mailer\Adapter\AdapterInterface::class` declares the transport. The committed values
target Mailpit from `compose.yml`, published to the host on port 1025:

| Key | Value | Notes |
|---|---|---|
| `useSmtp` | `true` | |
| `host` | `127.0.0.1` | service name `mailpit` from inside the container |
| `port` | `1025` | Mailpit's SMTP port |
| `smtpAuth` | `false` | Mailpit accepts unauthenticated mail by default |

Registration and password reset both send mail, so verification links do not arrive without it.

### Session lifetime

`session.gc_maxlifetime` in seconds, renewed on every request. The committed value is `28800`, which
is eight hours, and it overrides `php.ini`'s `session.gc_maxlifetime` of 1440.

## Database schema

Four tables have to exist before the application is useful. Each is created by a console command
the component owning the table registers.

| Command | Creates |
|---|---|
| `user:init-db` | the `user` table, and seeds an initial user |
| `session:init-db` | the table database session persistence writes to |
| `log:init-db` | the `log` table webware-log writes to |
| `acl:init-db` | `acl_role` and `acl_rule`, and seeds the base roles and rules |
| `acl:seed` | rewrites the ACL rules every installed component publishes |

Run them through the console host, `composer menu`, or directly:

```shell
vendor/bin/webware user:init-db
vendor/bin/webware session:init-db
vendor/bin/webware log:init-db
vendor/bin/webware acl:init-db
```

The commands read the same `PhpDb\Adapter\AdapterInterface` configuration as the application, so
the database connection has to be working first: they cannot bootstrap the database for you.

Order matters only in that `acl:init-db` seeds the base roles while `acl:seed` re-publishes the
rules the installed components declare, so run `acl:seed` again after installing or removing a
component that ships `RuleSeeds`.

## Configuration the components supply

These keys are supplied by a component and can be overridden from `config/autoload/`. They are
listed here so the surface is visible from one place, not because the application sets them.

| Key | Owner | Holds |
|---|---|---|
| `authentication` | webware-usermanager | `redirect`, `username`, `password`, `post_login_redirect` |
| `Webware\Core\UserInterface::class` | webware-usermanager | `login_path`, plus the three keys above |
| `Webware\Core\AclInterface::class` | webware-usermanager and `App\ConfigProvider` | `roles`, `resources`, `rule_seed_providers` |
| `Webware\MessageBus\MessageBusInterface::class` | webware-usermanager | command map and query map |
| `Psr\Log\LoggerInterface::class` | webware-log | `channel`, `log_errors`, `process_uuid`, `process_translation`, `table` |
| `theme` | webware-theme, `App\ConfigProvider` | `active`, `assets`, and the `themes` maps |
| `templates`, `view_helpers`, `input_filters`, `router` | several | per component |

Two of these deserve a note.

`authentication.username` is the identity field. webware-log writes the authenticated user into a
log record under that name, and reads it back out of the same name to fill the `user_identifier`
column, so changing it moves both halves at once. See the webware-log configuration reference.

`theme.active` is read once by webware-theme's `ThemeResolverFactory` and defaults to `default`.
Deleting `config/autoload/theme.settings.global.php` therefore falls back to the `default` theme
rather than failing, because `App\ConfigProvider` supplies that theme's templates and assets.

## Optional and development settings

| Setting | File | Default | Notes |
|---|---|---|---|
| `ConfigAggregator::ENABLE_CACHE` | `config/autoload/webware.global.php` | `true` | The cache target is `data/cache/config-cache.php`, held in git by `.gitkeep` only |
| `debug` | `config/autoload/webware.global.php` | `false` | Development mode forces it true |
| `Tracy\Debugger::class` | `config/development.config.php` | Development | Merged only when traccio is installed |

Development mode is driven by `dev:mode`, which copies `development.config.php.dist` over
`development.config.php` and drops the config cache on every change:

```shell
vendor/bin/webware dev:mode --enable       # copy the dist file, drop the config cache
vendor/bin/webware dev:mode --disable      # remove the active file, drop the config cache
vendor/bin/webware dev:mode --clear-cache  # drop the config cache without changing the mode
```

Editing any configuration file has no effect while the cache is on. Clear it, or toggle
development mode, then reload.

## Where to look next

- [README](../README.md) for what ships where and how to run the toolchain.
- webware-log's configuration reference, in `vendor/webware/webware-log/docs/configuration.md`,
  for the logging keys in full.
