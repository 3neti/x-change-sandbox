# 03-evolution-plan.md

# x-campaign Evolution Plan
## Incremental Path to a Beneficiary Distribution Platform

**Package:** `3neti/x-campaign`  
**Status:** Evolution Roadmap  
**Version:** 1.0

---

# Purpose of this Document

This document describes the recommended implementation sequence for `x-campaign`.

The goal is to provide a clear roadmap that:

- minimizes architectural risk
- avoids premature complexity
- establishes clean package boundaries
- delivers incremental value
- supports future scalability

This roadmap intentionally favors:

```text
Architecture First
Implementation Second
Optimization Later
```

---

# Guiding Principles

## Principle 1

Do not duplicate x-change.

x-campaign consumes:

- Program Blueprints
- Voucher Templates
- Pay Codes
- Claims
- Claim Status

from x-change.

It does not replace them.

---

## Principle 2

Do not build a marketing automation platform.

The initial objective is:

```text
Beneficiary Distribution
```

not:

```text
Marketing Automation
```

---

## Principle 3

Campaign Intelligence precedes AI.

The platform must first know:

- who was targeted
- what was delivered
- what was clicked
- what was claimed

before introducing AI-assisted features.

---

## Principle 4

Analytics must be designed from Day 1.

Even if dashboards are incomplete, the platform must begin collecting analytics immediately.

Historical data is difficult to reconstruct later.

---

# Evolution Overview

Target evolution:

```text
Phase 0
Architecture Foundation

Phase 1
Campaign Core

Phase 2
Distribution Engine

Phase 3
Attribution Layer

Phase 4
Recipient Intelligence

Phase 5
Analytics Platform

Phase 6
Cockpit Integration

Phase 7
Advanced Distribution

Phase 8
AI Copilot
```

---

# Phase 0
# Architecture Foundation

## Objective

Establish package shape and boundaries.

---

## Deliverables

### Documentation

Create:

```text
docs/
    x-campaign-architecture.md
    x-campaign-domain-model.md
    x-campaign-test-strategy.md
```

---

### Package Structure

Scaffold:

```text
src/
    Actions/
    Contracts/
    Data/
    Drivers/
    Models/
    Services/
```

---

### Domain Models

Scaffold:

```text
Campaign
CampaignAudience
CampaignRecipient
CampaignExecution
CampaignBatch
CampaignDelivery
CampaignImport
```

---

### Contracts

Scaffold:

```text
CampaignChannelDriver
PayCodeGenerationGateway
CampaignClaimStatusProvider
CampaignFeatureProfileResolver
```

---

## Success Criteria

Architecture boundaries are documented.

No business logic required.

---

# Phase 1
# Campaign Core

## Objective

Introduce campaign orchestration.

---

## Deliverables

### Campaign Management

Support:

- create campaign
- update campaign
- archive campaign
- schedule campaign

---

### Audience Management

Support:

- CSV import
- manual entry
- recipient management

---

### Recipient Management

Support:

- add recipient
- remove recipient
- audience assignment

---

### Campaign Execution

Support:

```text
Campaign
    ↓
Audience
    ↓
Execution
```

---

## Out of Scope

- analytics
- attribution
- CTA tracking

---

## Success Criteria

A campaign can target recipients and create execution records.

---

# Phase 2
# Distribution Engine

## Objective

Introduce channel orchestration.

---

## Deliverables

### Driver Architecture

Implement:

```php
CampaignChannelDriver
```

---

### Initial Drivers

```text
sms
email
webhook
csv_export
print_mailer
```

---

### Queue Support

All campaign delivery must execute through queues.

---

### Delivery Tracking

Track:

```text
queued
sent
delivered
failed
```

---

## Success Criteria

Campaigns can distribute content through pluggable channels.

---

# Phase 3
# Attribution Layer

## Objective

Introduce campaign attribution.

---

## Deliverables

### Campaign Links

Create:

```text
CampaignLink
CampaignClick
CampaignCta
```

---

### Link Tracking

Support:

- click tracking
- attribution tracking
- recipient attribution

---

### Host Domain Support

Support:

```text
https://host.com/c/{code}
```

instead of requiring third-party domains.

---

### QR Attribution

Support:

- QR scan tracking
- campaign attribution
- recipient attribution

---

## Success Criteria

The system can identify which campaign generated engagement.

---

