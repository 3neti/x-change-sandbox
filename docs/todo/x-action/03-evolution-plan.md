# 03-evolution-plan.md

# 3neti/x-action — Evolution Plan

## 1. Purpose of This Document

This document defines the implementation roadmap for `3neti/x-action`.

The objective is to evolve the package safely from a lightweight workflow-action grammar into a reusable workflow continuation platform.

The plan intentionally prioritizes:

```text
stability
composability
observability
backward compatibility
non-invasive integration
```

over feature completeness.

The package must first become useful.

Only then should it become powerful.

---

# 2. Strategic Philosophy

The package should evolve in layers.

Not:

```text
connectors first
AI first
administration UI first
```

But:

```text
grammar
    ↓
resolution
    ↓
analytics
    ↓
routing
    ↓
integration
    ↓
automation
    ↓
governance
```

This mirrors the successful evolution of:

- x-change claim compiler
- settlement envelope architecture
- cockpit architecture

where semantics are stabilized before orchestration.

---

# 3. Evolution Goals

By the end of the roadmap, the package should provide:

```text
Workflow Action Grammar
Workflow Action Resolution
Workflow Action Routing
Workflow Action Analytics
Workflow Action Playbooks
Workflow Action Connectors
Workflow Action Governance
```

while remaining:

```text
host-application owned
workflow-engine independent
claim-compiler safe
```

---

# 4. Phase 0 — Architectural Validation

## Goal

Validate the conceptual model before building infrastructure.

## Deliverables

Documentation only:

```text
01-current-state.md
02-target-state.md
03-evolution-plan.md
04-test-strategy.md
05-architecture-invariants.md
```

Functional specification:

```text
x-action_functional_specifications.md
```

Architecture review questions:

```text
Does CTA belong to notifications?
Does CTA belong to workflows?
How does claim compiler integrate?
How do campaigns consume CTA analytics?
How do connectors fit?
How does AI fit?
```

## Success Criteria

Consensus on:

```text
Workflow Action
```

as the primary abstraction.

---

# 5. Phase 1 — Core Grammar

## Goal

Create the smallest stable foundation.

No migrations.

No UI.

No connectors.

No administration.

## Deliverables

### DTOs

```php
ActionData
ActionTargetData
ActionSubjectData
ActionContextData
```

### Contracts

```php
WorkflowActionContract
ActionRegistryContract
ActionResolverContract
ActionRecorderContract
```

### Traits

```php
HasWorkflowActionDefaults
AsWorkflowAction
ResolvesActionTargets
```

### Config

```php
config/x-action.php
```

### Service Provider

```php
XActionServiceProvider
```

### Tests

100% DTO and contract coverage.

## Success Criteria

Host applications can define:

```php
claim.open_rider
claim.confirm
```

without needing any database tables.

---

# 6. Phase 2 — Registry & Resolution Engine

## Goal

Resolve actions from workflow state.

## Deliverables

### Registry

```php
ActionRegistry
```

### Resolver

```php
ActionResolver
```

### Provider Discovery

Support:

```php
claim.succeeded
    → OpenRiderUrlAction

claim.awaiting_documents
    → UploadDocumentAction
```

### Filtering

Support:

```text
supports()
permissions
state
feature profile
```

## Success Criteria

Given:

```text
workflow state
actor
feature profile
```

the package can return:

```php
ActionData[]
```

deterministically.

---

# 7. Phase 3 — Analytics Foundation

## Goal

Make actions measurable.

## Deliverables

### Migration

```text
workflow_action_events
```

### Model

```php
WorkflowActionEvent
```

### Recorder

```php
DatabaseActionRecorder
```

### Events

```php
WorkflowActionRendered
WorkflowActionClicked
WorkflowActionCompleted
WorkflowActionFailed
```

## Metrics

Track:

```text
rendered
clicked
completed
failed
```

## Success Criteria

Every action can be measured without changing workflow execution.

---

# 8. Phase 4 — x-change Claim Integration

## Goal

Safely integrate with claim compiler.

## Guiding Principle

This phase must be surgical.

The claim compiler is already stable.

We do not modify its internal clockwork.

## Deliverables

### CTA Decorator

```php
ClaimActionDecorator
```

Pattern:

```text
Compiled Experience
        ↓
Decorator
        ↓
Experience + actions[]
```

### Initial Actions

```text
claim.confirm
claim.open_rider
```

### Analytics

Record:

```text
rendered
clicked
completed
```

## Explicit Non-Goals

Do not change:

```text
claim execution
validation
form-flow
voucher instructions
YAML drivers
redemption pipeline
```

## Success Criteria

Claim flows behave exactly the same before and after integration.

---

# 9. Phase 5 — Token Routing

## Goal

Introduce trackable action routing.

## Deliverables

### Migration

```text
workflow_action_tokens
```

### Model

```php
WorkflowActionToken
```

### Controller

```php
ActionRedirectController
```

### Token Generation Service

```php
ActionTokenGenerator
```

### Drivers

```php
route
signed_route
external_url
```

## Success Criteria

Actions can be tracked independently from destination URLs.

---

# 10. Phase 6 — Generic Vue Components

## Goal

Provide reusable rendering components.

## Deliverables

### Components

```text
ActionButton.vue
ActionList.vue
ActionMenu.vue
ActionRenderer.vue
ActionCard.vue
```

### Inertia Compatibility

Support:

```text
Jetstream
Breeze
Inertia
Vue 3
```

