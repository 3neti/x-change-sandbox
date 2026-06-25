# 04-test-strategy.md

# 3neti/x-action — Test Strategy

## 1. Purpose of This Document

This document defines the testing philosophy, coverage expectations, test hierarchy, and validation strategy for `3neti/x-action`.

The package is intended to become foundational infrastructure across the x-change ecosystem.

Because it sits between:

```text id="e7h3a8"
workflow state
workflow continuation
analytics
routing
automation
AI connectors
```

the package must prioritize:

```text id="3xy7v0"
determinism
observability
safety
backward compatibility
```

over implementation convenience.

---

# 2. Testing Philosophy

## 2.1 Workflow Actions Must Be Deterministic

Given:

```text id="3u3vbx"
workflow state
actor
feature profile
```

the resolver must always return the same actions.

Action resolution should never depend on:

```text id="uj1m3q"
randomness
rendering layer
UI state
browser behavior
```

The package must be testable entirely from PHP.

---

## 2.2 Action Resolution Is the Core Behavior

The most important thing to test is not:

```text id="0mjlwm"
buttons
links
UI
```

The most important thing to test is:

```text id="q9q9cf"
which actions become available
```

Everything else is downstream.

---

## 2.3 Analytics Must Never Influence Resolution

Actions should resolve identically regardless of:

```text id="pvz4ul"
analytics enabled
analytics disabled
database available
database unavailable
```

Analytics are observers.

They must never affect action availability.

---

## 2.4 Connector Failure Must Be Contained

A failed connector must not:

```text id="hkr1c5"
break workflows
break action resolution
break routing
break rendering
```

Connectors must fail independently.

---

## 2.5 Claim Compiler Safety Is Mandatory

Because x-action integrates with x-change claim compiler:

```text id="gznl5d"
claim behavior before CTA
=
claim behavior after CTA
```

except for:

```text id="dkr0xj"
additional actions[]
additional analytics
```

The package must never change claim execution semantics.

---

# 3. Test Pyramid

The package should follow:

```text id="o9y3pc"
70% Unit Tests
20% Feature Tests
10% Integration Tests
```

UI tests should remain minimal.

Most behavior should be validated at DTO, contract, resolver, and connector levels.

---

# 4. Test Categories

The package should maintain separate suites:

```text id="qzgvgv"
Unit
Feature
Integration
Scenario
Regression
Safety
```

---

# 5. Unit Testing Strategy

## Purpose

Verify individual classes in isolation.

Dependencies should be mocked or faked.

---

# 6. DTO Tests

Every DTO should have complete coverage.

## ActionData

Test:

```text id="9vgr92"
construction
serialization
meta handling
optional values
default values
```

## ActionTargetData

Test:

```text id="4z72j0"
route targets
signed routes
external URLs
connector targets
deep links
```

## ActionSubjectData

Test:

```text id="jlwm5y"
attribute retrieval
default values
nested values
state retrieval
```

## ActionContextData

Test:

```text id="kwk3yn"
actor data
feature profiles
campaign context
channel context
surface context
```

Coverage expectation:

```text id="o4k1vg"
100%
```

---

# 7. Contract Tests

Each contract implementation must be validated.

## WorkflowActionContract

Verify:

```text id="gsvs6o"
key()
supports()
toActionData()
```

behave correctly.

## ActionRegistryContract

Verify:

```text id="xt4gpf"
registration
lookup
replacement behavior
```

## ActionResolverContract

Verify:

```text id="7n63r2"
provider discovery
filtering
final action output
```

---

# 8. Registry Tests

Registry behavior is foundational.

Test:

```text id="zq36f2"
register action
retrieve action
multiple providers
missing provider
duplicate registration
```

Example:

```php id="eb7isr"
expect(
    $registry->actionsFor('claim.succeeded')
)->toHaveCount(2);
```

---

# 9. Resolver Tests

The resolver is the most important unit.

Test:

```text id="8zv8ho"
event → actions
profile → actions
permissions → actions
workflow state → actions
```

Example:

```text id="v0xwsm"
claim.succeeded
    ↓
claim.open_rider
```

Expected:

```php id="6f1onr"
expect($actions)
    ->toHaveCount(1)
    ->and($actions[0]->key)
    ->toBe('claim.open_rider');
```