# Phase 4
# Recipient Intelligence

## Objective

Introduce longitudinal recipient history.

---

## Deliverables

### Recipient Profile

Create:

```text
Recipient Profile
```

---

### Recipient Timeline

Track:

- deliveries
- clicks
- scans
- claims
- campaign participation

---

### Recipient Value History

Track:

- total distributed
- total claimed
- total expired

---

### Snapshot Preservation

Store:

```text
rendered_message
rendered_splash
rendered_url
rendered_qr_asset
```

for historical analysis.

---

## Success Criteria

The platform can answer:

```text
What has this recipient received?
```

---

# Phase 5
# Analytics Platform

## Objective

Transform collected events into intelligence.

---

## Deliverables

### Campaign Analytics

Support:

- recipients
- generated
- sent
- delivered
- claimed
- claim rate

---

### Engagement Analytics

Support:

- click rate
- scan rate
- view rate

---

### Delivery Analytics

Support:

- SMS performance
- Email performance
- Channel performance

---

### Program Analytics

Aggregate by:

```text
Program Blueprint
```

from x-change.

---

### Rider Analytics

Measure:

```text
rider.message
rider.splash
rider.url
```

effectiveness.

---

## Success Criteria

Campaign performance becomes measurable.

---

# Phase 6
# Cockpit Integration

## Objective

Expose campaign intelligence inside the x-change cockpit.

---

## Deliverables

### Campaign Dashboard

Overview metrics.

---

### Campaign Explorer

Campaign management.

---

### Recipient Explorer

Recipient history.

---

### Program Analytics

Program-level reporting.

---

### CTA Analytics

Click-through analysis.

---

### QR Analytics

Scan analysis.

---

### Rider Analytics

Message performance.

---

## Architecture

Ownership remains:

```text
x-campaign
    owns UI modules

x-change
    owns cockpit shell
```

---

## Success Criteria

Campaign operations become first-class cockpit functionality.

---

# Phase 7
# Advanced Distribution

## Objective

Expand distribution capabilities.

---

## Deliverables

### Additional Channels

Potential support:

```text
WhatsApp
Viber
Messenger
Push Notifications
```

---

### Advanced Audience Sources

Support:

- API synchronization
- dynamic audiences
- external beneficiary systems

---

### Campaign Scheduling

Support:

- recurring campaigns
- staged releases
- throttled execution

---

## Success Criteria

The platform supports enterprise-scale distribution operations.

---

# Phase 8
# AI Copilot

## Objective

Introduce intelligence and assistance.

---

## Deliverables

### Search

Natural language search:

```text
Show all campaigns involving Juan.
```

---

### Recipient Insights

Examples:

```text
Who has not claimed in 30 days?
```

---

### Campaign Insights

Examples:

```text
Which rider message generated the highest claim rate?
```

---

### Operational Recommendations

Examples:

```text
Recipients who may require follow-up.
```

---

## Important Rule

AI must consume campaign intelligence.

AI must not replace campaign intelligence.

---

# Program Blueprint Integration Plan

## Phase 0–2

Use references only.

Example:

```text
template_id
template_version
```

---

## Phase 3–5

Add analytics attribution to Program Blueprints.

---

## Phase 6+

Provide cockpit analytics grouped by:

```text
Program Blueprint
```

---

## Important Rule

Do not create a competing Campaign Template system.

Program definitions remain owned by x-change.

Campaigns consume them.

Campaigns analyze them.

Campaigns distribute them.

---

# Deferred Items

The following remain intentionally deferred:

## Marketing Automation

- funnels
- nurture sequences
- drip campaigns

---

## AI Content Generation

- SMS generation
- email generation
- content rewriting

---

## Recipient Scoring

- predictive engagement
- behavioral scoring

---

## Multi-Step Journeys

- campaign journey builders
- visual workflow builders

---

# Roadmap Summary

The planned evolution is:

```text
Architecture
    ↓
Campaigns
    ↓
Distribution
    ↓
Attribution
    ↓
Recipient Intelligence
    ↓
Analytics
    ↓
Cockpit
    ↓
Advanced Distribution
    ↓
AI Copilot
```

while preserving the core architectural separation:

```text
x-campaign
    =
    Beneficiary Distribution Platform

x-change
    =
    Execution Platform
```

This separation remains the primary architectural objective throughout the entire evolution.
