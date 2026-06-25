# 02-target-state.md

# x-campaign Target State
## Beneficiary Distribution Platform

**Package:** `3neti/x-campaign`  
**Status:** Target Architecture  
**Version:** 1.0

---

# Purpose of this Document

This document defines the desired end-state architecture for `x-campaign`.

It describes the long-term vision, responsibilities, boundaries, operating model, integrations, analytics strategy, cockpit integration, and scalability objectives.

This document is intentionally aspirational.

It should guide architectural decisions even when implementation occurs incrementally.

---

# Executive Summary

The target state of `x-campaign` is:

```text
Beneficiary Distribution Platform
```

responsible for:

```text
Audience
    ↓
Recipient
    ↓
Distribution
    ↓
Engagement
    ↓
Attribution
    ↓
Analytics
```

while:

```text
x-change
```

remains the:

```text
Execution Platform
```

responsible for:

```text
Program Blueprint
    ↓
Pay Code
    ↓
Claim
    ↓
Redemption
    ↓
Disbursement
    ↓
Settlement
```

The two systems are complementary and intentionally separated.

---

# Strategic Vision

The platform should eventually answer questions such as:

## Audience Intelligence

```text
Who was targeted?
Why were they targeted?
Which audience produced the best outcomes?
```

---

## Recipient Intelligence

```text
How much assistance has this recipient received?
How many campaigns included them?
How often do they claim?
Which messages engage them most?
```

---

## Distribution Intelligence

```text
What was delivered?
What failed?
Which channels perform best?
```

---

## Engagement Intelligence

```text
What was opened?
What was clicked?
What was scanned?
What was viewed?
```

---

## Program Intelligence

```text
Which programs perform best?
Which templates convert best?
Which campaigns achieve the highest claim rates?
```

---

# Target Position in Ecosystem

## x-change

Owns:

- Program Blueprints
- Voucher Templates
- Pay Code generation
- Claims
- Redemptions
- Withdrawals
- Disbursements
- Settlements

---

## x-feedback

Owns:

- communication transport
- delivery providers
- notification infrastructure

---

## x-journal

Owns:

- audit trails
- compliance history
- immutable records

---

## x-campaign

Owns:

- audiences
- recipients
- campaign execution
- distribution orchestration
- engagement tracking
- attribution
- analytics

---

# Core Operating Model

## Distribution Lifecycle

```text
Program Blueprint
        ↓
Campaign
        ↓
Audience
        ↓
Recipients
        ↓
Pay Code Generation
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

# Campaign Domain

## Campaign

Represents:

```text
A distribution initiative
```

Examples:

- Educational Assistance 2027
- Payroll Batch January
- Scholarship Release
- Disaster Relief Campaign

Campaigns become the primary operational unit for beneficiary distribution.

---

## Campaign Execution

A campaign may execute:

- immediately
- scheduled
- queued
- batched

Large campaigns should automatically partition into batches.

---

## Campaign Batches

The system should support:

```text
1 recipient
10 recipients
1,000 recipients
1,000,000 recipients
```

without architectural changes.

Batches exist solely for execution scalability.

---

# Audience Platform

## Audience Sources

The system should support:

### CSV

Bulk imports.

### Excel

Spreadsheet imports.

### API

External systems.

### Database Queries

Dynamic audiences.

### Manual Entry

Small campaigns.

---

## Audience Reuse

Audiences should be reusable.

Examples:

```text
All Scholars
All Employees
Region IV-A Beneficiaries
Disaster Relief Recipients
```

A single audience may participate in multiple campaigns.

---

# Recipient Platform

The target architecture introduces:

```text
Recipient Intelligence
```

---

## Recipient Profile

Every recipient should have a longitudinal profile.

Examples:

```text
Name
Mobile
Email
Address
External Reference
```

---

## Recipient Timeline

The system should answer:

```text
What was sent?
When was it sent?
What was claimed?
What was clicked?
What was viewed?
```

---

## Recipient Value History

The system should answer:

```text
How much value has been distributed?
How much has been claimed?
How much expired?
```

across all campaigns.

---

# Program Blueprint Integration

## Single Source of Truth

Program definitions remain in:

```text
x-change
```

The target state explicitly avoids creating a competing template architecture.

---

## Program Blueprint Ownership

Examples:

```text
Educational Assistance
Scholarship
Payroll
Medical Assistance
```

remain owned by x-change.

---

## Campaign Usage

Campaigns consume Program Blueprints and generate distribution analytics around them.

---

# Distribution Engine

## Campaign Distribution Flow

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

## Supported Channels

Initial:

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

## Driver-Based Architecture

All channels should be provider-agnostic.

Drivers become replaceable infrastructure.

---

# CTA Platform

## Ownership

Workflow ownership remains:

```text
x-change
```

CTA tracking belongs to:

```text
x-campaign
```

---

## CTA Flow

```text
Recipient
    ↓
