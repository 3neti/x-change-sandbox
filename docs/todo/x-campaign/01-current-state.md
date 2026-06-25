# 01-current-state.md

# x-campaign Current State
## Architecture Discovery Baseline

**Package:** `3neti/x-campaign`  
**Status:** Pre-Implementation  
**Version:** 1.0

---

# Purpose of this Document

This document describes the current state of campaign-related functionality across the x-change ecosystem before the introduction of the dedicated `x-campaign` package.

It serves as the baseline from which the package will evolve.

This is intentionally a snapshot of the current architecture, including identified gaps, responsibilities, coupling, and future opportunities.

---

# Executive Summary

Today, campaign functionality does not exist as a dedicated domain.

Instead, campaign behavior is distributed across:

- x-change
- voucher
- x-feedback
- host application code
- manual operational workflows

The platform can:

- generate Pay Codes
- send notifications
- execute claims
- track redemptions

but it cannot yet:

- manage audiences
- manage beneficiary populations
- orchestrate campaigns
- track campaign attribution
- track CTA engagement
- analyze campaign performance
- understand recipient history

As a result, the platform supports execution but does not yet support beneficiary distribution intelligence.

---

# Current Ecosystem State

## x-change

Currently owns:

- Pay Code issuance
- voucher instruction contracts
- claim lifecycle
- redemption
- withdrawal
- disbursement
- settlement orchestration
- pricing
- onboarding
- wallet orchestration

x-change currently functions as the:

```text
Execution Platform
```

It knows:

- what was issued
- who claimed
- how much was disbursed

It does not know:

- why a recipient received a Pay Code
- which campaign caused issuance
- which message was delivered
- which CTA was clicked
- how recipients were selected

---

## x-feedback

Currently owns:

- notification delivery
- SMS sending
- email sending
- webhook delivery

x-feedback is transport-oriented.

It knows:

- a message was sent

It does not know:

- campaign intent
- audience membership
- recipient engagement
- attribution

---

## x-journal

Currently owns:

- audit trails
- immutable historical records

It knows:

- what happened

It does not know:

- campaign performance
- recipient engagement
- distribution effectiveness

---

# Existing Distribution Capabilities

Today, Pay Codes can be distributed through:

- SMS
- Email
- Webhook
- Direct sharing
- QR Codes
- Manual export

These capabilities are generally executed:

- manually
- through custom application logic
- through operational procedures

There is currently no unified campaign orchestration layer.

---

# Existing Audience Management

There is currently no dedicated audience domain.

The platform lacks:

```text
CampaignAudience
CampaignRecipient
CampaignImport
CampaignExecution
CampaignBatch
```

Recipient lists are typically managed outside the platform through:

- spreadsheets
- external systems
- manually prepared datasets

As a result:

- audiences are not reusable
- recipients are not tracked longitudinally
- campaign membership is not preserved

---

# Existing Campaign Management

No dedicated campaign entity exists.

The platform currently lacks:

```text
Campaign
CampaignExecution
CampaignDelivery
```

Consequently:

- campaigns cannot be scheduled
- campaigns cannot be repeated
- campaigns cannot be analyzed
- campaign history cannot be reconstructed

---

# Existing Recipient Intelligence

The platform currently has no recipient intelligence layer.

Questions such as:

```text
How many Pay Codes has this recipient received?
How much value has been distributed to this recipient?
Which campaigns included this recipient?
Which messages were sent?
Which claims were completed?
```

cannot currently be answered by a dedicated subsystem.

Historical answers require custom queries across multiple systems.

---

# Existing Template Architecture

A template architecture is emerging within x-change.

Current direction:

```text
Program Blueprint
Voucher Template
Issuance Blueprint
```

These concepts are expected to become the authoritative source for:

- voucher instructions
- validation requirements
- claim requirements
- settlement defaults
- feature profile defaults
- rider content

At present, this architecture is still evolving.

No formal integration currently exists between campaign operations and future Program Blueprints.

---

# Existing Rider Content

Current voucher architecture supports:

- rider.message
- rider.splash
- rider.url

These are attached to voucher instructions and influence the claim experience.

The platform currently lacks:

- rider analytics
- splash analytics
- CTA analytics
- recipient engagement analytics

The system knows content exists but does not know how recipients interact with it.

---

# Existing CTA Architecture

CTA architecture is currently being formalized.

The emerging direction is:

```text
Workflow ownership
    belongs to x-change

CTA tracking
    belongs to x-campaign
```

Today:

- CTA clicks are generally not tracked
- attribution is not preserved
- campaign engagement is not measured

---

# Existing Link Tracking

There is currently:

```text
No campaign link domain
```

The platform lacks:

```text
CampaignLink
CampaignClick
CampaignCta
```

Therefore:

- click-through rates cannot be measured
- attribution chains cannot be reconstructed
- recipient engagement cannot be analyzed

---

# Existing QR Code Strategy

QR codes exist as execution artifacts.

Current QR usage focuses on:

- Pay Code access
- claim initiation
- payment workflows
- settlement workflows

The platform currently lacks:

- campaign QR branding
- QR attribution
- QR engagement analytics
- QR performance reporting

---

# Existing Analytics

Analytics today are primarily operational.

Examples:

- issued vouchers
- redeemed vouchers
- disbursed amounts

Missing capabilities include:

```text
campaign analytics
delivery analytics
engagement analytics
recipient analytics
template analytics
program analytics
```

The platform currently answers:

```text
What happened?
```

but not:

```text
Why did it happen?
What caused it?
How effective was it?
```

---

# Existing Feature Profile Usage

Feature Profiles are being adopted across the ecosystem.

Examples:

```text
default
dbp
lgu
dswd
private-bank
```

Feature Profiles currently influence:

- terminology
- branding
- workflow presentation

There is currently no campaign-specific Feature Profile strategy.

---

# Existing Cockpit State

The future x-change cockpit is evolving into the primary operator interface.

Current direction:

```text
Cockpit
    =
    Operational Control Center
```

Campaign-related views do not currently exist.

Missing modules include:

- Campaign Dashboard
- Recipient Explorer
- Program Analytics
- Delivery Analytics
- CTA Analytics
- QR Analytics

---

# Current Architectural Gap

The platform possesses:

```text
Execution Intelligence
```

but lacks:

```text
Distribution Intelligence
```

Current architecture answers:

- What was issued?
- What was claimed?
- What was disbursed?

It cannot yet answer:

- Who was targeted?
- Why were they targeted?
- What was delivered?
- What was clicked?
- Which message performed best?
- Which campaign performed best?
- Which recipients are most engaged?

---

# Current Strategic Opportunity

The introduction of `x-campaign` creates a dedicated domain responsible for:

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

while allowing:

```text
x-change
    ↓
Execution
```

to remain focused on financial workflows.

This separation creates a clean boundary between:

```text
Beneficiary Distribution Platform
```

and

```text
Execution Platform
```

which is the intended long-term architecture of the ecosystem.

---

# Current State Summary

Today the ecosystem supports:

- Pay Code generation
- notification delivery
- claim execution
- redemption
- disbursement

but lacks:

- campaign orchestration
- audience management
- recipient intelligence
- engagement tracking
- attribution tracking
- campaign analytics

The platform is execution-capable but not yet campaign-aware.

The primary purpose of `x-campaign` is to introduce this missing distribution and analytics layer without duplicating the financial execution responsibilities already owned by x-change.
