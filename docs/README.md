# Documentation

This directory holds the project's documentation, kept close to the code that
implements it so it stays accurate as the plugin evolves.

## Purpose

Good documentation is what turns a reusable boilerplate into a maintainable
codebase. It captures the *why* behind decisions that the code alone cannot
express, and it gives future contributors (human or AI) a fast path to
context.

## Conventions

- Keep docs **concept-first** and free of any project-specific private
  details (credentials, proprietary names, or URLs you would not share
  publicly).
- **Never commit secrets.** Use placeholders (e.g. `<api_key>`, `<secret>`)
  and a gitignored `.env` / `.env.example` pattern for anything sensitive.
- Prefer concise, decision-oriented prose over sprawling prose.

## Suggested structure

Use subfolders to keep things findable. Suggested layout:

```
docs/
  architecture/     # High-level design, DI, patterns, technical decisions
  guides/           # Step-by-step how-tos (setup, testing, release)
  checklist/        # Implementation checklists / progress trackers
  testing/          # Test strategy, environment setup, checklists
  maintenance/      # Known issues, technical debt, upgrade notes
```

Start by adding a file under the relevant subfolder rather than dumping
everything into this index.

## Index

- `architecture/dependency-injection.md` — When to use the DI container vs
  direct invocation (mindset-based guidance). Added by the DI setup work.
