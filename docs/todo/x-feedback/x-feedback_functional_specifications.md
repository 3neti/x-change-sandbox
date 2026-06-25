# x-feedback Functional Specifications
## Version 1.0
## Status: Draft
## Package: 3neti/x-feedback

---

# 1. Purpose

x-feedback is the centralized feedback, notification, delivery, and communication package for the x-change ecosystem.

It provides:

- event-driven notifications
- message rendering
- channel routing
- delivery tracking
- delivery retry management
- in-app notifications
- operational feedback visibility

It does not own:

- workflow decisions
- business rules
- claim logic
- campaign orchestration
- audit history

---

# 2. Package Positioning

## x-change

Owns:

```text
business workflows
claim lifecycle
disbursement lifecycle
settlement lifecycle
event meaning
```

---

## x-feedback

Owns:

```text
message generation
message delivery
channel routing
notification rendering
delivery tracking
```

---

## x-journal

Owns:

```text
audit history
business timeline
activity narrative
```

---

## x-campaign

Owns:

```text
mass distribution
beneficiary campaigns
audience management
```

---

# 3. Core Principle

x-feedback does not decide:

```text
what happened
what should happen
```

x-feedback only decides:

```text
who should be informed
how they should be informed
whether delivery succeeded
```

---

# 4. Architecture

```text
Domain Event
    ↓

Feedback Mapper

    ↓

Feedback Intent

    ↓

Feedback Dispatcher

    ↓

Channel Drivers

    ↓

Delivery Records
```

---

# 5. Core Concepts

## Feedback Event

Represents:

```text
something happened
```

Examples:

```text
claim.succeeded
claim.failed
disbursement.failed
campaign.delivery.failed
```

Events originate outside x-feedback.

---

## Feedback Intent

Represents:

```text
someone should be informed
```

Contains:

```text
title
body
actions
artifacts
recipients
channels
```

---

## Feedback Delivery

Represents:

```text
one attempted delivery
```

Examples:

```text
email
sms
slack
webhook
in_app
```

---

## Feedback Receipt

Represents:

```text
feedback about feedback
```

Examples:

```text
delivery succeeded
delivery failed
delivery partially failed
```

---

# 6. Domain Models

## FeedbackIntent

Fields:

```text
id
event_key
feature_profile
title
body
priority
expires_at
metadata
```

---

## FeedbackRecipient

Fields:

```text
id
type
reference
name
```

Examples:

```text
User
Contact
ExternalEndpoint
SlackChannel
WebhookEndpoint
```

---

## FeedbackDelivery

Fields:

```text
id
intent_id
channel
recipient
status
attempt_count
max_attempts
expires_at
provider_response
```

---

## FeedbackRoute

Fields:

```text
notifiable_type
notifiable_id
channel
address
verified_at
is_primary
```

Examples:

```text
mail
sms
slack
webhook
whatsapp
viber
```

---

## NotificationPreference

Fields:

```text
event_key
channel
enabled
```

---

# 7. Supported Channels

Initial channels:

```text
mail
sms
webhook
slack
in_app
log
null
```

Future channels:

```text
whatsapp
viber
messenger
mobile_push
teams
```

---

# 8. Driver Architecture

All drivers must implement:

```php
FeedbackChannelDriver
```

Methods:

```php
send()
supports()
health()
```

---

# 9. Feature Profiles

Feature Profiles determine:

```text
branding
wording
templates
actions
```

Examples:

```text
default
dbp
dswd
lgu
private_bank
```

Feature Profiles are not languages.

They are institutional experiences.

---

# 10. Template System

Templates resolve using:

```text
feature profile
+
event key
+
channel
```

Example:

```text
claim.succeeded
    ↓
dbp
    ↓
mail
```

---

# 11. Action Support

Every notification may include actions.

Actions are provided by upstream workflow mappers.

x-feedback does not decide actions.

Example:

```text
View Claim
Approve Request
Retry Disbursement
```

Actions are rendered by channels.

---

# 12. Artifact Support

x-feedback supports artifact rendering.

Examples:

```text
selfie
signature
location
government_id
medical_certificate
```

x-feedback does not own artifact storage.

---

# 13. Artifact Rendering Policy

Per-channel rendering rules.

Examples:

```text
preview
link
hide
attach
```

Examples:

```text
email
    selfie preview

sms
    no preview

slack
    secure link
```

---

# 14. Delivery State Machine

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

# 15. Retry Management

Each channel may define:

```text
max_attempts
backoff
expiration
```

Examples:

```text
sms
mail
webhook
slack
```

---

# 16. Freshness Policies

Notifications may become stale.

Examples:

```text
OTP expired
pending payout already completed
claim already cancelled
```

Expired notifications should not be delivered.

---

# 17. Delivery Receipts

Operational notifications regarding delivery.

Examples:

```text
feedback delivered
feedback failed
feedback partially failed
```

These are distinct from business notifications.

---

# 18. Notification Routes

Recipients should not contain hardcoded channel fields.

Use:

```text
NotificationRoute
```

Examples:

```text
mail
sms
slack
webhook
whatsapp
viber
```

---

# 19. In-App Notifications

x-feedback shall support:

```text
unread
read
archived
dismissed
```

Capabilities:

```text
mark read
mark unread
bulk mark read
```

---

# 20. Delivery Console

Provide APIs and reusable components for:

```text
delivery status
attempt history
provider responses
retry actions
```

---

# 21. UI Ownership

## x-feedback Owns

Reusable UI components:

```text
NotificationBadge
NotificationBell
NotificationList
NotificationItem

DeliveryStatusBadge
DeliveryTimeline
DeliveryAttemptTable

ChannelIcon
RetryDeliveryButton
```

---

## Cockpit Owns

Pages:

```text
Notification Center
Claim Details
Campaign Details
Settlement Details
Dashboard Widgets
```

---

# 22. Operational Monitoring

Provide:

```text
channel health
queue depth
delivery failures
retry backlog
```

Examples:

```text
SMS credits depleted
Slack unavailable
Webhook endpoint down
```

---

# 23. Credential Management

Credentials belong to:

```text
tenant
institution
customer
```

Examples:

```text
DBP
LGU
DSWD
Private Bank
```

Supported credentials:

```text
SMTP
SMS Provider
Slack
Webhook Signing
WhatsApp
Viber
```

x-feedback must resolve credentials dynamically.

---

# 24. Journal Integration

Every significant feedback event may emit journal events.

Examples:

```text
feedback.created
feedback.sent
feedback.failed
feedback.expired
```

x-journal remains the system of record.

---

# 25. Non-Goals

x-feedback must not own:

```text
claim workflow
campaign orchestration
voucher lifecycle
settlement lifecycle
audit history
CTA decisions
artifact storage
```

---

# 26. Guiding Principle

x-feedback is the communication layer of the platform.

It informs.

It routes.

It tracks.

It renders.

It does not decide business workflows.

Business meaning belongs to x-change.
