# 05-architecture-invariants.md
# x-feedback Evolution Plan
## Architecture Invariants

### Status

This document defines the non-negotiable architectural rules for:

```text id="7nl2aj"
3neti/x-feedback
```

These invariants must remain true across all implementation phases, refactors, package integrations, and future channel additions.

---

# 1. x-feedback Does Not Own Business Meaning

x-feedback must never decide what a workflow event means.

Business meaning belongs to upstream domain packages, especially:

```text id="9h3rgp"
x-change
x-campaign
settlement-envelope
voucher
```

Correct:

```text id="tpi26l"
ClaimSucceeded event
    → mapped into FeedbackIntent
```

Incorrect:

```text id="eyh1yy"
x-feedback inspects voucher status and decides a claim succeeded
```

---

# 2. Workflows Emit Events, Not Notifications

Workflow classes must not directly send specific notifications.

Correct:

```text id="moh924"
RedeemPayCode
    → emits ClaimSucceeded
```

Incorrect:

```text id="jx1eir"
RedeemPayCode
    → sends ClaimSucceededEmail
```

Workflows should remain channel-agnostic.

---

# 3. Feedback Intent Is the Boundary Object

All communication must pass through:

```text id="mrvtu2"
FeedbackIntent
```

Drivers should not receive raw workflow objects as their primary input.

Correct:

```text id="zo94qc"
Domain Event
    → FeedbackIntent
    → Driver
```

Incorrect:

```text id="mkqejp"
Voucher
    → MailDriver
```

---

# 4. Channels Are Drivers

Every delivery channel must be implemented as a driver.

Required driver boundary:

```php id="jz8112"
FeedbackChannelDriver
```

No channel-specific logic should leak into workflow services.

Channels include:

```text id="2bec6p"
mail
sms
webhook
slack
in_app
log
null
whatsapp
viber
```

---

# 5. Delivery Must Be Observable

Every attempted delivery must create a delivery record.

The system must be able to answer:

```text id="np87pa"
what was sent
to whom
through which channel
when
with what result
```

No fire-and-forget delivery may bypass delivery tracking.

---

# 6. Delivery State Is Explicit

All deliveries must follow the delivery state machine.

Canonical states:

```text id="w783hs"
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

States must not be represented only as booleans.

Avoid:

```text id="gdtj4a"
sent = true
failed = false
```

Use explicit status values.

---

# 7. Retry Logic Belongs to x-feedback

Retry behavior must be centralized in x-feedback.

Workflows must not implement notification retry loops.

Correct:

```text id="2ek56e"
FeedbackDelivery
    → RetryPolicy
    → RetryScheduled
```

Incorrect:

```text id="hhepxf"
ClaimService retries SMS manually
```

---

# 8. Freshness Must Be Checked Before Sending

A delayed notification must not be sent blindly.

Before each send or retry, x-feedback must determine:

```text id="4qudwc"
Is this still true?
Is this still useful?
Is this still allowed?
```

Stale notifications must be:

```text id="cn6s6v"
expired
cancelled
replaced
```

not delivered.

---

# 9. Delivery Receipts Are Not Business Feedback

Business feedback and operational delivery receipts are separate.

Business feedback:

```text id="bnsyim"
Pay Code ABC was claimed.
```

Delivery receipt:

```text id="cssivc"
Email sent.
SMS failed.
Webhook retry scheduled.
```

These must not be confused.

---

# 10. In-App Is a Channel, Not an Audit Log

The `in_app` channel is for user-facing notifications.

It must not be used as a mandatory logging mechanism.

Audit/history belongs to:

```text id="lqjv12"
x-journal
```

Operational delivery state belongs to:

```text id="y0ee5v"
FeedbackDelivery
```

---

# 11. x-journal Owns Historical Narrative

x-feedback may emit journal events.

x-journal must own durable business history.

Correct:

```text id="hv5l4o"
feedback.sent
    → x-journal records timeline entry
