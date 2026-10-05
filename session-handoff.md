# Session handoff — 2026-10-05

Written at the end of the WSL-side session, for the next session running on **Windows + VS Code**.
Anything below is **measured** (a command was run and its output read) unless explicitly marked
**unverified**.

## Why this handoff exists

The dev environment is moving off VS Code's WSL remote. A BYOK model is not selectable for WSL
workspaces — upstream `microsoft/vscode#332085`, acknowledged as not-user-error and backlogged with
no fix intent. Step debugging is a standing requirement, so this is not optional.

Target: **Windows VS Code with all PHP inside Docker** — step debugging, PHPUnit and Infection
included — backed by each repo's `compose.yml`, so developers without VS Code use the identical
environment through plain `docker compose`.

## The first thing to know: the standing rules do not follow you

The user-memory files that constrain how work is done here live under
`/home/jsmith/.vscode-server/data/User/globalStorage/github.copilot-chat/memory-tool/memories/` —
that is **WSL VS Code server storage**. On the Windows side they will be absent (or hold different
content). The rules below are therefore copied in here deliberately; do not rely on memory being
present. Where they came from is noted so they can be re-established.

## Measured state at handoff

- Every repo under `~/github.com/webinertia` is on its default branch and in sync with `origin`.
- **Docker Desktop is the engine, and it is shared between Windows and WSL.** `docker context ls`
  shows `desktop-linux` → `npipe:////./pipe/dockerDesktopLinuxEngine`; `/var/run/docker.sock` exists
  in WSL; `docker info` reports `os=Docker Desktop`, server `29.8.1`, driver `overlayfs`. A compose
  file behaves identically from either side — no compose rewrite is needed for parity.
- `webware/Dockerfile` installs **pcov and xdebug**. Xdebug is parked at `mode=off` so ordinary runs
  pay nothing, and is switched on per invocation with `XDEBUG_MODE`. Image config:
  `xdebug.mode=off`, `xdebug.start_with_request=yes`, `xdebug.client_host=host.docker.internal`.
- `webware/Dockerfile` and `webware/.devcontainer/devcontainer.json` are **byte-identical to the
  `webware-tools` preset artifacts** (`presets/webware-alignment/artifacts/`) — they are
  preset-managed. Change them in `webware-tools` first; editing the local copy forks the template.
- `webware/compose.yml` is **repo-owned** and customised (`mysql`, `phpmyadmin`, `mailpit`, the
  `webware_app` database). Editing it forks nothing.
- `devcontainer.json` carries no environment of its own: `dockerComposeFile: ["../compose.yml"]`,
  `service: "tooling"`, `workspaceFolder`, extensions, nothing else. The compose file is the single
  source of truth — which is already the "Dev Container backed by compose" arrangement that was
  asked for.
- The `tooling` service publishes **no ports** and runs `command: sleep infinity`.
- Every script in `webware/composer.json` shells out to a host `php`: `serve`, `test`,
  `test-coverage`, `test-integration`, `mutation-test`.
