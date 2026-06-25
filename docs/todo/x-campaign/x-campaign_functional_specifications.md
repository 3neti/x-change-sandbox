# x-campaign Functional Specifications
## Beneficiary Distribution Platform

**Package:** `3neti/x-campaign`  
**Status:** Draft Functional Specification  
**Version:** 1.0

---

# 1. Executive Summary

## Purpose

`x-campaign` is a Beneficiary Distribution Platform responsible for:

- audience management
- beneficiary management
- Pay Code distribution
- delivery orchestration
- engagement tracking
- attribution tracking
- campaign analytics
- recipient intelligence

The package is designed to support:

- government aid
- educational assistance
- subsidy programs
- payroll distribution
- disaster relief
- financial assistance
- incentives
- scholarship programs

and similar large-scale beneficiary programs.

---

## Strategic Positioning

### x-campaign is NOT

- a voucher package
- a claim package
- a redemption package
- a disbursement package
- a notification package

---

### x-campaign IS

A:

```text
Beneficiary Distribution Platform
```

that manages:

```text
Audience
    ↓
Distribution
    ↓
Engagement
    ↓
Attribution
    ↓
Analytics
```

---

# 2. Ecosystem Position

## x-change

Owns:

- Program Blueprints
- Voucher Templates
- Pay Code generation
- Pricing
- Claims
- Redemption
- Withdrawal
- Settlement
- Disbursement

---

## x-feedback

Owns:

- transactional notification infrastructure
- delivery adapters
- communication transport

---

## x-journal

Owns:

- audit trail
- immutable history
- compliance records

---

## x-campaign

Owns:

- campaign orchestration
- audience management
- recipient management
- distribution execution
- delivery visibility
- engagement tracking
- attribution tracking
- analytics

---

# 3. Guiding Principle

The package should be capable of handling:

```text
1 recipient
10 recipients
1,000 recipients
1,000,000 recipients
```

using the same architecture.

There should be no separate architecture for:

- individual beneficiary campaigns
- mass campaigns

Both should use the same model.

---

# 4. Core Lifecycle

## Campaign Lifecycle

```text
Create Campaign
    ↓
Import Audience
    ↓
Generate Pay Codes
    ↓
Distribute
    ↓
Track Delivery
    ↓
Track Engagement
    ↓
Track Claims
    ↓
Analytics
```

---

# 5. Domain Model

## Campaign

Represents:

```text
A distribution initiative
```

Examples:

- DBP Educational Assistance 2027
- LGU Emergency Relief Program
- Payroll Batch 2027-01
- Scholarship Release Q1

### Fields

- id
- name
- description
- feature_profile
- owner
- issuer
- status
- scheduled_at
- metadata

---

## CampaignAudience

Represents:

```text
A collection of recipients
```

Examples:

- All Scholars
- Region IV-A Beneficiaries
- Payroll Group A

---

## CampaignRecipient

Represents:

```text
A single beneficiary
```

### Fields

- name
- mobile
- email
- address
- external_reference
- metadata

---

## CampaignExecution

Represents:

```text
A campaign run
```

Example:

```text
Educational Assistance
Run #1
```

---

## CampaignBatch

Represents:

```text
Execution partition
```

Used for:

- queue partitioning
- large campaign scaling
- parallel execution

---

## CampaignDelivery

Represents:

```text
One delivery attempt
```

---

## CampaignImport

Represents:

```text
Audience ingestion event
```

---

# 6. Program Blueprint Integration

## Source of Truth

Campaigns must NOT create their own voucher templates.

The authoritative source remains:

```text
x-change Program Blueprint
```

or

```text
Voucher Template
```

---

## Campaign Usage

Campaigns consume:

```text
Program Blueprint
```

and distribute outputs derived from it.

Campaigns must never duplicate:

- voucher instructions
- validation requirements
- claim requirements
- settlement configuration

---

# 7. Audience Management

Supported audience sources:

## CSV

Upload recipients.

---

## Excel

Upload recipients.

---

## API

Import from external systems.

---

## Database Query

Dynamic audience generation.

---

## Manual Entry

Small campaigns.

---

# 8. Pay Code Generation

## Ownership

Pay Code generation belongs exclusively to:

```text
x-change
```

---

## Campaign Flow

```text
Recipient
    ↓
Generate Pay Code via x-change
    ↓
Store reference
```

Campaigns never generate vouchers directly.

---

# 9. Distribution Architecture

## Flow

```text
Campaign
    ↓
Execution
    ↓
Recipient
    ↓
Channel
    ↓
Provider
```

---

# 10. Channel System

Initial channels:

```text
sms
email
webhook
csv_export
print_mailer
```

Future:

```text
whatsapp
viber
messenger
mobile_push
```

---

## Driver Contract

```php
CampaignChannelDriver
```

```php
send(
    CampaignRecipientData $recipient,
    CampaignMessageData $message
): CampaignDeliveryResultData
```