---

# 10. Provider Tests

Every workflow action provider must have dedicated tests.

Example:

```text id="hcb0zs"
OpenRiderUrlAction
```

Test:

```text id="8bpk2v"
supports when rider exists
does not support when rider missing
returns correct ActionData
returns correct target
```

Coverage expectation:

```text id="3t4m8x"
100%
```

---

# 11. Trait Tests

Traits must be tested through fixtures.

Examples:

```text id="qql6pc"
HasWorkflowActionDefaults
AsWorkflowAction
ResolvesActionTargets
RecordsActionEvents
```

Verify:

```text id="00f4zt"
default icon
default style
helper target generation
event recording
```

---

# 12. Configuration Tests

Verify package configuration.

Examples:

```text id="g5l6wz"
route prefixes
connector drivers
analytics drivers
registry configuration
```

Ensure:

```text id="6f6n9r"
config merge
config override
config publishing
```

all work correctly.

---

# 13. Feature Testing Strategy

Feature tests verify package endpoints.

---

# 14. Action Redirect Tests

Endpoint:

```text id="abxxkk"
GET /actions/{token}
```

Verify:

```text id="ysh3y9"
valid token redirects
expired token blocked
invalid token blocked
analytics recorded
target resolved
```

Example:

```php id="t73i4e"
get($url)
    ->assertRedirect($expected);
```

---

# 15. Action Event Endpoint Tests

Endpoint:

```text id="7e7g3m"
POST /actions/events
```

Verify:

```text id="pjc4np"
rendered
clicked
completed
failed
```

events are stored correctly.

---

# 16. Action Resolve Endpoint Tests

Endpoint:

```text id="k1ik88"
POST /actions/resolve
```

Verify:

```text id="7vh5ws"
subject
context
profile
```

resolve into expected actions.

---

# 17. Callback Endpoint Tests

Endpoint:

```text id="j5sm7r"
POST /actions/connectors/{connector}/callbacks/{correlation_id}
```

Verify:

```text id="yg1d5x"
valid callback
invalid signature
missing correlation
duplicate callback
```

---

# 18. Analytics Tests

Analytics are critical.

---

## Event Recording

Verify:

```text id="0thsgm"
rendered
clicked
completed
failed
expired
```

create expected records.

---

## Event Metadata

Verify:

```text id="qkp40l"
campaign_id
actor_id
surface
channel
connector
```

are persisted correctly.

---

## Event Ordering

Verify:

```text id="0m7nwu"
clicked
before
completed
```

and other lifecycle sequences.

---

# 19. Token Tests

Verify:

```text id="tqzkcl"
generation
expiration
validation
consumption
```

Example:

```php id="6blgkj"
expect($token->isExpired())
    ->toBeFalse();
```

---

# 20. Action Run Tests

Action runs support async execution.

Verify:

```text id="0hvjlwm"
pending
completed
failed
expired
```

state transitions.

Ensure:

```text id="szjyb9"
invalid transitions
```

are rejected.

---

# 21. Connector Testing Strategy

Connectors must be heavily isolated.

Use fakes.

Use HTTP mocking.

Never call real services.

---

# 22. Webhook Connector Tests

Verify:

```text id="haxjlwm"
payload generation
headers
callback handling
error handling
```

Use:

```php id="5dg3m7"
Http::fake();
```

---

# 23. Pipedream Connector Tests

Verify:

```text id="7z7nlu"
payload
correlation_id
callback URL
callback verification
```

No live Pipedream calls.

---

# 24. Future AI Connector Tests

Verify:

```text id="twjv0e"
payload creation
response mapping
authorization boundaries
```

AI must never gain:

```text id="k6x2c0"
financial authority
```

through connector execution.

---

# 25. Security Tests

Security is mandatory.

---

## Token Security

Verify:

```text id="vt5o6r"
invalid token
expired token
tampered token
```

fail correctly.

---

## Authorization

Verify:

```text id="35gb5f"
wrong actor
wrong profile
missing permission
```

cannot invoke actions.

---

## Callback Security

Verify:

```text id="j0sqdb"
bad signature
unknown connector
replay attack
```

are rejected.

---