- Watermark: `webware/1.0.x` at `af12336` (PR #29), `webware-usermanager/1.0.x` at `12ea198`
  (PR #83).

## Open decisions — the user has not answered these

Nothing was changed because of them. **Do not pick one unilaterally**; the user was explicit that
this is their environment and that the agent should not be making these calls.

1. **The MySQL address from inside the container.** `webware-dev-environment.md` RULE 3 requires one
   value, `127.0.0.1`, identical on the host and in CI, with no override; RULE 1 forbids the compose
   service name `mysql` as a PHP host *in any context, on any platform, ever*. Inside `tooling`,
   `127.0.0.1` is the container itself, so the only doctrine-legal address left is
   `host.docker.internal:3306` (mysql is published on the host, and `tooling` already carries the
   `host.docker.internal:host-gateway` alias). But that value differs by *where PHP runs*, which is
   precisely the override RULE 3 was written to eliminate. Putting PHP in the container re-opens
   RULE 3 by construction and needs an explicit amendment from the user.
2. **Reaching the app.** Publishing `8080` on `tooling` in `compose.yml` gives both the Dev
   Container and plain-compose developers the port. Relying on VS Code's automatic port forwarding
   gives it only to the Dev Container, which is the parity break to avoid.
3. **Coverage driver for Infection.** pcov is installed and is the active driver while xdebug sits
   at `mode=off`; it is roughly 3–5× faster for coverage, and Infection works with either. Xdebug is
   what step debugging needs specifically. `XDEBUG_MODE` selects per run
   (`debug` / `coverage` / unset), so this is not either/or — just a default to choose.
4. **Root-owned artifacts.** PHP in the container writes `.phpunit.cache/`, `clover.xml` and
   `infection.log` into the bind-mounted tree as root. That breaks host-side coverage runs and needs
   root to clean up. Running as the host UID, or redirecting the cache directory, belongs with
   whichever of the above is chosen.

## Loose ends found — none of them touched

- `webware/plan/webware-coordination-handoff-2026-09-30.md` — **untracked and superseded**. Its
  "NEXT TASK" and its "26 dirty files on `feat/app-acl-seeds`" state predate the merges that
  followed (that branch landed as PR #7). Left in place rather than committed, because committing it
  as-is would mislead the next reader. It also duplicates rules recorded here.
- `webware-usermanager/docs/agent-plan-admin-create-user.md` and
  `webware-usermanager/docs/plan-admin-create-user.md` — untracked, and duplicates of the tracked
  `plan/` copies (the first is byte-identical; the second differs only in one relative link).
  Leftovers from the docs → plan move in PR #83.
- A stash in `webware-htmx` (4 test fixture templates, one line each) and one in `webware-phpdb`
  (self-described "byte-identical copy of feat/schema-abstraction, pre-switch"). Local snapshots;
  neither is pushable as-is.
- ~30 open PRs across the org, nearly all Renovate lock-file maintenance. Not this session's work;
  left alone.

## Other open threads

- `webware-acl` **#44** — adopt `Webware\Core\Role`, drop the per-role proxies. Its `ForbiddenHandler`
  guest branch is blocked on a **design decision, not code**: `Forbidden` is not a statement about
  identity, and the branch asks `getIdentity()`. The question has to become an ACL question.
  Unresolved: which resource it asks about, and whether the guest redirect moves to
  `AuthorizationMiddleware`. `webware-acl#59` was also filed during this session.
- `webware-usermanager` RFCs **#84** and **#85** await decisions D1–D7 and D1–D5.
- **D4 of #85** asks for `ProprietaryInterface` on `webware-acl`'s `RouteResourceInterface`; it has
  no issue of its own yet.
- `webware-migration` is deliberately on hold — do not start it.
- `webware-acl/src/Acl.php` was open in the editor at handoff, but the working tree is clean; there
  is no in-progress edit there.

## Rules to carry forward

- **Never delete a file without the user's explicit approval.** Renames and moves count as deletions.
  If a file looks wrong, report it in one line and stop.
- **Every change lands through a PR.** Never push to a default or version branch. Releases and tags
  belong to the owner alone — never tag, never `gh release`.
- Commit identity is `Joey Smith <jsmith@webinertia.net>` (`git commit -s`). Never override the
  author or committer, and never derive an identity from the OS username.
- **Merge commits by default.** Decide each PR on its own merit, and state the reason *before*
  squashing.
- **Never hand PHP the compose service name `mysql` as a host.** Not on the host, not in a
  container, not on any platform.
- **Do not run anything that boots app code while the user's debug session is attached.** Check
  `ss -ltnp | grep -E ':(9000|9003)'` first. A stray PHP process can kill the session.
- Do not probe Packagist; use `git ls-remote --tags` and `gh release list`.
- Reply style: findings first, no closing recap or restatement, no option menus; say "measured"
  versus "inferred" honestly, and ask one narrow question when blocked.
