# 03-evolution-plan.md
# x-feedback Evolution Plan
## Migration & Implementation Roadmap

### Status

This document defines the phased evolution strategy for:

```text
3neti/x-feedback
```

The objective is to move from the current notification implementation toward a fully event-driven communication platform without disrupting existing x-change functionality.

---

# Guiding Principle

The migration must be:

```text
incremental
backward compatible
observable
reversible
```

At no point should existing notification capabilities be lost.

The package should initially coexist with legacy implementations before eventually becoming the primary communication layer.

---

# Strategic Goal

Transition from:

```text
Workflow
    ↓
Notification
```

to:

```text
Workflow
    ↓
Domain Event
    ↓
Feedback Intent
    ↓
Feedback Dispatcher
    ↓
Channel Drivers
```

without requiring immediate workflow rewrites.

---

# Phase 0
# Discovery & Package Foundation

## Objective

Establish package boundaries and architecture before implementing delivery behavior.

---

## Deliverables

### Package Skeleton

Create:

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
```

---

### Documentation

Create:

```text
01-current-state.md
02-target-state.md
03-evolution-plan.md
04-test-strategy.md
05-architecture-invariants.md
```

---

### Functional Specification

Create:

```text
x-feedback_functional_specifications.md
```

---

### Architecture Compass

Create:

```text
x-feedback_compass.md
```

Track:

```text
current phase
completed milestones
next milestone
known risks
architectural decisions
```

---

## Success Criteria

```text
Package structure exists.
Architecture documented.
No runtime behavior yet.
```

---

# Phase 1
# Core Feedback Engine

## Objective

Introduce Feedback Intent as the primary communication abstraction.

---

## Deliverables

### DTOs

Create:

```text
FeedbackIntentData
FeedbackRecipientData
FeedbackDeliveryData
FeedbackRouteData
FeedbackMessageData
```

---

### Contracts

Create:

```php
FeedbackChannelDriver
FeedbackTemplateResolver
FeedbackDispatcher
FeedbackCredentialResolver
```

---

### Services

Create:

```text
FeedbackManager
FeedbackIntentFactory
FeedbackDispatcher
```

---

### Delivery Model

Create:

```text
FeedbackDelivery
```

with initial states:

```text
pending
queued
sent
failed
```

---

## Success Criteria

```text
Feedback Intent exists.
Dispatcher exists.
Drivers can be registered.
No workflow integration yet.
```

---

# Phase 2
# Channel Driver Architecture

## Objective

Introduce pluggable delivery channels.

---

## Initial Drivers

Implement:

```text
mail
webhook
in_app
log
null
```

Do not implement:

```text
sms
slack
whatsapp
viber
```

yet.

---

## Requirements

All drivers must support:

```text
send
health
supports
```

---

## Success Criteria

```text
Drivers are swappable.
Intent can be dispatched.
Delivery records are created.
```

---

# Phase 3
# Event-Driven Integration

## Objective

Connect x-feedback to x-change events.

---

## Create

```text
FeedbackEventMapper
```

Examples:

```text
ClaimSucceeded
ClaimFailed
DisbursementFailed
ApprovalRequired
```

---

## Important Rule

Workflows must not know channels.

Workflows emit:

```text
Domain Event
```

x-feedback determines:

```text
delivery
routing
rendering
```

---

## Success Criteria

```text
At least one x-change event generates a Feedback Intent.
```

---

# Phase 4
# Delivery Tracking & Retry Engine

## Objective

Introduce operational reliability.

---

## Expand State Machine

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

---

## Retry Policies

Introduce:

```text
max_attempts
backoff
expiration
```

per channel.

---

## Freshness Policies

Introduce:

```text
isStillSendable()
```

support.

---

## Success Criteria

```text
Retries observable.
Stale messages prevented.
```

---

# Phase 5
# Notification Routes & Preferences

## Objective

Decouple recipients from channel-specific fields.

---

## Introduce

```text
NotificationRoute
NotificationPreference
```

---

## Add Trait

```php
HasNotificationRoutes
```

---

## Initial Support

```text
User
Contact
```

---

## Success Criteria

```text
Recipients expose routes.
Channels resolved dynamically.
```

---

# Phase 6
# In-App Notification System

## Objective

Provide first-class notification center capabilities.

---

## Deliverables

Models:

```text
NotificationInbox
NotificationItem
```

Capabilities:

```text
read
unread
archive
dismiss
```

---

## APIs

Provide:

```text
notification count
notification list
mark read
mark unread
```

---

## Success Criteria

```text
In-app notifications functional.
Unread counts supported.
```

---

# Phase 7
# Delivery Receipt System

## Objective

Provide visibility into feedback outcomes.

---

## Introduce

```text
FeedbackReceipt
```

Examples:

```text
all delivered
partially delivered
delivery failed
```

---

## Default Channel

```text
in_app
```

for operators.

---

## Success Criteria

```text
Operators know whether feedback succeeded.
```

---

# Phase 8
# Feature Profile Integration

## Objective

Support institution-specific communication experiences.

---

## Introduce

Resolution strategy:

```text
profile
+
event
+
channel
```

---

## Profiles

Initial:

```text
default
dbp
lgu
dswd
```

---

## Support

```text
branding
wording
actions
templates
```

---

## Success Criteria

```text
Same event renders differently per profile.
```

---

# Phase 9
# Artifact Rendering Framework

## Objective

Support rich notification content.

---

## Introduce

```text
ArtifactData
FeedbackArtifactRenderingPolicy
```

---

## Initial Types

```text
selfie
signature
location
```

---

## Rules

Allow:

```text
preview
link
hide
```

Do not support:

```text
attachments
```

in initial implementation.

---

## Success Criteria

```text
Artifact previews rendered in email and in-app.
```

---

# Phase 10
# Slack Integration

## Objective

Add operational communication channels.

---

## Deliverables

```text
SlackFeedbackDriver
```

Support:

```text
channels
users
threading
links
```

---

## Success Criteria

```text
Slack deliveries tracked.
```

---

# Phase 11
# SMS Integration

## Objective

Reintroduce SMS using driver architecture.

---

## Deliverables

```text
SmsFeedbackDriver
```

Provider selection deferred to adapters.

---

## Success Criteria

```text
SMS behaves identically to other channels.
```

---

# Phase 12
# Operational Console

## Objective

Provide complete delivery observability.

---

## APIs

```text
delivery timeline
attempt history
provider responses
retry status
```

---

## Reusable Components

```text
NotificationBell
NotificationBadge
NotificationList

