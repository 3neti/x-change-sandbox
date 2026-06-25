# 04-test-strategy.md
# x-feedback Evolution Plan
## Test Strategy

### Status

This document defines the testing strategy for:

```text
3neti/x-feedback
```

The objective is to ensure that x-feedback evolves into a highly reliable communication infrastructure layer capable of supporting:

- financial workflows
- government aid programs
- beneficiary notifications
- operational alerts
- approval workflows
- future campaign distribution

without introducing hidden communication failures.

---

# Testing Philosophy

x-feedback is not merely a UI package.

It is a:

```text
communication infrastructure package
```

Therefore testing must focus on:

```text
correctness
reliability
observability
recoverability
```

rather than only message generation.

---

# Primary Testing Principle

Every communication path must be testable without requiring:

```text
real SMTP
real SMS
real Slack
real Webhook
```

All provider interactions shall be abstracted behind drivers.

---

# Testing Pyramid

## Unit Tests

Verify:

```text
DTOs
Policies
Resolvers
Mappers
Drivers
Services
```

without database access.

---

## Integration Tests

Verify:

```text
dispatcher
driver resolution
database persistence
retry scheduling
template resolution
```

with database interaction.

---

## End-to-End Tests

Verify:

```text
event
    ↓
feedback intent
    ↓
delivery
    ↓
tracking
```

through complete execution paths.

---

# Required Test Coverage

## Feedback Intent Creation

### Objective

Verify creation of Feedback Intent objects.

---

### Scenarios

```text
creates basic feedback intent

creates feedback intent with actions

creates feedback intent with artifacts

creates feedback intent with recipients

creates feedback intent with expiration
```

---

### Assertions

```text
intent fields populated correctly

metadata preserved

actions preserved

artifacts preserved
```

---

# Event Mapper Tests

### Objective

Verify translation from domain events into feedback intents.

---

### Scenarios

```text
ClaimSucceeded → FeedbackIntent

ClaimFailed → FeedbackIntent

DisbursementFailed → FeedbackIntent

ApprovalRequired → FeedbackIntent
```

---

### Assertions

```text
correct title

correct body

correct recipients

correct actions

correct event key
```

---

# Template Resolution Tests

### Objective

Verify template selection.

---

### Scenarios

```text
default profile

dbp profile

lgu profile

missing profile fallback
```

---

### Assertions

```text
correct template selected

fallback behavior works

profile overrides honored
```

---

# Feature Profile Tests

### Objective

Verify profile-specific communication.

---

### Scenarios

```text
same event

different profiles
```

Example:

```text
claim.succeeded

default

dbp

lgu
```

---

### Assertions

```text
title differs

body differs

actions differ

branding differs
```

where expected.

---

# Driver Resolution Tests

### Objective

Verify driver discovery and registration.

---

### Scenarios

```text
mail driver

webhook driver

slack driver

null driver

unknown driver
```

---

### Assertions

```text
correct driver returned

unsupported driver rejected
```

---

# Mail Driver Tests

### Objective

Verify mail delivery behavior.

---

### Scenarios

```text
successful send

provider failure

invalid recipient

expired intent
```

---

### Assertions

```text
delivery record created

status updated

provider response stored
```

---

# Webhook Driver Tests

### Objective

Verify webhook behavior.

---

### Scenarios

```text
successful webhook

500 response

timeout

invalid endpoint
```

---

### Assertions

```text
delivery status correct

retry status correct

provider response preserved
```

---

# Slack Driver Tests

### Objective

Verify Slack delivery.

---

### Scenarios

```text
successful send

channel not found

invalid token

rate limited
```

---

### Assertions

```text
delivery state updated

retry scheduled when applicable
```

---

# In-App Driver Tests

### Objective

Verify in-app notification behavior.

---

### Scenarios

```text
notification created

notification read

notification unread

notification archived
```

---

### Assertions

```text
state transitions valid

counts updated
```

---

# Notification Route Tests

### Objective

Verify route resolution.

---

### Scenarios

```text
User email route

User sms route

Contact whatsapp route

fallback route
```

---

### Assertions

