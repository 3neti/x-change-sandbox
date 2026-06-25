# Codex Instruction — Scaffold `3neti/x-action` Core Workflow Action Package

## Mission

Create the initial scaffold for a new reusable Laravel package:

```text
3neti/x-action
```

Namespace:

```php
LBHurtado\XAction
```

The package will provide the workflow action / CTA infrastructure for the x-change ecosystem.

The immediate objective is **not** to build the full automation platform.

The immediate objective is to establish the stable core grammar:

```text
ActionData
ActionTargetData
ActionSubjectData
ActionContextData
WorkflowActionContract
ActionRegistryContract
ActionResolverContract
ActionRecorderContract
basic registry
basic resolver
config
tests
compass
```

Do not integrate with x-change claim compiler yet.

Do not add connectors yet.

Do not add migrations yet unless explicitly required by the current slice.

Do not add Vue components yet.

This first slice should be backend-first, DTO-driven, test-covered, and package-safe.

---

# Project Location

Create or work in:

```text
/Users/rli/PhpstormProjects/packages/x-action
```

Package namespace:

```php
LBHurtado\XAction
```

Composer package name:

```json
"3neti/x-action"
```

---

# Core Compass

Persist this compass in the repository as:

```text
docs/x-action-compass.md
```

## X-ACTION COMPASS v1

`3neti/x-action` is a reusable workflow action infrastructure package.

It answers:

```text
What can this actor do next?
```

It does not execute the business workflow itself.

It provides the grammar for workflow continuation across:

```text
x-change
x-feedback
x-campaign
x-journal
Cockpit
future AI Copilot
future external automation connectors
```

## Core Principle

```text
Notifications inform.
Workflows continue.
```

## Ownership Rule

```text
x-action owns action grammar, resolution, routing, analytics, and connectors.

Host applications own claim, disbursement, campaign, settlement, money movement, and compliance semantics.
```

## Safety Rule

`x-action` must never become the authority for money or compliance state.

It may describe, route, track, and correlate an action.

It must not approve claims, move money, retry disbursements, bypass OTP, bypass KYC, or mutate voucher state directly.

## Claim Compiler Rule

The claim compiler is clockwork.

CTA integration with claim compiler must be:

```text
read-only
append-only
decorative
failure-safe
non-invasive
```

No claim compiler integration in this first scaffold unless explicitly instructed later.

## Package Rule

The minimal useful core must work without:

```text
database
migrations
Vue
connectors
playbooks
admin UI
x-change
```

The first stable core is:

```text
DTOs
contracts
registry
resolver
config
tests
```

---

# Required Documents to Create

Create these docs if they do not already exist:

```text
docs/01-current-state.md
docs/02-target-state.md
docs/03-evolution-plan.md
docs/04-test-strategy.md
docs/05-architecture-invariants.md
docs/x-action-compass.md
```

Use the already provided architecture documents as reference. If they are already present, do not overwrite blindly; inspect and preserve intent.

---

# Current Authorized Slice

You are authorized to implement **Phase 1 — Core Grammar** and a small part of **Phase 2 — Registry & Resolution Engine**.

Implement only:

```text
1. Composer package scaffold
2. Service provider
3. Config file
4. Core DTOs
5. Core contracts
6. Basic in-memory/config action registry
7. Basic action resolver
8. Traits for default action behavior
9. Null action recorder
10. Tests
11. Compass document
```

Do not implement:

```text
database migrations
action router endpoints
controllers
resources
Vue components
connectors
Pipedream
AI agent connectors
playbooks
x-change claim compiler integration
lifecycle scenario runner integration
```

Those are later phases.

---

# Suggested Package Structure

```text
src/
    Contracts/
        WorkflowActionContract.php
        ActionRegistryContract.php
        ActionResolverContract.php
        ActionRecorderContract.php

    Data/
        ActionData.php
        ActionTargetData.php
        ActionSubjectData.php
        ActionContextData.php

    Registries/
        ActionRegistry.php

    Resolvers/
        ActionResolver.php

    Recorders/
        NullActionRecorder.php

    Traits/
        HasWorkflowActionDefaults.php
        AsWorkflowAction.php
        ResolvesActionTargets.php

    XActionServiceProvider.php

config/
    x-action.php

docs/
    x-action-compass.md
    01-current-state.md
    02-target-state.md
    03-evolution-plan.md
    04-test-strategy.md
    05-architecture-invariants.md

tests/
    Unit/
    Feature/
```

---

# Composer Requirements

Use current Laravel package conventions.

Recommended dependencies:

```json
"php": "^8.2",
"illuminate/support": "^11.0|^12.0"
```

For testing:

```json
"orchestra/testbench": "^9.0|^10.0",
"pestphp/pest": "^2.0|^3.0",
"pestphp/pest-plugin-laravel": "^2.0|^3.0"
```

Do not add heavy dependencies.

Do not require `lorisleiva/laravel-actions` as a hard dependency in this first slice.

The package should be compatible with Laravel Actions later, but not dependent on it.

---

# DTO Requirements

## `ActionData`

Namespace:

```php
LBHurtado\XAction\Data
```

Fields:

```php
public string $key;
public string $label;
public ActionTargetData $target;
public ?string $intent = null;
public ?string $description = null;
public ?string $icon = null;
public ?string $style = null;
public ?string $audience = null;
public ?string $surface = null;
public ?string $channel = null;
public ?string $expires_at = null;
public array $permissions = [];
public array $conditions = [];
public array $analytics = [];
public array $meta = [];
```

Provide:

```php
toArray(): array
fromArray(array $data): self
```

## `ActionTargetData`

Fields:

```php
public string $type;
public ?string $route = null;
public array $parameters = [];
public ?string $url = null;
public ?string $method = 'GET';
public ?string $surface = null;
public ?string $connector = null;
public ?string $operation = null;
public array $payload = [];
public array $meta = [];
```

Support target types as constants or enum-like strings:

```text
route
signed_route
external_url
api
mobile_deep_link
action_router
connector
```

## `ActionSubjectData`

Fields:

```php
public string $type;
public string|int|null $id = null;
public array $attributes = [];
public array $state = [];
public array $meta = [];
```

Provide:

```php
get(string $key, mixed $default = null): mixed
```

Support dot notation using Laravel `data_get`.

## `ActionContextData`

Fields:

```php
public ?string $actor_type = null;
public string|int|null $actor_id = null;
public string $feature_profile = 'default';
public ?string $surface = null;
public ?string $channel = null;
public ?string $campaign_id = null;
public ?string $campaign_run_id = null;
public array $capabilities = [];
public array $meta = [];
```

Provide:

```php
hasCapability(string $capability): bool
```

---

# Contract Requirements

## `WorkflowActionContract`

```php
interface WorkflowActionContract
{
    public function key(): string;

    public function supports(ActionSubjectData $subject, ActionContextData $context): bool;

    public function toActionData(ActionSubjectData $subject, ActionContextData $context): ActionData;
}
```

## `ActionRegistryContract`

```php
interface ActionRegistryContract
{
    public function register(string $eventOrState, string|WorkflowActionContract $action): void;

    /**
     * @return array<int, string|WorkflowActionContract>
     */
    public function actionsFor(string $eventOrState): array;
}
```

## `ActionResolverContract`

```php
interface ActionResolverContract
{
    /**
     * @return array<int, ActionData>
     */
    public function resolve(
        string $eventOrState,
        ActionSubjectData $subject,
        ActionContextData $context
    ): array;
}
```

## `ActionRecorderContract`

```php
interface ActionRecorderContract
{
    public function rendered(ActionData $action, ActionSubjectData $subject, ActionContextData $context): void;

    public function clicked(string $actionKey, ActionSubjectData $subject, ActionContextData $context): void;

    public function completed(string $actionKey, ActionSubjectData $subject, ActionContextData $context, array $result = []): void;

    public function failed(string $actionKey, ActionSubjectData $subject, ActionContextData $context, array $error = []): void;
}
```

---

# Registry Requirements

Implement:

```php
LBHurtado\XAction\Registries\ActionRegistry
```

Behavior:

1. Load configured actions from `config('x-action.registry.actions')`.
2. Allow runtime registration through `register()`.
3. Return registered actions using `actionsFor()`.
4. Preserve registration order.
5. Do not instantiate action classes inside the registry unless necessary.

---

# Resolver Requirements

Implement:

```php
LBHurtado\XAction\Resolvers\ActionResolver
```

Behavior:

1. Fetch action providers from registry.
2. Instantiate class strings through Laravel container.
3. Ignore providers that do not implement `WorkflowActionContract`.
4. Call `supports()`.
5. Convert supported actions to `ActionData`.
6. Return array of `ActionData`.
7. Fail gracefully if an action provider is invalid, unless config says strict mode.

Add config:

```php
'strict' => false,
```

If strict is `true`, invalid providers may throw.

Default should be safe and non-breaking.

---

# Recorder Requirements

Implement:

```php
LBHurtado\XAction\Recorders\NullActionRecorder
```

It should implement `ActionRecorderContract` and do nothing.