DeliveryTimeline
DeliveryStatusBadge
RetryDeliveryButton
```

---

## Important Boundary

Do not create Cockpit pages.

Provide:

```text
components
resources
contracts
```

only.

Cockpit owns:

```text
pages
routes
layouts
```

---

## Success Criteria

```text
Host application can build delivery dashboards.
```

---

# Phase 13
# Tenant Credential Resolution

## Objective

Support institution-specific infrastructure.

---

## Introduce

```text
FeedbackCredentialResolver
```

Examples:

```text
DBP SMTP
DBP Slack
DBP SMS
LGU SMTP
```

---

## Success Criteria

```text
Credentials resolved dynamically.
```

---

# Deferred Phases

Do not implement yet:

```text
WhatsApp
Viber
Teams
Push Notifications
AI-generated notifications
Voice Notifications
```

Only establish extension points.

---

# Migration Strategy

Existing notification logic should be migrated gradually.

Preferred pattern:

```text
Legacy Notification
    ↓
Feedback Intent
    ↓
Driver
```

rather than:

```text
Delete Legacy
    ↓
Rewrite Everything
```

---

# Definition of Done

The evolution is complete when:

```text
All communication originates from Feedback Intents.

All channels are drivers.

All deliveries are tracked.

All retries are observable.

All templates are Feature Profile aware.

All workflows are notification-agnostic.

All communication concerns are centralized in x-feedback.
```

At that point:

```text
x-feedback becomes the communication infrastructure layer for the entire x-change ecosystem.
```
