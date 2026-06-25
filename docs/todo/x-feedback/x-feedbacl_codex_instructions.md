# x-feedback Package — Master Codex Instruction

## Mission

You are tasked with designing and scaffolding the new package:

```text
3neti/x-feedback
```

This package will become the centralized feedback, notification, delivery, retry, in-app notification, and communication infrastructure layer for the x-change ecosystem.

Treat this as a long-running architectural program, not a one-shot feature ticket.

The goal is to evolve the existing scattered feedback/notification logic into a clean, event-driven, driver-based, observable communication package while preserving existing behavior and package boundaries.

---

# Repository Locations

## x-change package

Use the x-change package as the primary consuming package:

```text
/Users/rli/PhpstormProjects/x-change-sandbox/packages/x-change
```

Do not scaffold x-feedback inside the x-change package.

x-change should consume x-feedback through contracts, events, DTOs, adapters, and configuration.

---

## New package location

Create or work inside:

```text
/Users/rli/PhpstormProjects/packages/x-feedback
```

If the package already exists, inspect it first before changing anything.

If the package does not exist, scaffold it as a Laravel-compatible package.

---

## Other package locations

Other packages are located under:

```text
/Users/rli/PhpstormProjects/packages
```

Relevant packages may include:

```text
voucher
contact
x-journal
x-campaign
settlement-envelope
form-flow
wallet
emi-core
```

Inspect them only when needed to understand integration boundaries.

---

# Tooling Expectations

Laravel Boost is available and should be used as the primary Laravel inspection mechanism.

Use Laravel Boost to inspect:

```text
routes
controllers
requests
service providers
bindings
models
actions
DTOs
tests
package structure
configuration
events
listeners
notifications
queues
```

Also use normal repository tools when useful:

```bash
git status
git diff
git log
git branch
rg
find
composer test
vendor/bin/pest
npm run test
npm run test:frontend
```

Discover the actual test commands before assuming them.

---

# Required Reading

Before making any code changes, read and internalize the following documents.

They should be created or located under:

```text
docs/architecture/x-feedback
```

Required documents:

```text
docs/architecture/x-feedback/01-current-state.md
docs/architecture/x-feedback/02-target-state.md
docs/architecture/x-feedback/03-evolution-plan.md
docs/architecture/x-feedback/04-test-strategy.md
docs/architecture/x-feedback/05-architecture-invariants.md
```

Also read:

```text
docs/architecture/x-feedback/x-feedback_functional_specifications.md
docs/architecture/x-feedback/X_FEEDBACK_COMPASS.md
```

If these documents do not yet exist, create them using the architectural direction provided in this instruction.

These documents are authoritative.

Do not redesign them casually.

If implementation findings contradict the documents, stop and report the discrepancy before proceeding.

---

# Core Thesis

The architectural direction is:

```text
Workflows emit events.
x-feedback turns selected events into Feedback Intents.
Feedback Intents become channel-specific deliveries.
Deliveries are tracked, retried, expired, and surfaced operationally.
```

The target runtime is:

```text
Domain Event
    ↓
Feedback Mapper
    ↓
Feedback Intent
    ↓
Template / Feature Profile Resolution
    ↓
Feedback Dispatcher
    ↓
Channel Driver
    ↓
Feedback Delivery Record
    ↓
Delivery Receipt / Journal Event
```

---

# Package Positioning

## x-change owns

```text
business workflows
claim lifecycle
disbursement lifecycle
settlement lifecycle
event semantics
CTA/action meaning
artifact meaning
```

x-change emits domain events.

x-change does not directly send channel-specific notifications.

---

## x-feedback owns

```text
feedback intents
message rendering
template resolution
channel routing
driver dispatch
delivery records
retry management
freshness checks
in-app notification primitives
delivery receipt generation
feedback UI components
```

x-feedback does not own business workflows.

---

## x-journal owns

