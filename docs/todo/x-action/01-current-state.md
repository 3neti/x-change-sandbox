# 01-current-state.md

# 3neti/x-action — Current State

## 1. Purpose of This Document

This document captures the current architectural state before scaffolding `3neti/x-action`.

The goal is to describe:

- what already exists in the x-change ecosystem
- why workflow actions / CTAs are now becoming necessary
- where the current gaps are
- what must not be disturbed
- what architectural assumptions should guide the next scaffold

This is a current-state document, not the target implementation.

---

# 2. Package Identity

Proposed package:

```text
3neti/x-action
```

Namespace:

```php
LBHurtado\XAction
```

Proposed role:

```text
Reusable workflow-action / CTA infrastructure package
```

The package is intended to provide the grammar for workflow actions across:

- x-change
- x-feedback
- x-campaign
- x-journal
- Cockpit
- future AI Copilot / agentic automation
- future external automation connectors

---

# 3. Current Architectural Context

The x-change ecosystem is evolving from a voucher/disbursement application into an execution platform.

Current package responsibilities are becoming clearer:

```text
x-change
    owns business workflows, claim execution, disbursement, settlement orchestration

x-feedback
    owns message delivery and channel rendering

x-journal
    owns history and audit narrative

x-campaign
    owns distribution, campaign attribution, and campaign performance

Cockpit
    owns operator work surfaces

voucher
    owns voucher identity, instructions, redemption contracts, and lifecycle semantics

form-flow
    owns dynamic claimant step rendering and input collection
```

The architectural principle already emerging is:

```text
Notifications inform.
Workflows continue.
```

This means CTAs should not be owned by notification templates.

---

# 4. Current CTA Situation

There is no unified CTA / workflow action model yet.

CTA-like behavior currently exists in scattered forms:

- claim UI buttons
- form-flow submit buttons
- success page continuation links
- `rider->url`
- email links
- SMS links
- notification links
- cockpit/operator buttons
- future campaign links
- retry/approval actions
- document upload links
- dashboard links

These are real workflow continuation points, but they are not yet modeled as one common concept.

---

# 5. Current Claim Compiler Situation

The claim compiler / claim experience machinery is already carefully curated and working.

It compiles voucher instructions into claimant experience.

It is responsible for shaping the claimant journey, including:

- required steps
- form-flow integration
- claim continuation
- completion
- success payload
- rider messaging
- rider URL behavior

This compiler is sensitive and should not be disrupted.

The current claim flow should be treated as clockwork.

Any CTA integration into this area must be:

```text
read-only
append-only
decorative at first
failure-safe
non-invasive
```

The initial CTA work must not rewrite:

- form-flow step resolution
- YAML driver behavior
- claim validation
- redemption execution
- post-redemption pipeline
- success redirect behavior

---

# 6. Current Rider URL Situation

`rider->url` already behaves like a continuation action after claim success.

Conceptually, it is a CTA.

However, its existence belongs to the claim compiler / voucher instruction realm.

Current understanding:

```text
Claim compiler owns the existence of rider->url.
x-action should wrap rider->url as an ActionData for tracking, routing, and rendering.
```

The first safe representation may be:

```text
action_key: claim.open_rider
label: Continue
target_type: external_url
surface: claim_success
actor: claimant
```

But this must not remove or break the existing rider URL handling.

---

# 7. Current Confirm Redemption Situation

The confirm redemption button is an internal workflow continuation point.

It is not merely a UI control.

It moves the claimant from review/completion into actual claim execution.

Conceptually:

```text
Confirm Redemption = claim.confirm
```

However, the existing form-flow and claim submit behavior should remain untouched at first.

Initial CTA integration should only:

- tag it
- serialize it if safe
- record rendered/clicked/completed events
- avoid changing the route or execution semantics

---

# 8. Current Operational CTA Situation

Operational CTAs are not yet unified.

Examples include:

- retry disbursement
- approve OTP request
- review beneficiary
- upload settlement document
- contact beneficiary
- resend notification
- open audit trail
- open dashboard
- review failed campaign delivery

These actions may appear in:

- Cockpit
- email
- SMS
- in-app notifications
- campaign dashboards
- admin queues
- AI Copilot suggestions

There is currently no shared registry or action grammar for these.

---

# 9. Current Campaign Relationship

Campaigns need CTA attribution to be useful.

Campaigns care about:

- delivered
- opened
- clicked
- completed
- failed
- abandoned
- converted

However, campaigns should not own workflow action semantics.

Current preferred boundary:

```text
Workflow owns CTA definitions.
Campaign owns CTA attribution and analytics.
Feedback renders CTA.
Journal records CTA history.
Cockpit visualizes CTA performance.
```

This means campaign context should travel with action analytics, but campaign should not decide financial workflow actions.

---

# 10. Current Feedback Relationship

`x-feedback` currently or eventually delivers messages across channels.

It may render actions as:

- email buttons
- SMS short links
- in-app buttons
- webhook payload links
- Slack links

But feedback should not decide:

- what action is available
- who can execute it
- whether the action is stale
- whether an action should go to Cockpit, claim UI, campaign UI, or a connector

Feedback should receive already-resolved `ActionData[]`.

---

# 11. Current Journal Relationship

`x-journal` should record historical activity.

Workflow action events are natural journal entries:

- action rendered
- action clicked
- action invoked
- action completed
- action failed
- connector callback received

However, `x-journal` should not be the runtime action analytics engine.

Preferred boundary:

```text
x-action records structured action telemetry.
x-journal records narrative or audit history.
```

---

# 12. Current Connector Idea

There is no connector model yet, but the need is emerging.

Potential external automation tools include:

- webhook
- Pipedream
- n8n
- Zapier
- Slack
- email
- SMS
- MCP tools
- AI agents

