# 05-architecture-invariants.md

# 3neti/x-action — Architecture Invariants

## 1. Purpose of This Document

This document defines the architectural invariants for `3neti/x-action`.

These are the rules that must remain true across all future implementation phases, refactors, integrations, connectors, UI components, and AI extensions.

If a future change violates one of these invariants, the change is architecturally suspect and must be redesigned.

---

# 2. Primary Invariant

## Notifications inform. Workflows continue.

`x-action` exists to model workflow continuation.

It does not exist to make notifications prettier.

A notification may render an action, but it must not decide the action.

---

# 3. Workflow Ownership Invariant

Workflow actions are owned by the workflow domain.

`x-action` owns the grammar and infrastructure.

It does not own business workflows.

```text id="nskt9g"
x-action owns:
    action model
    action registry
    action resolver
    action routing
    action analytics
    action connectors

host apps own:
    claim
    disbursement
    settlement
    campaign
    approval
    money movement
    compliance
```

---

# 4. Action Meaning Invariant

An action is not a URL.

A URL is only one possible target.

```text id="f2yoqq"
Action = intent + actor + subject + state + target + context + analytics
URL = destination
```

No implementation may reduce workflow actions to links only.

---

# 5. CTA Naming Invariant

The internal concept is:

```text id="i98l5i"
Workflow Action
```

The user-facing rendering may be called:

```text id="mhk4gm"
CTA
button
link
menu item
suggested action
```

Internal code should prefer:

```text id="uhshbu"
ActionData
WorkflowActionContract
ActionTargetData
ActionRun
```

not UI-only names.

---

# 6. Claim Compiler Safety Invariant

Claim compiler integration must be non-invasive.

Initial and ongoing integration must preserve:

```text id="tttllb"
existing step resolution
existing form-flow behavior
existing validation
existing redemption execution
existing rider URL behavior
existing success flow
```

`x-action` may decorate compiled output.

It must not secretly rewrite the claim compiler.

---

# 7. Append-Only Integration Invariant

For sensitive workflows, especially claim and disbursement, CTA integration must begin as append-only.

Allowed:

```text id="t2h3og"
add actions[]
add action_key metadata
record analytics
wrap existing URLs
```

Forbidden:

```text id="cvi9q3"
change execution path
change validation path
change form-flow steps
change voucher instructions
change money movement behavior
```

---

# 8. Execution Authority Invariant

`x-action` never becomes the authority for money or compliance state.

It may route to execution.

It may invoke a connector.

It may track an action.

It may receive callbacks.

But final authority remains in the domain system.

```text id="qdhwcc"
x-action can say:
    "this actor clicked retry disbursement"

x-change decides:
    whether retry disbursement may execute
```

---

# 9. Connector Safety Invariant

External connectors may extend execution at the edge.

They must not own core authority.

Allowed connector behavior:

```text id="ld7kyc"
notify
draft
summarize
classify
prepare
request
suggest
create external ticket
trigger external workflow
```

Forbidden connector behavior:

```text id="cljfhb"
directly approve claim
directly move money
directly mutate voucher state
directly bypass OTP
directly bypass KYC
directly bypass settlement approval
```

---

# 10. AI Connector Invariant

Agentic AI is a connector, not a sovereign actor.

AI may:

```text id="fg2gpk"
suggest
draft
classify
summarize
recommend
prepare
invoke approved endpoints
```

AI must not:

```text id="97awsx"
become financial authority
bypass approval
override validation
self-authorize execution
```

---

# 11. Determinism Invariant

Action resolution must be deterministic.

Given the same:

```text id="b1epqq"
workflow state
subject
actor
feature profile
surface
channel
campaign context
```

the resolver must return the same actions.

No hidden randomness.

No UI-layer invention.

No unstable ordering.

---

# 12. Renderer Boundary Invariant

Renderers render.

They do not decide action availability.

```text id="62r3vk"
Backend resolves actions.
Frontend renders actions.
```

Vue components, email templates, SMS templates, and Cockpit components must receive already-resolved actions.

---

# 13. Feedback Boundary Invariant

`x-feedback` may render `ActionData`.

It must not determine whether an action exists.

```text id="b9nd1f"
x-feedback owns:
    delivery
    channel rendering

x-action owns:
    action shape
    action routing
    action analytics

workflow package owns:
    action meaning
```

---

# 14. Campaign Boundary Invariant

Campaigns provide attribution.

They do not own workflow semantics.

```text id="p1edwr"
Campaign owns:
    campaign_id
    campaign_run_id
    attribution
    campaign analytics

Workflow owns:
    action meaning
    eligibility
    execution semantics
```

---

# 15. Journal Boundary Invariant

`x-journal` records history.

`x-action` records structured action telemetry.

The two may integrate, but they must not collapse into one responsibility.

---

# 16. Cockpit Boundary Invariant

Cockpit is an action consumer.

It owns operator placement and layout.

It does not own canonical action semantics.

Cockpit may display:

```text id="jq57mb"
Retry Disbursement
Review Beneficiary
Approve Request
View Audit Trail
```

but those actions must come from action resolution.

---

# 17. Action Definition Invariant

Action definitions should live in code/config, not only in the database.

The database may store:

```text id="ka4qzi"
events
tokens
runs
playbooks
analytics
```

But safe action classes must be developer-defined.

Admins may configure enabled actions, labels, priority, and surfaces.

Admins must not create arbitrary executable code.

---

# 18. Playbook Invariant

Action Playbooks configure availability.

