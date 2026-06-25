# 02-target-state.md
# x-feedback Evolution Plan
## Target State Architecture

### Status

This document describes the desired end-state architecture for:

```text
3neti/x-feedback
```

The package shall become the canonical communication and notification infrastructure for the x-change ecosystem.

---

# Vision

The platform should evolve from:

```text
workflow-driven notifications
```

to:

```text
event-driven communication infrastructure
```

where business workflows emit events and x-feedback becomes solely responsible for:

```text
message generation
message routing
message delivery
message tracking
message visibility
```

---

# Core Architectural Principle

x-feedback shall not own business meaning.

Business meaning belongs to:

```text
x-change
```

x-feedback shall own communication.

Examples:

```text
x-change
    ClaimSucceeded

x-feedback
    notify issuer
    notify claimant
    notify webhook
    notify Slack
```

---

# Package Positioning

## x-change

Owns:

```text
workflow execution
claim lifecycle
disbursement lifecycle
settlement lifecycle
authorization lifecycle
event semantics
```

Produces:

```text
Domain Events
```

---

## x-feedback

Owns:

```text
feedback intents
templates
channels
delivery
tracking
retry management
notification center
```

Produces:

```text
communication outcomes
```

---

## x-journal

Owns:

```text
audit history
timeline
narrative history
```

Produces:

```text
durable records
```

---

## x-campaign

Owns:

```text
audiences
campaigns
mass distribution
beneficiary outreach
```

Produces:

```text
campaign events
```

which may be consumed by x-feedback.

---

# Event-Driven Architecture

Target flow:

```text
Domain Event
    ↓

Feedback Mapper

    ↓

Feedback Intent

    ↓

Dispatcher

    ↓

Channel Drivers

    ↓

Delivery Records

    ↓

Receipts
```

---

# Feedback Intent Model

Every communication shall originate from a:

```text
FeedbackIntent
```

Examples:

```text
claim.succeeded
claim.failed
otp.approval.required
disbursement.failed
campaign.delivery.failed
```

Intent contains:

```text
title
body
actions
artifacts
priority
recipients
channels
metadata
```

---

# Recipient Architecture

Recipients shall be represented as:

```text
Notifiable Entities
```

Examples:

```text
User
Contact
CampaignRecipient
Issuer
Operator
ExternalEndpoint
```

---

# Notification Routes

Recipients shall expose:

```text
Notification Routes
```

Examples:

```text
email
sms
slack
webhook
whatsapp
viber
```

Example:

```text
User
    ├─ email
    ├─ sms
    └─ slack

Contact
    ├─ sms
    └─ whatsapp
```

The platform shall avoid hardcoded channel fields.

---

# Notification Preferences

Recipients shall support:

```text
NotificationPreference
```

Examples:

```text
claim.succeeded → sms enabled

claim.succeeded → email enabled

campaign.delivery → sms disabled
```

This enables:

```text
channel-level opt-in
channel-level opt-out
event-level preferences
```

---

# Channel Abstraction

All delivery mechanisms shall be implemented as:

```text
FeedbackChannelDriver
```

Drivers:

```text
mail
sms
webhook
slack
in_app
log
null
```

Future:

```text
whatsapp
viber
teams
mobile_push
messenger
```

Drivers shall be hot-swappable.

---

# In-App Notification Architecture

x-feedback shall provide:

```text
Notification Center
```

conceptually.

Capabilities:

```text
unread notifications
read notifications
archived notifications
dismissed notifications
```

Operations:

```text
mark read
mark unread
bulk read
bulk archive
```

---

# Notification Badge Support

The package shall support:

```text
notification bell
unread count
badge indicators
```

through reusable UI components.

---

# Delivery Tracking

Every send operation shall create:

```text
FeedbackDelivery
```

records.

The platform shall always know:

```text
who
what
when
where
outcome
```

for every attempted communication.

---

# Delivery State Machine

All deliveries shall progress through:

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

# Retry Management

Retry behavior shall be:

```text
channel-specific
configurable
observable
```

Examples:

```text
sms
    5 retries

mail
    8 retries

webhook
    exponential backoff
```

---

# Freshness Management

Notifications shall support expiration.

Examples:

```text
OTP request
    expires after 5 minutes

pending payout alert
    expires after payout completion

claim reminder
    expires after claim completion
```

The system shall avoid delivering stale communications.

---

# Delivery Receipts

The platform shall support:

```text
feedback about feedback
```

Examples:

```text
notification delivered

notification partially delivered

notification failed
```

These receipts are distinct from business notifications.

---

# Operational Visibility

Operators shall be able to answer:

```text
Was it sent?

Was it delivered?

Did it fail?

Will it retry?

What happened?
```

without consulting external providers.

---

# Feedback Console

The package shall expose APIs and components for:

```text
delivery timeline
attempt history
retry status
provider responses
channel status
```

---

# Artifact Rendering Architecture

Artifacts shall be supported through:

```text
Artifact References
```

Examples:

```text
selfie
signature
location
government_id
medical_certificate
loa
```

x-feedback shall not own artifact storage.

---

# Artifact Rendering Policy

The package shall own:

```text
how artifacts appear
```

Examples:

```text
email preview
slack thumbnail
secure link
hide
```

The package shall not own:

```text
artifact retention
artifact permissions
artifact storage
```

---

# Feature Profile Integration

Templates shall resolve through:

```text
feature profile
+
event
+
channel
```

Examples:

```text
dbp
claim.succeeded
mail

lgu
claim.succeeded
mail
```

This allows:

```text
institution-specific language
institution-specific branding
institution-specific CTAs
```

without changing workflow logic.

---

# CTA Support

Every notification may contain:

```text
actions
```

Examples:

```text
View Claim
Approve Request
Retry Disbursement
Open Dashboard
```

Important:

```text
CTA ownership does not belong to x-feedback.
```

Actions originate from upstream workflow mappers.

x-feedback only renders them.

---

# Multi-Tenant Credential Resolution

The package shall support:

```text
tenant-specific credentials
```

Examples:

```text
DBP SMTP
DBP Slack
DBP SMS Provider

LGU SMTP
LGU SMS Provider
```

Credential resolution shall occur dynamically.

---

# UI Ownership Model

## x-feedback Owns

Reusable components:

```text
NotificationBell
NotificationBadge
NotificationList
NotificationItem

DeliveryTimeline
DeliveryStatusBadge
RetryButton
ChannelIcon
```

---

## Cockpit Owns

Workflow pages:

```text
Claim Pages
Campaign Pages
Settlement Pages
Operator Dashboards
```

The package provides building blocks.

The host application composes experiences.

---

# Journal Integration

Every significant delivery event shall be journal-aware.

Examples:

```text
feedback.created
feedback.sent
feedback.failed
feedback.expired
feedback.retry_scheduled
```

x-journal remains the source of truth for historical narrative.

---

# Monitoring & Health

The package shall expose:

```text
channel health
queue depth
retry backlog
provider failures
```

Examples:

```text
SMTP unavailable
Slack unavailable
Webhook endpoint down
SMS credits depleted
```

---

# Future Extensibility

The architecture shall support future additions without redesign.

Examples:

```text
AI-assisted notifications
WhatsApp
Viber
Microsoft Teams
Mobile Push
Voice Notifications
```

through additional drivers.

---

# End-State Definition

The target architecture is achieved when:

```text
all communication flows through Feedback Intents

all channels are driver-based

all deliveries are tracked

all retries are managed

all templates are Feature Profile aware

all notifications support actions

all operators have visibility

all workflows remain notification-agnostic
```

At that point:

```text
x-feedback becomes the communication backbone of the x-change ecosystem.
```