Campaign Link
    ↓
Click
    ↓
Redirect
    ↓
Claim Journey
```

---

# Campaign Link Platform

Target domain:

```text
CampaignLink
CampaignClick
CampaignCta
```

---

## Purpose

Provide:

- attribution
- click tracking
- recipient tracking
- campaign tracking

---

## Host-Owned URLs

The platform should support:

```text
https://host.com/c/ABC123
```

using the same domain as the host application.

---

# QR Asset Platform

Campaigns should support:

```text
Branded QR Assets
```

while x-change remains the source of QR payloads.

---

## QR Capabilities

- QR rendering
- branding
- campaign artwork
- printable QR assets
- QR analytics

---

# Delivery Tracking

Track:

```text
queued
sent
delivered
failed
opened
clicked
```

when supported by the provider.

---

# Engagement Platform

Track:

## Link Clicks

---

## QR Scans

---

## Splash Views

---

## Rider Message Views

---

## CTA Interactions

---

## Landing Page Visits

---

## Claim Starts

Consumed from x-change.

---

## Claim Completions

Consumed from x-change.

---

# Rider Analytics

The platform should become the system of record for rider engagement.

Examples:

```text
rider.message
rider.splash
rider.url
```

---

## Questions

```text
Which rider.message performs best?
Which splash performs best?
Which CTA performs best?
```

---

# Snapshot Strategy

Every delivery should preserve:

```text
Rendered Snapshot
```

including:

- rendered_message
- rendered_splash
- rendered_url
- rendered_qr_asset

This guarantees historical accuracy.

---

# Analytics Platform

The target state includes multiple analytics layers.

---

## Campaign Analytics

Examples:

- recipients
- generated
- sent
- delivered
- claimed
- claim rate

---

## Delivery Analytics

Examples:

- SMS performance
- Email performance
- Webhook performance

---

## Engagement Analytics

Examples:

- click rate
- scan rate
- view rate

---

## Recipient Analytics

Examples:

- total sent
- total claimed
- total value
- engagement score

---

## Program Analytics

Examples:

```text
Educational Assistance
Scholarship
Payroll
```

aggregated across campaigns.

---

## Template Analytics

Even though templates belong to x-change, x-campaign should measure:

- template effectiveness
- message effectiveness
- splash effectiveness
- CTA effectiveness

---

# Cockpit Integration

The future x-change cockpit becomes the primary operator interface.

---

## Campaign UI Ownership

Owned by:

```text
x-campaign
```

---

## Cockpit Shell Ownership

Owned by:

```text
x-change
```

---

## Installation Model

```bash
php artisan x-campaign:install
php artisan x-change:install --force
```

or equivalent package-aware installation flow.

---

# Campaign UI Modules

Target modules:

## Campaign Dashboard

---

## Campaign Explorer

---

## Recipient Explorer

---

## Program Analytics

---

## Delivery Analytics

---

## CTA Analytics

---

## QR Analytics

---

## Rider Analytics

---

# Scalability Objectives

The platform should support:

```text
small campaigns
large campaigns
national campaigns
institution-wide campaigns
```

without architectural changes.

All large executions should be:

```text
queue-driven
```

and horizontally scalable.

---

# Long-Term Ecosystem Vision

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

This creates a complete beneficiary lifecycle while preserving clean package boundaries.

---

# Success Criteria

The target architecture is successful when the platform can answer:

```text
Who received assistance?
How much assistance was provided?
What was delivered?
What was viewed?
What was clicked?
What was claimed?
Which campaigns worked best?
Which messages worked best?
Which recipients are most engaged?
```

without duplicating the financial execution responsibilities owned by x-change.

---

# Target State Summary

The desired future state is a dedicated Beneficiary Distribution Platform that provides:

- audience management
- recipient intelligence
- campaign orchestration
- engagement tracking
- attribution tracking
- analytics

while consuming Program Blueprints, Pay Codes, and claim lifecycle information from x-change.

This separation enables the ecosystem to evolve independently in distribution and execution while maintaining a coherent operator experience through the x-change cockpit.