They do not create unsafe behavior.

Playbooks may decide:

```text id="7u2jds"
which approved actions appear
for which profile
on which surface
for which audience
```

Playbooks must not define new business execution logic.

---

# 19. Analytics Observer Invariant

Analytics must be observational.

Analytics failure must not break action resolution or workflow execution.

If the recorder fails, the action should still resolve.

If the workflow is money-sensitive, telemetry failure must never block the workflow unless explicitly configured.

---

# 20. Token Safety Invariant

Trackable action links must use opaque tokens or signed routes.

Sensitive payloads must not be exposed in URLs.

Tokens must support:

```text id="a4zs6v"
expiration
revocation
actor validation
subject validation
metadata
```

---

# 21. Asynchronous Action Invariant

A clicked action is not automatically a completed action.

For async connectors:

```text id="maljor"
clicked
    ≠
completed
```

Required lifecycle:

```text id="z43udt"
clicked
pending
completed / failed / expired
```

Every async connector must use correlation.

---

# 22. Correlation Invariant

Every external connector invocation must be traceable.

Required:

```text id="kvrc49"
ActionRun
correlation_id
callback_url
external_reference
status
```

No async connector may be fire-and-forget without a recoverable record unless explicitly configured as non-critical.

---

# 23. Callback Verification Invariant

Connector callbacks must be verified.

At minimum, callbacks should support one or more of:

```text id="wpwit4"
signed token
shared secret
HMAC signature
correlation ID
idempotency key
```

Unverified callbacks must not complete action runs.

---

# 24. Idempotency Invariant

Action callbacks and action event writes must tolerate duplicate delivery.

Repeated callback with the same correlation ID must not create conflicting state.

---

# 25. Failure Containment Invariant

Failure of one action must not corrupt other actions.

Failure of connector invocation must update action state, not crash unrelated workflows.

Failure of action analytics must not prevent business execution.

Failure of UI rendering must not mutate action state incorrectly.

---

# 26. Package Independence Invariant

`x-action` must remain reusable outside `x-change`.

The package may be first integrated with x-change, but it must not hardcode:

```text id="v7uxgq"
voucher
claim
disbursement
settlement
campaign
```

as required concepts.

Those belong to host packages.

---

# 27. Host Authority Invariant

Host applications must be able to override:

```text id="c6lg2x"
action registry
resolver
recorder
target drivers
connectors
authorization checks
token generation
```

The package must be configurable and extendable.

---

# 28. DTO Stability Invariant

DTOs are package contracts.

Changes to:

```text id="ngm3e6"
ActionData
ActionTargetData
ActionSubjectData
ActionContextData
ActionRunData
```

must be backward-compatible or versioned.

---

# 29. UI Thinness Invariant

Package UI components must be generic.

Allowed:

```text id="tygia8"
ActionButton
ActionList
ActionRenderer
ActionMenu
ActionCard
```

Forbidden:

```text id="oemgun"
ClaimSuccessPage
RetryDisbursementButton
DBPBeneficiaryReviewPage
CampaignDashboard
SettlementApprovalPage
```

Business UI belongs to host applications.

---

# 30. Route Boundary Invariant

Package routes must be infrastructure routes only.

Allowed package routes:

```text id="x77npu"
GET /actions/{token}
POST /actions/events
POST /actions/resolve
POST /actions/connectors/{connector}/callbacks/{correlation_id}
```

Forbidden package routes:

```text id="j8lqjc"
POST /claims/{id}/confirm
POST /disbursements/{id}/retry
POST /campaigns/{id}/send
POST /settlements/{id}/approve
```

Domain execution routes belong to domain packages.

---

# 31. Laravel Actions Compatibility Invariant

The package may be compatible with `lorisleiva/laravel-actions`.

It must not make Laravel Actions the only way to define workflow actions.

Correct distinction:

```text id="i2b7an"
WorkflowActionContract = describes what can be done next.
Laravel Action = performs a concrete task.
```

---

# 32. Testing Invariant

Every new capability must include tests.

Especially:

```text id="mk6zuq"
resolver behavior
registry behavior
target routing
token expiry
analytics events
connector callbacks
claim compiler safety
```

No connector may be merged without fake/mock tests.

No claim compiler integration may be merged without regression tests proving existing behavior is preserved.

---

# 33. Scenario Runner Invariant

Lifecycle scenario runner integration must treat actions as observable artifacts.

The runner may assert and follow actions.

It must not make every available action execute automatically.

---

# 34. Backward Compatibility Invariant

Existing workflows must continue to work without x-action.

Where possible:

```text id="mfo2mo"
x-action disabled
```

should degrade to existing behavior.

Especially in claim UI:

```text id="k4mgsi"
existing claim works
existing rider URL works
existing confirm button works
```

even before full action rendering is adopted.

---

# 35. Minimal Core Invariant

The package must remain useful without:

```text id="p8f3h3"
database
Vue components
connectors
playbooks
admin UI
```

The minimal core is:

```text id="xtc6l4"
DTOs
contracts
registry
resolver
```

Everything else is layered.

---

# 36. Final Architecture Rule

If a change makes `x-action` responsible for deciding business state, it is wrong.

If a change makes workflow continuation explicit, observable, routable, and extensible while preserving host ownership, it is aligned.

---

# 37. One-Line Invariant Summary

`3neti/x-action` must make workflow actions intentional, portable, measurable, and extensible without ever becoming the workflow engine or the authority over money, compliance, campaign, or claim semantics.
