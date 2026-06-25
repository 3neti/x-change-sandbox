# 02-target-state.md

# 3neti/x-action — Target State

## 1. Purpose of This Document

This document describes the desired future state of `3neti/x-action`.

It answers:

- What should the package ultimately become?
- What role should it play in the x-change ecosystem?
- How should workflows, campaigns, notifications, cockpit, and future AI agents consume it?
- How should actions be represented, resolved, rendered, routed, measured, and extended?

This document intentionally describes the destination, not the implementation sequence.

---

# 2. Vision

`3neti/x-action` becomes the canonical workflow action infrastructure for the x-change ecosystem.

Its purpose is to answer a single question:

> Given the current workflow state, what can this actor do next?

The package provides a universal language for workflow continuation.

It becomes the standard way to represent:

- claimant actions
- operator actions
- campaign actions
- administrative actions
- automation actions
- AI-assisted actions

across all applications and packages.

---

# 3. Long-Term Identity

The package is not:

```text
a button package
a notification package
a routing package
a campaign package
a workflow engine
```

The package is:

```text
a workflow action platform
```

It sits between:

```text
Workflow State
        ↓
Workflow Action Resolution
        ↓
Rendering / Routing / Analytics / Automation
```

---

# 4. Core Architectural Role

Future architecture:

```text
Workflow
    ↓
Experience Compiler
    ↓
Experience Payload

Experience Payload
    ├── Status
    ├── Messages
    ├── Components
    ├── Forms
    ├── Actions
    └── Metadata
```

In this model:

```text
Claim Compiler
Campaign Compiler
Settlement Compiler
Cockpit Compiler
```

all emit workflow actions using the same language.

`x-action` becomes the common action layer across all experiences.

---

# 5. Canonical Action Model

Every workflow continuation point should become a first-class action.

Examples:

```text
claim.confirm
claim.open_rider
claim.upload_document

campaign.claim_benefit
campaign.complete_survey

disbursement.retry
beneficiary.contact

settlement.upload_document

cockpit.review_claim
cockpit.export_batch
```

All actions should serialize into a common structure.

Future rendering surfaces should not care where the action originated.

---

# 6. Workflow Action as First-Class Domain Object

In the target architecture:

```text
Workflow Action
```

becomes a first-class domain artifact.

Not:

```php
$rider->url
```

But:

```php
ActionData(
    key: 'claim.open_rider',
    label: 'Continue',
    target: ...
)
```

Not:

```php
<button>Retry</button>
```

But:

```php
ActionData(
    key: 'disbursement.retry',
    label: 'Retry Disbursement',
    target: ...
)
```

This allows the same action to be:

- rendered
- routed
- measured
- attributed
- audited
- automated

without duplicating behavior.

---

# 7. Universal Action Resolution

The future resolution model:

```text
Workflow Event
        ↓
Action Registry
        ↓
Action Providers
        ↓
Feature Profile Rules
        ↓
Permission Filter
        ↓
State Filter
        ↓
ActionData[]
```

The renderer receives only the final actions.

No rendering layer should invent actions.

---

# 8. Action Registry

The target package should provide a centralized registry.

Example:

```text
claim.succeeded
    ↓
claim.open_rider

claim.awaiting_documents
    ↓
claim.upload_document

disbursement.failed
    ↓
disbursement.retry
beneficiary.contact
audit.view
```

The registry becomes the canonical source of workflow action availability.

---

# 9. Action Providers

Actions should be implemented as providers.

Example:

```text
OpenRiderUrlAction
RetryDisbursementAction
UploadSettlementDocumentAction
ViewCampaignAction
```

Each provider decides:

```text
supports?
label?
target?
metadata?
```

The package should support provider composition and extension.

---

# 10. Feature Profile Awareness

Feature Profiles become first-class action resolution inputs.

Examples:

```text
default
dbp
lgu
dswd
private-bank
```

Same workflow event:

```text
claim.succeeded
```

may resolve differently:

```text
default
    View Claim

DBP
    Review Beneficiary

LGU
    Process Assistance

Private Bank
    Open Dashboard
```

The package must support profile-aware action resolution.

---

# 11. Campaign-Aware Actions

The package should support campaign attribution.

Every action may optionally carry:

```text
campaign_id
campaign_run_id
segment_id
batch_id
```

This enables:

```text
rendered
clicked
completed
failed
abandoned
```

to become campaign metrics.

Campaigns should not own action definitions.

Campaigns should own action attribution.

---

# 12. Multi-Surface Rendering

Actions should be surface-independent.

The same action may appear in:

```text
Claim UI
Campaign UI
Cockpit
Email
SMS
Slack
Webhook
AI Copilot
```

Rendering becomes a concern of the consumer.

The action model remains unchanged.

---

# 13. Deep Link Architecture

The package should support target abstraction.

Actions should not store raw URLs as canonical behavior.

Instead:

```text
Action
    ↓
Target
    ↓
Target Driver
    ↓
Final Destination
```

Supported targets:

```text
route
signed_route
external_url
api
mobile_deep_link
connector
action_router
```

The package becomes responsible for target resolution.

---

# 14. Action Router

The package should provide a generic action router.

Example:

```text
GET /actions/{token}
```

Responsibilities:

```text
validate token
validate actor
record click
record analytics
check expiry
resolve target
redirect
invoke connector
```

The action router becomes the universal action entry point.

---

# 15. Action Analytics Platform

Every meaningful workflow action should be measurable.

Target events:

```text
rendered
viewed
clicked
invoked
pending
completed
failed
expired
cancelled
```

This enables analytics such as:

```text
completion rate
conversion rate
drop-off rate
connector success rate
campaign effectiveness
operator productivity
```

---

# 16. Action Lifecycle

Future lifecycle:

```text
created
rendered
viewed
clicked
invoked
pending
completed
failed
expired
cancelled
```

This lifecycle should be standardized.

Every connector and renderer should understand it.

---

# 17. Action Runs

Every invocation should create a runtime execution record.

Future model:

```text
WorkflowActionRun
```

Responsibilities:

```text
correlation
status tracking
connector invocation
callback handling
analytics
timeouts
idempotency
```

This becomes essential for asynchronous automation.

---

# 18. Connector Platform

Connectors become the extension seam.

Supported categories:

```text
webhook
pipedream
n8n
zapier
slack
email
sms
mcp
ai_agent
human_review
```

The package should not be tightly coupled to any specific provider.

It should provide a common connector contract.

---

# 19. Asynchronous Automation

Future connector flow:

```text
Action Clicked
        ↓
Action Run Created
        ↓
Connector Invoked
        ↓
Pending
        ↓
Callback Received
        ↓
Completed / Failed
```

The package becomes the orchestrator of connector correlation.

---

# 20. Agentic AI Integration

The package should eventually support AI agents through the same connector model.

Future:

```text
Action
    ↓
AI Agent Connector
    ↓
Suggestion
Draft
Classification
Preparation
Execution Proposal
```

The package should not distinguish AI from other connector categories.

Both are external action performers.

---

# 21. Human-in-the-Loop Actions

The same model should support human approval workflows.

Examples:

```text
Approve Claim
Review Beneficiary
Approve Settlement
Review Campaign
```

Human review becomes another action target.

---

# 22. Action Playbooks

The package should provide optional action playbooks.

Playbooks answer:

```text
For this workflow event,
which approved actions should appear?
```

Inputs:

```text
feature profile
audience
surface
channel
permissions
workflow state
```

Outputs:

```text
enabled actions
priority
labels
overrides
```

---

# 23. Action Catalog

The package should maintain a discoverable action catalog.

Future users should be able to answer:

```text
What workflow actions exist?
Where are they used?
Who can execute them?
What do they do?
```

This becomes critical for governance.

---

# 24. UI Component Library

The package should provide generic reusable UI components.

Examples:

```text
ActionButton
ActionList
ActionMenu
ActionRenderer
ActionCard
ActionTimeline
```

These components should work across:

```text
x-change
x-campaign
Cockpit
future applications
```

No business-specific UI should live inside the package.

---

# 25. Optional Administration Module

The package may eventually provide optional administration screens.

Potential modules:

```text
Action Catalog
Action Playbooks
Action Analytics
Action Runs
Connector Runs
```

These screens should remain optional.

The package must remain usable without them.

---

# 26. Integration with Claim Compiler

Future claim compiler output:

```text
Claim Experience
    ↓
actions[]
```

Rather than:

```text
success.rider.url
```

the experience may expose:

```text
claim.open_rider
```

The claim compiler still owns workflow semantics.

`x-action` owns action representation.

---

# 27. Integration with Execution Engine

Future execution engine:

```text
executes workflows
returns state
```

`x-action`:

```text
interprets continuation opportunities
```

The package never becomes the workflow engine.

It remains the workflow continuation layer.

---

# 28. Integration with x-feedback

Future feedback flow:

```text
Workflow Event
        ↓
Action Resolution
        ↓
Feedback Template
        ↓
Channel Rendering
```

Feedback becomes action-aware but not action-owning.

---

# 29. Integration with x-journal

Action events become journal events.

Examples:

```text
Action Clicked
Action Completed
Action Failed
Connector Callback Received
```

This creates a unified operational history.

---

# 30. Integration with Cockpit

Cockpit becomes a major consumer.

Future Cockpit actions:

```text
Review Beneficiary
Retry Disbursement
Approve Request
View Audit Trail
Export Batch
```

Cockpit should not define action semantics.

Cockpit should consume resolved actions.

---

# 31. Lifecycle Scenario Runner Integration

Future scenario runner should understand actions.

Examples:

```php
expectAction('claim.confirm');

followAction('claim.confirm');

expectActionCompleted('claim.confirm');
```

Actions become testable workflow artifacts.

---

# 32. Long-Term Ecosystem Role

Future ecosystem:

```text
Workflow Packages
        ↓
Action Resolution
        ↓
x-action
        ↓
Rendering
Routing
Analytics
Automation
AI
Connectors
```

Every package emits actions.

Every consumer understands actions.

---

# 33. Success Criteria

The package reaches target state when:

1. Workflow actions become first-class objects.
2. Actions are reusable across all surfaces.
3. Actions are measurable.
4. Actions are campaign-attributable.
5. Actions are connector-extensible.
6. Actions support asynchronous execution.
7. Actions support AI-agent execution.
8. Claim compiler integration remains non-invasive.
9. Workflow ownership remains in host applications.
10. External automation never becomes the authority for money or compliance state.

---

# 34. Final Target Statement

`3neti/x-action` becomes the canonical workflow continuation platform for the x-change ecosystem.

It provides a universal language for answering:

> What can this actor do next?

across claims, campaigns, settlements, cockpit operations, notifications, automation systems, and future AI agents while preserving workflow ownership inside the applications that actually execute the business logic.