```

Incorrect:

```text id="i5b0nh"
x-feedback becomes audit history system
```

---

# 12. x-campaign Owns Mass Distribution

x-feedback must not become a campaign orchestration package.

Correct:

```text id="874e5l"
x-campaign creates distribution jobs
x-feedback delivers messages
```

Incorrect:

```text id="7wkcjq"
x-feedback imports 1,000,000 beneficiaries
```

---

# 13. CTA Ownership Is Upstream

x-feedback must render actions but must not decide workflow actions.

Correct:

```text id="0krpxu"
x-change mapper supplies View Claim action
x-feedback renders it
```

Incorrect:

```text id="teq5j7"
x-feedback decides that DisbursementFailed means Retry Disbursement
```

Actions belong to workflow semantics.

---

# 14. Feature Profiles Affect Presentation, Not Domain Logic

Feature Profiles may affect:

```text id="bvhbzr"
wording
branding
template
channel formatting
action labels
```

They must not change:

```text id="m0p6mi"
whether a claim succeeded
whether payout happened
whether a voucher is valid
```

Domain behavior remains upstream.

---

# 15. Artifact Meaning Is Upstream

x-feedback must not define what an artifact means.

Artifact meaning belongs to workflow/domain packages.

Examples:

```text id="4x2m0k"
selfie
signature
location
government_id
LOA
medical_certificate
```

x-feedback may render or hide artifacts, but may not assign business meaning.

---

# 16. Artifact Storage Is Not x-feedback Responsibility

x-feedback must not become artifact storage.

Storage responsibilities belong to:

```text id="8pcf3h"
storage/media layer
artifact package
host app storage
```

x-feedback may consume:

```text id="5impn4"
artifact references
preview URLs
signed URLs
metadata
```

but not own artifact lifecycle.

---

# 17. Artifact Attachments Are Disabled by Default

Email attachments and direct artifact distribution must not be enabled by default.

Default behavior:

```text id="6eabjy"
preview
secure link
hide
```

not:

```text id="607493"
attach original file
```

This protects sensitive evidence and avoids duplicating artifacts outside the controlled storage layer.

---

# 18. Notification Routes Are Separate From Entities

Do not add permanent channel columns directly to every notifiable model.

Avoid:

```text id="crv23h"
users.slack_id
users.whatsapp_id
contacts.webhook_url
```

Use:

```text id="v1godj"
NotificationRoute
```

instead.

This enables new channels without schema churn.

---

# 19. Voucher Is Not Generally Notifiable

A voucher may contain feedback instructions.

But a voucher should not generally behave as a notifiable person or endpoint.

Correct:

```text id="858abc"
voucher.instructions.feedback
```

Incorrect:

```text id="h4bfl8"
voucher->notify()
```

Notifiable entities are usually:

```text id="1x0eu3"
User
Contact
Issuer
Operator
CampaignRecipient
ExternalEndpoint
```

---

# 20. Credentials Are Resolved, Not Hardcoded

Channel credentials must be resolved through a credential resolver.

Correct:

```text id="tr8ser"
tenant DBP
    → DBP SMTP
    → DBP Slack
    → DBP SMS
```

Incorrect:

```text id="coo1o3"
global SMTP used for every institution
```

Tenant/institution credentials must be supported.

---

# 21. Provider Failures Must Not Break Workflows

Notification delivery failure must not automatically fail the business workflow unless explicitly configured.

Correct:

```text id="y9bzxg"
claim succeeds
notification fails
delivery marked failed_retryable
operator alerted
```

Incorrect:

```text id="ymsxhh"
claim rolls back because SMS provider is down
```

Communication is usually consequential, not transactional with the workflow.

---

# 22. Channel Health Must Be Observable

Each channel should expose health information.

Examples:

```text id="543ejx"
SMTP unavailable
SMS credits depleted
Slack rate limited
Webhook endpoint down
```

Health data should influence retry, pause, and operator alerts.

---

# 23. UI Components Are Package-Owned; Pages Are Host-Owned

x-feedback owns reusable components.

Examples:

```text id="x4bon0"
NotificationBell
NotificationBadge
DeliveryTimeline
DeliveryStatusBadge
```

x-change/Cockpit owns:

```text id="1n9pzk"
pages
routes
layouts
workflow composition
```

The package must not hardcode Cockpit pages.

---

# 24. Headless UI Is Preferred

Reusable components should be as layout-independent as practical.

They should expose:

```text id="b59gxj"
props
events
slots
```

rather than assuming a specific host application layout.

---

# 25. No Provider-Specific Coupling in Core Services

Core services must not depend directly on provider SDKs.

Provider integrations belong in:

```text id="2wydl3"
drivers
adapters
provider packages
```

Core x-feedback logic should remain provider-neutral.

---

# 26. Webhooks Are a Channel, Not a Workflow

Webhook sending is a delivery channel.

Webhook receiving belongs to the host app or appropriate integration package.

Outgoing webhooks may use a dedicated webhook driver.

---

# 27. Delivery Attempts Must Be Idempotent

A retry must not create duplicate uncontrolled sends when preventable.

Each delivery should have:

```text id="o86921"
idempotency key
payload hash
attempt history
```

Drivers should use provider idempotency where available.

---

# 28. Delivery Records Must Preserve Provider Responses

Provider responses should be stored in a structured way.

Examples:

```text id="rpet9n"
message_id
status_code
error_code
error_message
raw_response
```

This supports debugging, reconciliation, and operator visibility.

---

# 29. Sensitive Data Must Be Minimized

Feedback payloads should avoid copying sensitive raw data.

Prefer:

```text id="21r920"
references
summaries
metadata
signed preview URLs
```

over:

```text id="6v1huh"
raw selfie base64
raw ID image
full KYC payload
```

---

# 30. Package Must Remain Incrementally Adoptable

x-feedback must be adoptable in slices.

The package must not require immediate replacement of all legacy notifications.

Migration should support:

```text id="0fqgvw"
legacy notification
    → feedback intent adapter
```

before full rewrite.

---

# 31. Tests Must Protect These Invariants

Any implementation that violates these invariants should fail tests.

Critical test targets include:

```text id="oet27p"
driver boundaries
delivery state machine
retry rules
freshness rules
route resolution
preference enforcement
artifact rendering
credential resolution
```

---

# Final Rule

x-feedback is the communication infrastructure layer.

It must:

```text id="ysiam5"
inform
route
render
track
retry
observe
```

It must not:

```text id="ymvsch"
decide workflows
own artifacts
own campaigns
own audit history
own business meaning
```