This supports the invariant that the package must work without database infrastructure.

---

# Trait Requirements

## `HasWorkflowActionDefaults`

Provide defaults:

```php
icon(): ?string
style(): ?string
audience(): ?string
intent(): ?string
description(): ?string
```

## `ResolvesActionTargets`

Helper methods:

```php
routeTarget(string $route, array $parameters = [], ?string $surface = null): ActionTargetData

signedRouteTarget(string $route, array $parameters = [], ?string $surface = null): ActionTargetData

externalUrlTarget(string $url, ?string $surface = null): ActionTargetData

connectorTarget(string $connector, string $operation, array $payload = [], ?string $surface = null): ActionTargetData
```

## `AsWorkflowAction`

Optional helper trait that may provide a default `toActionData()` if the class defines enough methods.

Keep it simple. Do not over-engineer.

---

# Config Requirements

Create:

```text
config/x-action.php
```

Initial shape:

```php
return [
    'strict' => false,

    'registry' => [
        'actions' => [
            // 'claim.succeeded' => [
            //     App\WorkflowActions\OpenRiderUrlAction::class,
            // ],
        ],
    ],

    'analytics' => [
        'enabled' => false,
        'recorder' => LBHurtado\XAction\Recorders\NullActionRecorder::class,
    ],

    'routes' => [
        'enabled' => false,
        'prefix' => 'actions',
        'middleware' => ['web'],
        'api_middleware' => ['api'],
    ],

    'connectors' => [
        'enabled' => false,
        'drivers' => [],
    ],
];
```

---

# Service Provider Requirements

Create:

```php
LBHurtado\XAction\XActionServiceProvider
```

Responsibilities:

1. Merge config.
2. Publish config.
3. Bind contracts:
    - `ActionRegistryContract`
    - `ActionResolverContract`
    - `ActionRecorderContract`
4. Register package name.
5. Do not register routes yet unless routes are explicitly enabled.
6. Do not load migrations yet in this slice.

---

# Test Requirements

Use Pest.

Minimum tests:

## DTO Tests

```text
ActionDataTest
ActionTargetDataTest
ActionSubjectDataTest
ActionContextDataTest
```

Verify:

```text
construction
toArray
fromArray
default values
dot notation lookup
capability checks
```

## Registry Tests

Verify:

```text
loads configured actions
registers runtime actions
preserves order
returns empty array for unknown event
```

## Resolver Tests

Create fake test actions:

```php
AlwaysSupportedAction
NeverSupportedAction
RiderUrlAction
```

Verify:

```text
returns only supported actions
instantiates class-string providers
ignores unsupported providers in non-strict mode
throws in strict mode if implemented
```

## Recorder Tests

Verify `NullActionRecorder` methods are callable and do not throw.

## Service Provider Tests

Verify contracts are bound and config is merged.

---

# Coding Style

Use strict types.

Use final classes where appropriate.

Use typed properties.

Use constructor property promotion where appropriate.

Use Laravel helper functions only when they are available through illuminate/support.

Do not introduce unnecessary abstractions.

Do not implement future phases prematurely.

---

# Architectural Invariants to Preserve

1. Workflow actions are not URLs.
2. Notifications do not own actions.
3. Campaigns own attribution, not action semantics.
4. Claim compiler must not be touched in this slice.
5. x-action must work without database.
6. x-action must work without Vue.
7. x-action must work without connectors.
8. Host applications remain the authority for money/compliance workflows.
9. The resolver must be deterministic.
10. Analytics must be optional and non-blocking.

---

# Definition of Done

The scaffold is complete when:

1. Composer package is valid.
2. Namespace is `LBHurtado\XAction`.
3. Config publishes.
4. DTOs exist and are tested.
5. Contracts exist and are tested through implementations.
6. Registry exists and is tested.
7. Resolver exists and is tested.
8. Null recorder exists and is tested.
9. Service provider binds contracts.
10. `docs/x-action-compass.md` exists.
11. Test suite passes.
12. No claim compiler integration has been attempted.
13. No migrations, routes, Vue components, or connectors are introduced prematurely.

---

# Commit Guidance

Commit in small logical increments.

Recommended commits:

```text
chore: scaffold x-action package
feat: add workflow action DTOs and contracts
feat: add action registry and resolver
feat: add null action recorder and service bindings
test: cover core workflow action grammar
docs: add x-action compass and architecture docs
```

After every meaningful slice, update:

```text
docs/x-action-compass.md
```

with:

```text
Current status
Implemented
Deferred
Next recommended slice
Known risks
```

Do not leave the compass stale.