```text
correct route selected

primary route preferred

disabled route ignored
```

---

# Notification Preference Tests

### Objective

Verify preference enforcement.

---

### Scenarios

```text
enabled channel

disabled channel

missing preference
```

---

### Assertions

```text
delivery permitted

delivery suppressed
```

as appropriate.

---

# Delivery State Machine Tests

### Objective

Verify all delivery transitions.

---

### Valid States

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

### Assertions

Every transition must be tested.

Example:

```text
pending → queued

queued → sending

sending → delivered

sending → failed_retryable
```

---

### Invalid Transitions

Example:

```text
delivered → queued

cancelled → sending
```

must be rejected.

---

# Retry Strategy Tests

### Objective

Verify retry behavior.

---

### Scenarios

```text
retryable failure

non-retryable failure

maximum attempts exceeded
```

---

### Assertions

```text
retry scheduled

attempt count incremented

final failure triggered
```

---

# Freshness Policy Tests

### Objective

Prevent stale communication.

---

### Scenarios

```text
expired OTP

expired approval

expired reminder
```

---

### Assertions

```text
message not sent

delivery marked expired
```

---

# Delivery Receipt Tests

### Objective

Verify operational feedback.

---

### Scenarios

```text
all delivered

partially delivered

all failed
```

---

### Assertions

```text
receipt generated

receipt routed correctly
```

---

# Artifact Rendering Tests

### Objective

Verify channel-specific artifact behavior.

---

### Scenarios

```text
selfie preview

signature preview

location preview

government id hidden
```

---

### Assertions

```text
correct rendering policy applied

restricted artifact protected
```

---

# Credential Resolver Tests

### Objective

Verify tenant credential resolution.

---

### Scenarios

```text
default credentials

DBP credentials

LGU credentials

missing credentials
```

---

### Assertions

```text
correct credential selected

fallback behavior correct
```

---

# Journal Integration Tests

### Objective

Verify journal event generation.

---

### Scenarios

```text
feedback created

feedback sent

feedback failed

feedback expired
```

---

### Assertions

```text
journal event emitted
```

without coupling to journal implementation.

---

# UI Component Tests

### Objective

Verify reusable UI components.

---

### Components

```text
NotificationBell

NotificationBadge

NotificationList

NotificationItem

DeliveryTimeline

DeliveryStatusBadge

RetryDeliveryButton
```

---

### Assertions

```text
renders correctly

props honored

events emitted
```

---

# API Tests

### Objective

Verify public package APIs.

---

### Endpoints

Examples:

```text
notifications

unread count

delivery history

mark read

retry delivery
```

---

### Assertions

```text
authorization enforced

responses correct

pagination works
```

---

# Failure Simulation Tests

### Objective

Verify resilience.

---

### Simulate

```text
SMTP unavailable

Slack unavailable

Webhook timeout

Queue failure

Database failure
```

---

### Assertions

```text
system degrades gracefully

delivery records preserved

retry scheduled where appropriate
```

---

# Performance Tests

### Objective

Verify scalability.

---

### Scenarios

```text
100 notifications

1,000 notifications

10,000 notifications
```

---

### Assertions

```text
dispatch remains queued

memory usage acceptable

database writes predictable
```

---

# Required Coverage Standard

The package shall target:

```text
100% coverage
```

for:

```text
state transitions

retry logic

freshness logic

policy enforcement

driver resolution

template resolution
```

These represent business-critical communication behavior.

---

# Pest Conventions

All tests shall follow:

```text
Arrange
Act
Assert
```

pattern.

---

## Dataset Usage

Use labeled datasets extensively.

Example:

```php
dataset('delivery states', [
    'pending',
    'queued',
    'sending',
]);
```

Labels must clearly describe the scenario being tested.

---

# Definition of Test Completeness

Testing is considered complete when:

```text
every event can produce a feedback intent

every intent can be delivered

every delivery can be tracked

every failure can be explained

every retry can be observed

every notification can be audited
```

without requiring external providers or manual inspection.

At that point x-feedback can be considered production-ready infrastructure rather than a simple notification subsystem.
