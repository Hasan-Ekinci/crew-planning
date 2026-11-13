# Repository guidance

## Directory purpose and boundaries

Crew Planning is a Laravel 13 application for managing employees, roles, and shifts.
The frontend uses Vue 3, Inertia 3, Vite, and Tailwind CSS 4, with a TypeScript entry point.
This file applies to the entire repository; check for more specific `AGENTS.md` files before editing a subdirectory.

## Follow the user's exact instructions

- **Implement exactly what the user asks for. Treat the stated instructions and scope as authoritative.** Complete the requested work using the existing code patterns and structure.
- Add tests only when the user explicitly requests tests. Do not add or rewrite tests on your own initiative.
- Run tests, formatters, static analysis, type checks, builds, browser/UI checks (including Playwright, Cypress, and Maestro), and Git validation checks only when the user explicitly requests them. A request to implement something does not authorize an automatic validation workflow.
- Add only the features and structures needed for the explicitly requested result. Do not introduce extra features, architectural layers, abstractions, directories, or dependencies that the user did not request.
- Keep initiative limited to completing the stated task. Do not perform unsolicited refactoring, cleanup, redesigns, or improvements to adjacent code.
- Make only the edits necessary to fulfill the request. General best practices, personal preferences, or assumptions about future needs do not authorize additional work.
- If a material ambiguity affects the requested behavior or would require expanding scope, ask a focused clarification instead of guessing or adding extra work.

## Inventory

- `routes/web.php`: login and admin routes.
- `app/Http/Controllers/`: authentication and admin request handling.
- `app/Http/Requests/`: request validation.
- `app/Http/Middleware/`: admin access control and Inertia integration.
- `app/Repositories/`: employee and shift data access.
- `app/Models/` and `database/`: Eloquent models, migrations, factories, and seeders.
- `resources/js/pages/`: Inertia pages; `resources/js/shared-components/`: shared layouts and components.
- `resources/css/app.css` and `resources/views/app.blade.php`: styles and the application shell.
- `tests/Feature/` and `tests/Unit/`: Pest tests.

## Key flows

- Login routes use `LoginController`; Laravel Fortify configuration lives in `config/fortify.php` and `app/Providers/FortifyServiceProvider.php`.
- Admin routes are grouped under `AdminAuth` middleware in `routes/web.php`. Preserve server-side authorization when adding or changing routes.
- Admin pages render through Inertia. Employee and shift operations use `EmployeeRepository` and `ShiftRepository`.

## Local conventions and constraints

- **The existing code is the reference for the intended architecture and code patterns.** Before changing or adding a route, PHP class, middleware, Vue file, or directory, read the closest comparable implementation and its callers. Match its routing, class structure, file placement, naming, and interaction patterns.
- Extend the structure already used in this repository. Introduce a different architecture, abstraction, or coding pattern only when the user explicitly requests that change. Generic Laravel/Vue recommendations and generated scaffolding must fit the existing approach.
- Keep structural changes within the requested scope. Preserve existing classes, responsibilities, and directories when adding functionality. When examples differ, follow the closest comparable feature; correct defects when required by the task without turning them into conventions.
- Follow `.editorconfig`: UTF-8, LF, four spaces, and a final newline; YAML uses two spaces.
- Preserve route names, Inertia page names, request fields, component props/events, and response shapes unless the task requires changing them; update their consumers together when they change.
- Keep secrets in local environment configuration and out of tracked files.

### Routing and middleware

- Follow `routes/web.php`: explicit HTTP-verb routes with `[Controller::class, 'method']` handlers and explicit route names. Match the descriptive URL paths and camelCase route names used by the surrounding feature.
- Add admin endpoints to the existing `Route::middleware([AdminAuth::class])->group(...)` structure. Preserve the existing grouping and explicit paths when extending it.
- Follow `app/Http/Middleware/AdminAuth.php` for custom middleware class placement and `handle(Request $request, Closure $next): Response` structure. Apply middleware through the mechanism already used by comparable routes.
- Keep the current explicit controller routing approach unless the user requests a routing redesign; this includes decisions about resource routes, route prefixes, middleware aliases, and separate route files.

### PHP classes and file structure

- Keep controllers in `app/Http/Controllers/`, Form Requests in `app/Http/Requests/`, middleware in `app/Http/Middleware/`, repositories in `app/Repositories/`, and models in `app/Models/`. Match each class namespace to its existing directory structure.
- Use `AdminController` and the existing repositories as references for controller/repository collaboration: constructor-injected repositories, typed Form Request parameters for validated input, and controller methods that return Inertia pages or the existing array/collection responses.
- Put validation in Form Request classes with `authorize()` and `rules()`. Extend the existing repository classes for related data access and follow the current Eloquent model and relationship patterns.
- Match the comparable class's organization, method naming, visibility, signatures, and dependency injection. Keep new helpers and supporting classes in line with the current responsibilities and file layout.

### Vue files and frontend structure

- Keep Inertia pages in `resources/js/pages/`, feature-specific components beside their feature (for example, `pages/admin/shifts/ShiftModal.vue`), shared layouts in `shared-components/layouts/`, and shared UI components in `shared-components/subcomponents/`.
- Follow `pages/admin/Shifts.vue` and `pages/admin/shifts/ShiftModal.vue`: `<template>` followed by a plain `<script>`, `export default`, explicit component registration and props, and Composition API state and handlers inside `setup(...)` with returned template bindings. Preserve this component structure when extending or adding comparable files.
- Follow existing `@/` imports for shared components and relative imports for nearby feature components. Reuse `AdminLayout`, `BaseLayout`, and shared UI components where appropriate.
- Follow the existing Inertia `useHttp` request handling, reactive form state, prop/event contracts, and Tailwind utility styling used by the comparable component. Keep frontend requests aligned with the explicit backend routes.

## Change guidance

Follow the user's exact instructions for implementation and commands. Use `composer.json` and `package.json` as the source of truth when a command is explicitly requested, and run it from the repository root.

- For explicitly requested test work, follow the existing Pest conventions. Feature tests extend `Tests\TestCase`, which uses `RefreshDatabase`; ensure requested database tests use the dedicated MySQL database `crew_planning_testing` configured in `phpunit.xml`.
- `composer setup` installs dependencies, creates environment configuration, generates an application key, runs migrations, and builds assets. Use it for initial setup, with the intended database configured.
- Keep changes focused and preserve existing user edits.

## Context navigation

- Consult `composer.json`, `package.json`, and `.github/workflows/tests.yml` for dependencies and CI commands. CI currently uses PHP 8.5 and Node.js 22.
- Consult `bootstrap/app.php` for middleware and application bootstrapping, and `phpunit.xml` plus `tests/Pest.php` for test configuration.
- Keep this file aligned with verified repository behavior when architecture or commands change.