```text
audit history
durable timeline
business narrative
```

x-feedback may emit journal events, but x-journal remains the historical source of truth.

---

## x-campaign owns

```text
mass distribution
audiences
campaign execution
beneficiary outreach
```

x-feedback may deliver campaign messages, but it must not become a campaign package.

---

# Full Roadmap

You must understand the full roadmap, but you are only authorized to execute the current slice.

## Slice 0 — Documentation, Discovery, and Baseline

Create or verify the architecture documents.

Inspect existing notification behavior in x-change / redeem-x-derived code.

Produce discovery report.

No runtime package behavior yet unless required for skeleton.

---

## Slice 1 — Package Skeleton

Scaffold package structure:

```text
src/
    Contracts/
    Data/
    Drivers/
    Events/
    Exceptions/
    Jobs/
    Models/
    Policies/
    Services/
    Support/

config/
database/migrations/
resources/js/components/
tests/
docs/
```

Register service provider, config publishing, migrations, and test environment.

---

## Slice 2 — Core Feedback Intent Model

Introduce:

```text
FeedbackIntentData
FeedbackRecipientData
FeedbackMessageData
FeedbackActionData
FeedbackArtifactData
FeedbackDeliveryData
```

Introduce contracts:

```text
FeedbackDispatcher
FeedbackChannelDriver
FeedbackTemplateResolver
FeedbackCredentialResolver
FeedbackFreshnessPolicy
```

No real external sending yet.

---

## Slice 3 — Delivery Model and State Machine

Introduce:

```text
FeedbackDelivery
FeedbackDeliveryAttempt
FeedbackDeliveryStatus enum
```

Canonical delivery states:

```text
pending
queued
sending
sent
delivered
failed_retryable
retry_scheduled
failed_final
expired
cancelled
```

Add state transition tests.

---

## Slice 4 — Initial Drivers

Implement safe, local-first drivers:

```text
null
log
in_app
mail
webhook
```

External integrations should be mocked/faked in tests.

Do not implement SMS, Slack, WhatsApp, or Viber yet unless explicitly approved.

---

## Slice 5 — Event Mapping Integration

Introduce event-to-feedback mapping.

x-change events should map into Feedback Intents through adapter classes.

Workflows must emit domain events, not send notifications directly.

---

## Slice 6 — Retry and Freshness Engine

Implement:

```text
retry policies
backoff
attempt tracking
expiration
freshness checks
stale-message prevention
```

Notifications must not be retried blindly.

---

## Slice 7 — Notification Routes and Preferences

Introduce:

```text
NotificationRoute
NotificationPreference
HasNotificationRoutes
```

Attach capability to appropriate models such as:

```text
User
Contact
Issuer / Merchant
CampaignRecipient
```

Do not make Voucher generally notifiable.

Voucher may contain feedback instructions, but it is not itself a person or endpoint.

---

## Slice 8 — Delivery Receipts

Implement operational feedback about feedback.

Examples:

```text
feedback.all_sent
feedback.partially_failed
feedback.final_failed
feedback.expired
```

Default receipt channel should be:

```text
in_app
```

for issuer/operator visibility.

---

## Slice 9 — Feature Profile Template Resolution

Implement template resolution by:

```text
feature_profile
+
event_key
+
channel
```

Examples:

```text
default.claim.succeeded.mail
dbp.claim.succeeded.mail
lgu.claim.succeeded.sms
```

Feature Profiles affect wording, branding, templates, and rendered action labels.

They must not affect domain logic.

---

## Slice 10 — Artifact Rendering Policy

Introduce:

```text
FeedbackArtifactRenderingPolicy
```

x-feedback owns artifact presentation rules only.

Examples:

```text
selfie → mail preview allowed
signature → mail preview allowed
location → mail preview allowed
government_id → mail preview disabled
medical_certificate → link only
```

Do not attach original artifacts by default.

No email attachments in initial implementation.

