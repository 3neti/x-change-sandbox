# 01-current-state.md
# x-feedback Evolution Plan
## Current State Assessment

### Status

The x-feedback package does not currently exist as a dedicated package.

Notification and feedback responsibilities are presently scattered across multiple areas of the x-change ecosystem.

The existing implementation originated from the earlier redeem-x architecture and was subsequently inherited by x-change.

As a result:

```text
feedback exists
but feedback is not yet a first-class architectural capability
```

---

# Existing Architecture

## Notification Triggering

Notifications are currently triggered directly from workflow execution paths.

Typical pattern:

```text
Workflow
    ↓
Pipeline
    ↓
Notification Class
    ↓
Specific Channel
```

Examples include:

```text
claim completed
voucher redeemed
disbursement processed
```

where notification delivery is executed as a side effect of workflow completion.

This creates coupling between:

```text
business workflows
notification generation
notification delivery
```

---

## Redeem-X Legacy Pattern

The previous redeem-x implementation introduced:

```text
SendFeedbacks
```

as a post-redemption pipeline stage.

Conceptually:

```text
Redeem Voucher
    ↓
Disburse Cash
    ↓
Send Feedbacks
```

The feedback pipeline inspected voucher instructions:

```php
'instructions' => [
    'feedback' => [
        'email',
        'mobile',
        'webhook',
    ],
]
```

and attempted delivery.

---

## Channel Support

The existing architecture supports:

```text
email
sms
webhook
```

through application-specific implementations.

There is no generalized channel abstraction.

There is no pluggable driver model.

There is no centralized channel registry.

---

## Delivery Tracking

Current delivery visibility is minimal.

The system generally knows:

```text
notification attempted
```

but does not consistently track:

```text
queued
sent
delivered
failed
retry scheduled
expired
```

as first-class delivery states.

There is no unified delivery model.

---

## Retry Management

Retry behavior is currently delegated to:

```text
queue worker behavior
provider behavior
application-specific implementations
```

There is no centralized retry strategy.

There is no channel-specific retry policy.

There is no freshness policy.

The system cannot currently determine:

```text
should this notification still be delivered?
```

after significant delays.

---

## Feedback Visibility

The platform lacks a centralized feedback console.

Operators cannot consistently answer:

```text
Was the notification sent?

To whom?

Through which channel?

Did it succeed?

Did it fail?

Will it retry?
```

without inspecting logs or provider dashboards.

---

# Notification Ownership

Notification logic is currently mixed into:

```text
workflow services
pipeline handlers
notification classes
```

This results in:

```text
business logic knowing about channels
business logic knowing about delivery
business logic knowing about templates
```

instead of delegating those concerns to a dedicated package.

---

# Template Management

Templates currently exist in multiple forms:

```text
Laravel Notifications
Mailables
SMS templates
Webhook payload builders
```

There is no unified feedback template system.

There is no standardized event-to-template mapping.

There is no Feature Profile resolution layer.

---

# Feature Profiles

Feature Profiles have been adopted elsewhere in the platform as a strategic direction.

However:

```text
notification content
notification branding
notification actions
notification wording
```

are not yet Feature Profile aware.

There is currently no mechanism to support:

```text
DBP wording
LGU wording
DSWD wording
Private Bank wording
```

for the same underlying event.

---

# CTA Support

Current notifications are primarily informational.

There is no unified Action model.

There is no standardized support for:

```text
View Claim
Approve Request
Retry Disbursement
Open Dashboard
Upload Document
```

across channels.

CTA ownership has not yet been formally defined.

---

# In-App Notifications

The platform does not currently provide a unified in-app notification capability.

Missing capabilities include:

```text
notification inbox
unread count
notification bell
notification center
mark read
mark unread
```

These concepts exist only in fragmented or ad hoc forms.

---

# Delivery Receipts

The current system focuses on sending notifications.

It does not consistently provide:

```text
feedback delivery receipts
delivery confirmations
delivery failure summaries
operator visibility
```

for the parties who initiated the workflow.

As a result, operators may not know whether feedback actually reached its intended recipients.

---

# Artifact Rendering

Artifact handling currently originates from workflow implementations.

Examples:

```text
selfie
signature
location map
government ID
medical certificate
LOA
```

The old redeem-x implementation rendered selected artifacts into email notifications.

However:

```text
artifact rendering
artifact sensitivity
artifact visibility
artifact previews
```

are not currently governed by a dedicated architecture.

There is no artifact rendering policy.

There is no channel-aware artifact presentation model.

---

# Recipient Modeling

Recipients are currently represented through direct addresses.

Examples:

```text
email address
mobile number
webhook URL
```

The platform lacks a generalized concept of:

```text
Notification Route
Notification Preference
Notifiable Entity
```

that would allow:

```text
User
Contact
Issuer
Campaign Recipient
```

to expose multiple communication endpoints.

---

# Credential Management

Provider credentials are currently resolved through application configuration.

Examples:

```text
SMTP
SMS provider
Webhook configuration
```

There is no tenant-aware credential model.

The platform does not yet support:

```text
DBP SMTP
LGU SMTP
Bank-specific SMS provider
Institution-specific Slack workspace
```

as first-class capabilities.

---

# UI Ownership

Notification UI responsibilities are currently undefined.

There is no formal distinction between:

```text
package-owned reusable components
```

and

```text
application-owned pages and layouts
```

This boundary must be established before implementation begins.

---

# Journal Integration

Notification history is not formally integrated with x-journal.

The platform cannot yet consistently answer:

```text
What notification was sent?

When?

To whom?

Through which channel?

What was the outcome?
```

through a unified audit experience.

---

# Current Architectural Assessment

The current notification implementation is functional but fragmented.

Strengths:

```text
basic email support
basic SMS support
basic webhook support
existing redeem-x experience
```

Weaknesses:

```text
no package boundary
no channel abstraction
no delivery model
no retry model
no freshness model
no in-app notifications
no Feature Profile integration
no reusable UI components
no notification routing model
no delivery receipts
```

The system presently behaves as:

```text
workflow-driven notifications
```

rather than:

```text
event-driven communication infrastructure
```

which is the target direction of the x-feedback package.