---

# 11. CTA Architecture

Campaigns do not own CTA meaning.

CTA ownership remains with:

```text
x-change
```

Campaigns own:

```text
distribution
tracking
attribution
analytics
```

---

## Flow

```text
x-change CTA
    ↓
campaign tracking wrapper
    ↓
recipient click
    ↓
redirect
```

---

# 12. Campaign Links

## Purpose

Track:

- clicks
- scans
- attribution

---

## Domain Objects

### CampaignLink

Tracked destination.

### CampaignClick

Recorded engagement.

### CampaignCta

Campaign action reference.

---

## Example

```text
https://host.com/c/aB92x
```

redirects to:

```text
claim/start
payment
settlement
disburse
```

while tracking engagement.

---

# 13. QR Campaign Assets

Campaigns may generate:

```text
Branded QR Assets
```

Campaigns do NOT define QR payloads.

Payload authority remains:

```text
x-change
```

---

## Campaign QR Responsibilities

- branding
- rendering
- artwork
- printable assets
- analytics

---

# 14. Delivery Tracking

Track:

```text
queued
sent
delivered
failed
opened
clicked
```

when supported.

---

# 15. Engagement Tracking

Track:

## Clicks

CTA engagement.

---

## QR Scans

QR engagement.

---

## Landing Visits

Campaign page visits.

---

## Claim Starts

Forwarded from x-change.

---

## Claim Completion

Forwarded from x-change.

---

# 16. Claim Tracking

Campaigns consume claim status from x-change.

Track:

```text
generated
delivered
claimed
expired
cancelled
```

Campaigns do not determine these states.

---

# 17. Recipient Intelligence

The package must support:

```text
Recipient Profile
```

---

## Questions It Must Answer

### Distribution

How many Pay Codes were sent?

### Value

How much value was sent?

### Engagement

What was clicked?

### Delivery

What was delivered?

### Claims

What was claimed?

### History

What campaigns involved this recipient?

---

# 18. Message Snapshot Strategy

Every delivery must preserve:

```text
rendered content
```

not merely template references.

Store:

- rendered_sms
- rendered_email
- rendered_message
- rendered_splash
- rendered_cta
- rendered_qr_asset

This ensures historical accuracy.

---

# 19. Feature Profiles

Feature Profiles are first-class.

Examples:

```text
default
dbp
lgu
dswd
private-bank
```

---

## Purpose

Institution-specific behavior.

Not language localization.

---

# 20. Analytics

## Campaign Metrics

- recipient_count
- generated_count
- sent_count
- delivered_count
- failed_count
- claimed_count
- claim_rate

---

## Engagement Metrics

- click_rate
- scan_rate
- open_rate

---

## Program Metrics

- total distributed
- total claimed
- total expired

---

# 21. Program Analytics

Campaigns should aggregate analytics by:

```text
Program Blueprint
```

Examples:

```text
Educational Assistance
Scholarship
Payroll
Disaster Relief
```

---

# 22. Template Analytics

Although templates belong to x-change:

Campaigns should measure:

- template performance
- splash performance
- message performance
- CTA performance

Questions:

```text
Which rider.message generated the highest claim rate?
Which splash generated the highest engagement?
Which CTA generated the highest conversion?
```

---

# 23. Cockpit Integration

Campaign UI should be vendor-installable.

---

## Ownership

x-campaign owns:

- campaign pages
- campaign dashboards
- recipient pages
- analytics pages

---

## x-change Cockpit

Acts as:

```text
shell
container
navigation host
```

for campaign modules.

---

# 24. UI Modules

## Campaign Dashboard

Overview metrics.

---

## Campaign Detail

Campaign lifecycle.

---

## Recipient Explorer

Recipient-centric analytics.

---

## Program Analytics

Blueprint-level analytics.

---

## Delivery Analytics

Channel performance.

---

## CTA Analytics

Click performance.

---

## QR Analytics

Scan performance.

---

# 25. Scaling Strategy

Execution modes:

- immediate
- scheduled
- batch
- queued

Default:

```text
queued
```

Large campaigns must never execute synchronously.

---

# 26. Deferred Scope

Not part of first release:

- WhatsApp integration
- Viber integration
- AI content generation
- journey automation
- advanced segmentation
- marketing funnels
- predictive analytics

Only extension points should be scaffolded.

---

# 27. Long-Term Vision

```text
Program Blueprint (x-change)
        ↓
Campaign
        ↓
Audience
        ↓
Recipient
        ↓
Pay Code
        ↓
Distribution
        ↓
Engagement
        ↓
Claim
        ↓
Analytics
```

---

# 28. One-Sentence Summary

`x-campaign` is a Beneficiary Distribution Platform that manages audiences, recipients, distribution, engagement, attribution, and analytics while consuming Program Blueprints and claim lifecycle information from x-change without duplicating financial execution responsibilities.