Use previews, links, or hiding behavior only.

---

## Slice 11 — Reusable UI Components

Provide package-owned reusable components only.

Examples:

```text
NotificationBell.vue
NotificationBadge.vue
NotificationList.vue
NotificationItem.vue
DeliveryStatusBadge.vue
DeliveryTimeline.vue
DeliveryAttemptTable.vue
ChannelIcon.vue
RetryDeliveryButton.vue
```

Do not create Cockpit pages.

Cockpit / host app owns pages, layouts, and routes.

---

## Slice 12 — Tenant Credential Resolution

Introduce tenant/institution-specific credential resolution.

Examples:

```text
DBP SMTP
DBP SMS provider
DBP Slack workspace
DBP webhook signing secret
LGU SMTP
LGU SMS provider
```

Credentials must be resolved, not hardcoded.

---

## Slice 13 — Slack Driver

Add Slack support through a driver.

Track delivery states and provider responses.

---

## Slice 14 — SMS Driver

Add SMS support through a driver.

Provider implementation should be adapter-based.

---

## Deferred

Do not implement yet:

```text
WhatsApp
Viber
Microsoft Teams
mobile push
voice notifications
AI-generated notifications
campaign orchestration
full Cockpit pages
artifact storage package
```

Only create extension points where appropriate.

---

# Current Authorization

You are authorized to execute only:

```text
Slice 0 — Documentation, Discovery, and Baseline
```

and, if the package does not yet exist, a minimal documentation-only package skeleton necessary to store the architecture docs and Compass.

Do not proceed to Slice 1 implementation without explicit human approval.

---

# Slice 0 Mission

Create a clear baseline for x-feedback before implementation.

The goal is not to build the notification system yet.

The goal is to:

```text
document the current state
document the target state
document the phased plan
document the test strategy
document the invariants
create and maintain the Compass
inspect existing notification behavior
identify migration seams
```

---

# Slice 0 Required Activities

## 1. Confirm repository locations

Confirm:

```text
/Users/rli/PhpstormProjects/packages/x-feedback
/Users/rli/PhpstormProjects/x-change-sandbox/packages/x-change
/Users/rli/PhpstormProjects/packages
```

If paths differ, stop and report.

---

## 2. Read existing architecture documents

Read:

```text
01-current-state.md
02-target-state.md
03-evolution-plan.md
04-test-strategy.md
05-architecture-invariants.md
```

If missing, create them.

---

## 3. Create Compass

Create and maintain:

```text
docs/architecture/x-feedback/X_FEEDBACK_COMPASS.md
```

See Compass requirements below.

---

## 4. Inspect current notification implementation

Inspect x-change and related legacy code for:

```text
Laravel Notifications
SendFeedbacks pipeline
mail notifications
SMS notifications
webhook notifications
voucher instructions feedback fields
notification config
notification tests
queue behavior
pipeline behavior
in-app/database notifications
```

Look for:

```text
SendFeedbacks
SendFeedbacksNotification
voucher-pipeline.php
notifications.php
instructions.feedback
mail
sms
webhook
engage_spark
database notifications
```

---

## 5. Produce Slice 0 discovery report

Create:

```text
docs/architecture/x-feedback/reports/000-slice-0-discovery.md
```

Report must include:

```text
files inspected
existing notification paths
current channels
current delivery tracking gaps
current retry behavior
current artifact rendering behavior
current UI surfaces
migration seams
risks / uncertainties
test commands discovered
```

---

## 6. Do not change production behavior

Slice 0 must not alter runtime behavior.

Do not replace existing notifications.

Do not wire x-feedback into x-change yet.

Do not change voucher-pipeline.php.

Do not change claim/redeem/disbursement behavior.

---

# Forbidden During Slice 0

Do not introduce runtime production logic for:

```text
FeedbackDispatcher
FeedbackDelivery
FeedbackChannelDriver
NotificationRoute
NotificationPreference
SlackFeedbackDriver
SmsFeedbackDriver
WebhookFeedbackDriver
FeatureProfileTemplateResolver
RetryEngine
FreshnessPolicy
```

unless explicitly approved.

Do not modify existing workflow behavior.

Do not delete legacy notification code.

Do not introduce database migrations yet unless explicitly approved.

Do not create Cockpit pages.

---

# Architecture Invariants To Preserve

Preserve all rules in:

```text
05-architecture-invariants.md
```

Especially:

```text
x-feedback does not own business meaning
workflows emit events, not notifications
FeedbackIntent is the boundary object
channels are drivers
delivery must be observable
retry logic belongs to x-feedback
freshness must be checked before sending
in_app is a channel, not an audit log
x-journal owns historical narrative
x-campaign owns mass distribution
CTA ownership is upstream
artifact meaning is upstream
artifact storage is not x-feedback responsibility
credentials are resolved, not hardcoded
provider failures must not break workflows
UI components are package-owned; pages are host-owned
```

---

# Compass Management Addendum

The x-feedback evolution is expected to span multiple slices, multiple commits, and potentially multiple Codex sessions.

Codex must maintain a living Compass document throughout the migration.

The Compass is the operational source of truth for:

```text
where we are
what is completed
what remains
what changed
what risks exist
what decision points exist
```

The Compass supplements the stable architecture documents.

---

# Compass Location

Create and maintain:

```text
docs/architecture/x-feedback/X_FEEDBACK_COMPASS.md
```

If the architecture documentation lives elsewhere, place the Compass beside the architecture documents.

---

# Compass Update Frequency

Update the Compass whenever any of the following occur:

```text
a slice begins
a slice completes
a significant discovery is made
a new risk is identified
a new architectural decision is made
a test strategy changes
a migration plan changes
a package boundary changes
```

At minimum:

```text
one Compass update per completed slice
```

---

# Required Compass Structure

## 1. Mission

A concise statement of the migration goal.

Example:

```text
Evolve scattered notification logic into an event-driven, driver-based, observable communication infrastructure package.
```

---

## 2. Current Position

Example:

```text
Current Slice:
    Slice 0 — Documentation, Discovery, and Baseline

Status:
    In Progress

Last Updated:
    YYYY-MM-DD HH:MM
```

---

## 3. Slice Progress Table

Example:

| Slice | Name | Status |
|---|---|---|
| 0 | Documentation, Discovery, and Baseline | In Progress |
| 1 | Package Skeleton | Pending |
| 2 | Core Feedback Intent Model | Pending |
| 3 | Delivery Model and State Machine | Pending |
| 4 | Initial Drivers | Pending |
| 5 | Event Mapping Integration | Pending |
| 6 | Retry and Freshness Engine | Pending |
| 7 | Notification Routes and Preferences | Pending |
| 8 | Delivery Receipts | Pending |
| 9 | Feature Profile Template Resolution | Pending |
| 10 | Artifact Rendering Policy | Pending |
| 11 | Reusable UI Components | Pending |
| 12 | Tenant Credential Resolution | Pending |
| 13 | Slack Driver | Pending |
| 14 | SMS Driver | Pending |

Status values:

```text
Pending
In Progress
Blocked
Completed
Deferred
```

---

## 4. Completed Work

Record:

```text
docs created
files created
tests added
reports created
architecture decisions implemented
commits made
```

Prefer concise bullet lists.

---

## 5. Current Discoveries

Capture important findings discovered during inspection.

Examples:

```text
Legacy SendFeedbacks stage reads voucher.instructions.feedback.
Email supports artifact previews.
Webhook payload avoids raw selfie/signature data.
No centralized delivery state model exists.
```

These discoveries must survive across Codex sessions.

---

## 6. Risks

Record active risks.

Examples:

```text
Legacy feedback behavior may be coupled to redemption pipeline.
Provider failures may currently be invisible.
Artifact rendering may leak sensitive data if not controlled.
Feature Profile template resolution is not yet implemented.
```

Include mitigation plans where possible.

---

## 7. Architectural Decisions

Record decisions that affect implementation.

Examples:

```text
x-feedback owns communication infrastructure only.
x-change owns event meaning and CTA semantics.
x-journal owns audit/history.
x-campaign owns mass distribution.
in_app is a channel, not a log.
Voucher is not generally notifiable.
```

This section becomes the operational ADR log for the migration.

---

## 8. Test Coverage Status

Track:

```text
documentation coverage
characterization tests
state machine tests
driver tests
template tests
retry tests
freshness tests
UI component tests
```

Example:

```text
Documentation Coverage:
    Started

Characterization Coverage:
    Not Started

Delivery State Machine Tests:
    Not Started
```

Values do not need to be exact percentages.

Reasonable estimates are acceptable.

---

## 9. Next Recommended Slice

Example:

```text
Recommended Next Slice:
    Slice 1 — Package Skeleton

Reason:
    Slice 0 documentation and discovery are complete.
```

---

## 10. Open Questions

Track unresolved architectural questions.

Examples:

```text
Should webhook sending use spatie/laravel-webhook-server?
Should incoming webhook verification live in x-feedback or host integrations?
Should notification route preferences be package-owned or host-owned?
Should artifact rendering policies be configurable by tenant?
```

---

# Required Reporting Behavior

Whenever a slice completes, include a Compass summary in the status report.

Example:

```text
Compass Update

Current Slice:
    Slice 0 Complete

Completed:
    Created architecture docs and discovery report.

Risks:
    Legacy feedback pipeline still requires characterization tests.

Next Slice:
    Slice 1 — Package Skeleton
```

---

# Compass Preservation Rule

Before beginning work in a new Codex session:

```text
1. Read X_FEEDBACK_COMPASS.md
2. Read the five architecture documents
3. Read the functional specification
4. Reconcile differences
5. Continue from the latest recorded position
```

Do not rely solely on conversation history.

The Compass is the persisted migration memory.

---

# Compass Quality Bar

A future AI agent with no prior conversation history should be able to open:

```text
X_FEEDBACK_COMPASS.md
```

and immediately understand:

```text
where the x-feedback migration currently stands
what has been completed
what remains
what risks exist
what should happen next
```

---

# Expected First Response From Codex

Do not start coding immediately.

First respond with:

```text
1. repository locations confirmed
2. architecture documents found or missing
3. tooling detected, including Laravel Boost availability
4. test commands discovered or to be discovered
5. Slice 0 inspection plan
6. risks and caution areas
```

Then proceed with documentation creation/inspection and the Slice 0 discovery report.

---

# Commit Discipline

Use coherent commits.

Preferred rule:

```text
1 slice = 1 green commit
```

For Slice 0, commit only after:

```text
architecture docs are present
Compass is created or updated
discovery report is created
git diff is reviewed
no runtime behavior is changed
```

Suggested commit message:

```text
docs(x-feedback): add slice 0 architecture baseline
```

---

# Stop Conditions

Stop and report immediately if:

```text
repository paths differ significantly
architecture docs conflict with source findings
Laravel Boost is unavailable
existing package structure is unclear
test suite has unrelated pre-existing failures
runtime code changes appear necessary during Slice 0
legacy feedback behavior is unclear
artifact handling appears to expose sensitive data
```

Do not paper over these.

Do not silently guess.

---

# Slice 0 Definition of Done

Slice 0 is complete only when:

```text
architecture documents exist
functional specification exists
Compass exists and is updated
current notification behavior has been inspected
Slice 0 discovery report exists
migration seams are identified
no runtime behavior has changed
no production feedback classes have been introduced
```

At completion, stop and request approval before proceeding to:

```text
Slice 1 — Package Skeleton
```
