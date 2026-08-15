# Dependency Injection: When to Use PHP-DI vs Direct Invocation

This project ships **PHP-DI** as the default dependency-injection container,
scoped into the plugin's `ThirdParty` namespace. This document explains the
rationale and — more importantly — **when you should instead use direct
invocation**.

## The short answer

**There is no magic class-count number.** Whether you reach for the container
or plain `new` is a judgement call about how the code feels to navigate and
extend, not a threshold you can compute.

- Prefer **direct invocation** when your services are few and wiring is
  trivial — explicit `new` calls are simpler, more transparent, and easier to
  debug.
- Prefer **PHP-DI** when the service graph is large, interdependent, or
  clearly going to grow. Many real-world plugins have 50+ classes and a
  container pays off well before that scale.

## Why the container is *not* a performance problem

A common myth is that a DI container "wastes resources at runtime." That is
misleading:

- PHP-DI supports **compiled containers** (`ContainerBuilder::enableCompilation()`),
  which remove essentially all reflection/autowiring overhead in production.
- The real cost of PHP-DI is not runtime speed — it is **build complexity**:
  one extra Composer dependency, a PHP-Scoper finder entry, and a container
  bootstrap in the Loader.

So treat this as a **maintainability decision, not a performance one.** If the
container makes your code easier to reason about, use it. If it is adding
indirection with little benefit, drop it.

## Decision guide

| Situation | Recommendation |
|---|---|
| 1–5 small, independent services | Direct invocation |
| Services with shared dependencies (e.g. a shared repository/logger) | Container |
| Deep, growing dependency graph | Container |
| You want to unit-test wiring easily | Either (container makes swapping easy) |
| You want maximal simplicity/readability for a small plugin | Direct invocation |

## How the pieces fit

1. **`composer.json`** — `php-di/php-di: ^7.0` in `require` (provided by this
   boilerplate).
2. **`scoper.inc.php`** — the finder block scopes `vendor/php-di`
   and `vendor/psr/container` into the plugin's `ThirdParty` namespace so
   PHP-DI never collides with another copy loaded by core or a third-party
   plugin.
3. **`composer.json` scripts** — the planned `prefix-deps` (run PHP-Scoper)
   and `build` (re-install with `--no-dev`) scripts produce a
   production-ready `third-party/` directory.
4. **`includes/Loader.php`** — the provided Loader builds the container from
   the scoped `ThirdParty\DI\ContainerBuilder` and resolves services from it.
   See the docblock for the simpler direct-invocation alternative.

## Security note

Never commit real secrets (API keys, tokens, passwords). If a service needs
credentials, read them from an environment variable or a `.env` file that is
gitignored, and commit only a `.env.example` with placeholders.