The key requirement is that asynchronous external automation must be correlated back to the action.

This implies a future concept:

```text
WorkflowActionRun
```

With lifecycle:

```text
created
clicked
pending
completed
failed
expired
cancelled
```

External connector flow:

```text
CTA clicked
    ↓
ActionRun created
    ↓
Connector invoked
    ↓
External automation runs
    ↓
Callback received
    ↓
ActionRun completed or failed
```

---

# 13. Current Agentic AI Consideration

Agentic AI can fit the same connector model.

The AI agent should be treated as an external or semi-external action connector.

Allowed AI behavior:

- suggest
- draft
- summarize
- classify
- prepare
- recommend
- invoke approved endpoints

Forbidden AI behavior:

- directly approve claims
- directly retry money movement without domain authorization
- directly mutate voucher state
- bypass OTP or approval controls
- bypass compliance gates

Current principle:

```text
Agent may suggest or prepare.
x-change remains sovereign over money and compliance state.
```

---

# 14. Current Missing Pieces

There is currently no shared implementation for:

- `ActionData`
- `ActionTargetData`
- `ActionSubjectData`
- `ActionContextData`
- action registry
- action resolver
- action recorder
- action router
- action token
- action run
- action connector
- action playbook
- generic action UI components
- action analytics tables
- feature profile action overrides
- campaign attribution for CTAs
- lifecycle scenario runner CTA assertions

---

# 15. Current Naming Decision

The package should use “workflow action” internally.

“CTA” may remain a user-facing or UI-facing term.

Reason:

```text
CTA sounds like a button.
Workflow Action captures the domain meaning.
```

Recommended naming:

```text
WorkflowActionContract
ActionData
ActionTargetData
ActionRun
ActionConnector
ActionResolver
ActionRegistry
```

Avoid making the package too UI-only.

---

# 16. Current Laravel Actions Relationship

The ecosystem already uses `lorisleiva/laravel-actions`.

The package should be compatible with that style but should not make Laravel Actions the base abstraction.

Current distinction:

```text
WorkflowActionContract describes what can be done next.
Laravel Action performs a specific task.
```

A concrete host action may use both:

```php
use Lorisleiva\Actions\Concerns\AsAction;
use LBHurtado\XAction\Traits\AsWorkflowAction;
```

But `x-action` should define its own workflow action contract.

---

# 17. Current UI Position

The package may ship generic reusable UI components.

Examples:

```text
ActionButton.vue
ActionList.vue
ActionMenu.vue
ActionRenderer.vue
ActionCard.vue
```

The package should not ship business-specific pages such as:

- claim success page
- retry disbursement page
- campaign dashboard
- settlement approval page
- beneficiary review page

Business pages remain in host apps.

Optional generic admin pages may be considered later for:

- action catalog
- action playbooks
- action analytics

But these are not required for the first scaffold.

---

# 18. Current Database Position

The package should have optional/publishable migrations.

Migrations should not be required for action definitions.

Action definitions should live in:

- PHP classes
- config registry
- optional playbook records

Database storage should be used only for runtime concerns:

- action analytics
- action tokens
- action runs
- optional playbooks

Candidate tables:

```text
workflow_action_events
workflow_action_tokens
workflow_action_runs
workflow_action_playbooks
```

---

# 19. Current Endpoint Position

The package may own generic infrastructure endpoints.

Allowed package endpoints:

```text
GET  /actions/{token}
POST /actions/events
GET  /actions/resolve
POST /actions/resolve
POST /actions/connectors/{connector}/callbacks/{correlation_id}
```

The package must not own domain execution endpoints such as:

```text
POST /claims/{id}/confirm
POST /disbursements/{id}/retry
POST /campaigns/{id}/send
POST /settlements/{id}/documents
```

Those remain in domain packages or host apps.

---

# 20. Current Integration Strategy

The first integration target should be x-change, but x-action must remain reusable.

Recommended first slice:

```text
1. Scaffold DTOs and contracts.
2. Add registry and resolver.
3. Add analytics recorder.
4. Add optional action events table.
5. Add claim CTA decorator in x-change.
6. Append actions[] to claim compiled payload.
7. Start with claim.confirm and claim.open_rider.
```

This allows CTA to be introduced surgically without disturbing claim compiler internals.

---

# 21. Current Safety Principle

The package must never break the workflow if action resolution fails.

For claim compiler integration:

```text
CTA failure must degrade gracefully.
Claim flow must continue.
```

This is critical because claim, form-flow, voucher redemption, and disbursement are money-sensitive workflows.

---

# 22. Current Architectural Summary

Current state can be summarized as:

```text
The x-change ecosystem already has many workflow continuation points,
but they are scattered across claim UI, feedback, campaign, cockpit,
and operational flows.

The claim compiler already works and must not be disrupted.

The emerging need is a reusable action grammar that can wrap,
resolve, render, route, track, and extend CTAs without taking ownership
of the underlying workflows.
```

---

# 23. Immediate Scaffold Implication

The next scaffold should not begin with pages or connectors.

It should begin with the smallest stable core:

```text
ActionData
ActionTargetData
ActionSubjectData
ActionContextData
WorkflowActionContract
ActionRegistryContract
ActionResolverContract
ActionRecorderContract
basic config
basic tests
```

Only after this grammar is stable should the package add:

- migrations
- router tokens
- connector runs
- Vue components
- playbooks
- x-change claim compiler decorator
- lifecycle scenario runner helpers

---

# 24. Guiding Statement

`3neti/x-action` begins as a non-invasive workflow action grammar.

Its first job is not to steer workflows.

Its first job is to name, serialize, render, route, and measure the continuation points that already exist.