# 26. Integration Testing Strategy

Integration tests verify package collaboration.

---

# 27. x-change Integration Tests

Most important integration suite.

Verify:

```text id="dlkv42"
claim compiler output
```

before and after x-action integration.

Expected:

```text id="lxmxz8"
same workflow
same execution
same validation
```

plus:

```text id="vdbt73"
actions[]
```

---

# 28. Claim Decorator Tests

Verify:

```text id="ejz6vx"
rider URL present
```

produces:

```text id="0l8jlr"
claim.open_rider
```

Verify:

```text id="w7nx24"
claim confirmation
```

produces:

```text id="tqvsm1"
claim.confirm
```

without changing workflow behavior.

---

# 29. Campaign Integration Tests

Verify:

```text id="2pt4pv"
campaign context
```

flows into analytics.

Example:

```text id="lcxopj"
campaign_id
campaign_run_id
```

must persist correctly.

---

# 30. Journal Integration Tests

Verify:

```text id="ttb2b5"
action events
```

can be consumed by x-journal listeners.

---

# 31. Lifecycle Scenario Runner Tests

The package should integrate with scenario runner assertions.

---

## Action Assertions

Verify:

```php id="gxzjlwm"
expectAction()
expectActionRendered()
expectActionClicked()
expectActionCompleted()
```

---

## Action Following

Verify:

```php id="a7k1tq"
followAction()
```

correctly invokes routing.

---

## Analytics Probes

Verify:

```php id="6gblp7"
assertActionEvent()
```

against recorded telemetry.

---

# 32. Regression Testing Strategy

Every discovered bug must create:

```text id="e4q3pb"
failing test
fix
passing test
```

before merge.

No bug should be fixed without a permanent test.

---

# 33. Safety Testing Strategy

The package exists beside money-sensitive workflows.

Safety tests are mandatory.

---

## Claim Compiler Safety

Verify:

```text id="c4hslr"
CTA failure
```

does not break:

```text id="zpmqeg"
claim compilation
claim execution
claim validation
```

---

## Analytics Safety

Verify:

```text id="j1wqje"
analytics unavailable
```

does not break action resolution.

---

## Connector Safety

Verify:

```text id="h7d7je"
connector unavailable
```

does not break workflow state.

---

# 34. UI Testing Strategy

UI tests are secondary.

Focus only on:

```text id="ttgh4s"
ActionButton
ActionList
ActionRenderer
```

Verify:

```text id="93c18x"
labels
icons
links
events
```

render correctly.

Business pages are not tested here.

---

# 35. Coverage Targets

Minimum expectations:

| Area | Coverage |
|--------|--------|
| DTOs | 100% |
| Contracts | 100% |
| Resolver | 100% |
| Registry | 100% |
| Providers | 100% |
| Connectors | 95%+ |
| Controllers | 95%+ |
| Integration | Critical paths |
| UI Components | Core behaviors |

---

# 36. Testing Conventions

Use:

```text id="l5my0r"
Pest
Arrange-Act-Assert
Datasets
Named datasets
```

Example:

```php id="x0q1n0"
dataset('action-statuses', [
    'rendered',
    'clicked',
    'completed',
]);
```

Tests should read as documentation.

---

# 37. CI Requirements

Every pull request must run:

```text id="rkjuzh"
unit tests
feature tests
integration tests
```

before merge.

No scaffold should be accepted with failing tests.

---

# 38. Exit Criteria

The package is considered sufficiently tested when:

1. Action resolution is deterministic.
2. Routing is validated.
3. Analytics are verified.
4. Connectors are isolated and tested.
5. Claim compiler integration is proven safe.
6. Scenario runner support is validated.
7. Security boundaries are enforced.
8. Regression coverage exists for discovered defects.

---

# 39. Final Testing Statement

The testing strategy for `3neti/x-action` prioritizes trust.

Workflow actions sit at the intersection of workflow execution, campaign attribution, automation, routing, analytics, and future AI orchestration.

Therefore:

```text id="6xpf2q"
Every action must be deterministic.
Every action must be observable.
Every action must be safe.
```

The package should earn confidence not by UI behavior, but by proving that workflow continuation can be resolved, measured, routed, and extended without compromising the systems that own business execution.