## Non-Goals

No business pages.

No claim pages.

No campaign pages.

No cockpit pages.

## Success Criteria

Any host application can render actions from `ActionData[]`.

---

# 11. Phase 7 — Action Runs

## Goal

Support asynchronous execution.

## Deliverables

### Migration

```text
workflow_action_runs
```

### Model

```php
WorkflowActionRun
```

### Lifecycle

```text
created
pending
completed
failed
expired
cancelled
```

### Correlation

```text
correlation_id
callback_url
external_reference
```

## Success Criteria

Actions can survive asynchronous automation.

---

# 12. Phase 8 — Connector Framework

## Goal

Introduce extensibility.

## Deliverables

### Contract

```php
ActionConnectorContract
```

### Drivers

```text
webhook
null
```

### Callback Infrastructure

```php
ActionConnectorCallbackController
```

## Success Criteria

External systems can participate in action execution.

---

# 13. Phase 9 — Pipedream Connector

## Goal

Validate external automation model.

## Deliverables

### Connector

```php
PipedreamConnector
```

### Payload Transformation

```php
ActionPayloadTransformer
```

### Callback Verification

```php
ActionCallbackVerifier
```

### Correlation Recovery

```text
Action Run
        ↓
Pipedream
        ↓
Callback
        ↓
Action Run Completed
```

## Success Criteria

Asynchronous automation becomes first-class.

---

# 14. Phase 10 — Lifecycle Scenario Runner Integration

## Goal

Make actions testable workflow artifacts.

## Deliverables

### Assertions

```php
expectAction()
expectActionRendered()
expectActionClicked()
expectActionCompleted()
```

### Follow Helpers

```php
followAction()
```

### Analytics Probes

```php
assertActionEvent()
```

## Success Criteria

Actions participate in scenario testing.

---

# 15. Phase 11 — Campaign Attribution

## Goal

Connect actions to campaign performance.

## Deliverables

### Context Fields

```text
campaign_id
campaign_run_id
segment_id
batch_id
```

### Analytics Dimensions

Track:

```text
campaign render
campaign click
campaign completion
campaign abandonment
```

## Success Criteria

Campaigns can measure CTA effectiveness.

---

# 16. Phase 12 — Feature Profile Resolution

## Goal

Support deployment-specific action behavior.

## Deliverables

### Resolution Layer

```text
default
dbp
lgu
dswd
private-bank
```

### Override Rules

Support:

```text
labels
priority
target
visibility
```

## Success Criteria

The same workflow can expose different actions across institutions.

---

# 17. Phase 13 — Action Playbooks

## Goal

Make actions administrable.

## Deliverables

### Migration

```text
workflow_action_playbooks
```

### Model

```php
WorkflowActionPlaybook
```

### Resolver

```php
PlaybookResolver
```

### Rules

```text
event
audience
surface
channel
profile
```

## Success Criteria

Administrators can configure action availability without writing code.

---

# 18. Phase 14 — Optional Administration Module

## Goal

Provide governance tooling.

## Deliverables

### Pages

```text
Action Catalog
Action Analytics
Action Runs
Action Playbooks
```

### Search

```text
action usage
action performance
action ownership
```

## Success Criteria

Operators can understand the action ecosystem.

---

# 19. Phase 15 — AI Connector Framework

## Goal

Prepare for agentic execution.

## Deliverables

### Connector Type

```text
ai_agent
```

### Capabilities

```text
suggest
draft
classify
prepare
recommend
```

### Safety Layer

AI cannot:

```text
move money
approve claims
change settlement state
bypass authorization
```

without domain approval.

## Success Criteria

AI becomes another connector.

Not a privileged subsystem.

---

# 20. Phase 16 — Copilot Action Surfaces

## Goal

Allow actions to appear in AI-assisted workflows.

## Example

```text
Disbursement Failed

Suggested Actions:
    Retry Disbursement
    Contact Beneficiary
    Escalate to Bank
```

Generated from:

```php
ActionData[]
```

not custom AI logic.

## Success Criteria

Copilot becomes another action consumer.

---

# 21. Phase 17 — Ecosystem Adoption

## Goal

Move beyond x-change.

Potential adopters:

```text
x-change
x-campaign
x-feedback
Cockpit
track-ai
future packages
```

## Success Criteria

Workflow actions become a reusable ecosystem primitive.

---

# 22. Technical Debt Management

Each phase must maintain:

```text
backward compatibility
host-app ownership
action determinism
non-invasive integrations
```

No phase may:

```text
rewrite claim compiler
rewrite execution engine
replace workflow ownership
```

---

# 23. Exit Criteria

The roadmap is considered complete when:

1. Workflow actions are first-class.
2. Actions are measurable.
3. Actions are campaign-aware.
4. Actions are connector-aware.
5. Actions support asynchronous execution.
6. Actions support AI connectors.
7. Actions remain workflow-engine independent.
8. Host applications remain the authority.
9. Claim compiler remains stable.
10. External automation never gains financial authority.

---

# 24. Final Evolution Statement

The evolution of `3neti/x-action` is intentionally conservative.

The package begins as a simple workflow-action grammar.

It gradually evolves into a workflow continuation platform capable of powering:

```text
claims
campaigns
settlements
cockpit operations
notifications
external automation
AI-assisted workflows
```

without ever taking ownership of the workflows themselves.

The package becomes the connective tissue between workflow state and workflow continuation while preserving the sovereignty of the systems that actually execute business logic.
