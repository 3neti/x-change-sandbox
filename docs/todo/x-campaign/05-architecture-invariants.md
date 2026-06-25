# 05-architecture-invariants.md

# x-campaign Architecture Invariants
## Non-Negotiable Rules of the Beneficiary Distribution Platform

**Package:** `3neti/x-campaign`  
**Status:** Architecture Invariants  
**Version:** 1.0

---

# Purpose of this Document

This document defines the architectural rules that must remain true regardless of:

- implementation phase
- refactoring
- optimization
- feature additions
- AI-assisted development

These invariants exist to preserve:

- package boundaries
- system clarity
- scalability
- maintainability

Any future implementation that violates these invariants should be considered architecturally incorrect, even if technically functional.

---

# Core Identity Invariant

## Invariant 1

```text
x-campaign is a Beneficiary Distribution Platform.
```

It is not:

- an execution platform
- a payment platform
- a voucher platform
- a notification platform

---

## Consequence

All future design decisions must answer:

```text
Does this improve beneficiary distribution?
```

before being accepted.

---

# Boundary Invariants

## Invariant 2

```text
x-campaign owns distribution.
```

---

### x-campaign owns

- audiences
- recipients
- campaigns
- distribution
- delivery tracking
- engagement tracking
- attribution
- analytics

---

### x-campaign does not own

- claims
- redemptions
- withdrawals
- settlements
- disbursements
- wallet mutations
- Pay Code execution

---

# Execution Invariant

## Invariant 3

```text
x-change remains the Execution Platform.
```

---

### x-change owns

- Program Blueprints
- Voucher Templates
- Pay Code generation
- claim lifecycle
- redemption
- withdrawal
- disbursement
- settlement

---

### x-campaign consumes execution information

It never owns execution information.

---

# Program Blueprint Invariant

## Invariant 4

```text
Program definitions belong to x-change.
```

---

### Examples

```text
Educational Assistance
Payroll
Scholarship
Medical Assistance
Disaster Relief
```

remain owned by x-change.

---

### Consequence

x-campaign must not create a competing template architecture.

---

### Allowed

```text
ProgramBlueprintReference
ProgramBlueprintProjection
ProgramAnalytics
```

---

### Forbidden

```text
CampaignProgramBlueprint
CampaignVoucherTemplate
CampaignClaimTemplate
```

or any duplicate implementation.

---

# Campaign Invariant

## Invariant 5

```text
Every campaign is a distribution event.
```

---

Campaigns exist to:

- reach recipients
- distribute information
- distribute Pay Codes
- measure engagement

Campaigns do not exist to execute financial workflows.

---

# Audience Invariant

## Invariant 6

```text
Recipients belong to audiences.
Audiences belong to campaigns.
```

---

### Consequence

Audience management remains a first-class capability.

Campaigns should never degrade into:

```text
send arbitrary messages
```

without recipient context.

---

# Recipient Invariant

## Invariant 7

```text
Recipient history is cumulative.
```

---

Recipients are long-lived entities.

Campaigns are transient entities.

---

### Consequence

The platform must answer:

```text
What has this recipient received over time?
```

without requiring campaign reconstruction.

---

# Attribution Invariant

## Invariant 8

```text
Attribution must be explicit.
```

---

The system must never infer attribution.

---

### Every engagement event should be traceable to:

```text
campaign
recipient
delivery
cta
channel
```

---

### Examples

Clicks.

Scans.

Views.

Claims.

---

# Campaign Link Invariant

## Invariant 9

```text
Campaign links are attribution assets.
```

---

Campaign links exist to:

- track
- attribute
- analyze

---

Campaign links do not own workflow behavior.

---

### Workflow behavior belongs to x-change.

---

# CTA Invariant

## Invariant 10

```text
CTA ownership belongs to workflow owners.
```

---

Typically:

```text
x-change
```

owns CTA meaning.

---

### Examples

```text
Claim
Pay
Settle
Disburse
```

remain execution concerns.

---

### x-campaign owns

```text
delivery
tracking
engagement
analytics
```

for those CTAs.

---

# QR Invariant

## Invariant 11

```text
QR payloads belong to x-change.
```

---

### x-change owns

- QR meaning
- QR destination
- QR workflow

---

### x-campaign owns

- QR presentation
- QR branding
- QR analytics
- QR attribution

---

# Delivery Invariant

## Invariant 12

```text
All distribution channels are drivers.
```

---

The system must remain provider-agnostic.

---

### Examples

```text
sms
email
webhook
csv_export
print_mailer
```

---

### Future channels

```text
whatsapp
viber
messenger
push
```

must fit the same contract.

---

# Queue Invariant

## Invariant 13

```text
Campaign execution is asynchronous.
```

---

### Consequence

Large campaigns must never execute synchronously.

---

### Required modes

```text
immediate
scheduled
queued
batched
```

---

### Internal implementation

must assume:

```text
queue-first
```

architecture.

---

# Scale Invariant

## Invariant 14

```text
The architecture must support one recipient and one million recipients equally.
```

---

The architecture must not change based on scale.

Only execution strategy may change.

---

# Analytics Invariant

## Invariant 15

```text
Analytics are first-class functionality.
```

---

Analytics are not optional reporting.

Analytics are core product capabilities.

---

### New features should answer:

```text
How will this be measured?
```

before implementation.

---

# Snapshot Invariant

## Invariant 16

```text
Historical communications must remain reconstructable.
```

---

The platform must preserve rendered snapshots.

---

### Examples

```text
rendered_message
rendered_splash
rendered_url
rendered_qr_asset
```

---

### Consequence

Analytics must never rely solely on mutable templates.

---

# Rider Invariant

## Invariant 17

```text
Rider content is analyzable content.
```

---

Examples:

```text
rider.message
rider.splash
rider.url
```

---

The platform must support:

- engagement analysis
- attribution analysis
- performance analysis

for rider content.

---

# Engagement Invariant

## Invariant 18

```text
Engagement events belong to x-campaign.
```

---

Examples:

- clicks
- scans
- opens
- views

---

### Consequence

These events should not be owned by x-feedback.

---

# Claim Invariant

## Invariant 19

```text
Claims belong to x-change.
```

---

Campaigns consume claim information.

Campaigns do not determine claim outcomes.

---

### Allowed

```text
claim analytics
claim attribution
claim reporting
```

---

### Forbidden

```text
claim execution
claim validation
claim orchestration
```

---

# Feature Profile Invariant

## Invariant 20

```text
Feature Profiles are first-class.
```

---

Examples:

```text
default
dbp
lgu
dswd
private-bank
```

---

Feature Profiles may influence:

- branding
- messaging
- presentation
- analytics segmentation

---

Feature Profiles must not change business ownership boundaries.

---

# Cockpit Invariant

## Invariant 21

```text
x-change owns the cockpit shell.
```

---

### x-campaign owns

- campaign modules
- analytics modules
- recipient modules

---

### x-change owns

- navigation
- shell
- orchestration

---

### Consequence

Campaign functionality must be installable and removable without breaking cockpit architecture.

---

# Installability Invariant

## Invariant 22

```text
x-campaign must remain vendor-installable.
```

---

The package should support:

```bash
php artisan x-campaign:install
```

and integration through:

```bash
php artisan x-change:install --force
```

or future package-aware installation flows.

---

# AI Invariant

## Invariant 23

```text
AI consumes intelligence.
AI does not replace intelligence.
```

---

Before AI features exist, the platform must already possess:

- campaign intelligence
- recipient intelligence
- attribution intelligence
- analytics intelligence

---

AI should be layered on top of those capabilities.

---

# Ownership Invariant

## Invariant 24

```text
One concept has one owner.
```

---

Examples:

| Concept | Owner |
|----------|----------|
| Program Blueprint | x-change |
| Pay Code | x-change |
| Claim | x-change |
| Disbursement | x-change |
| Campaign | x-campaign |
| Audience | x-campaign |
| Recipient Intelligence | x-campaign |
| Attribution | x-campaign |
| Analytics | x-campaign |
| Notifications | x-feedback |
| Audit Trail | x-journal |

---

No concept should have multiple competing owners.

---

# Architectural Summary

The architecture is successful only if the following remains true:

```text
x-change
    =
    Execution Platform

x-campaign
    =
    Beneficiary Distribution Platform

x-feedback
    =
    Communication Infrastructure

x-journal
    =
    Historical System of Record
```

and no package absorbs responsibilities that properly belong to another.

---

# Final Invariant

If a future feature proposal creates ambiguity, apply this test:

```text
Does this concern
distribution
or
execution?
```

If the answer is:

```text
distribution
```

it likely belongs in x-campaign.

If the answer is:

```text
execution
```

it likely belongs in x-change.

This rule supersedes implementation convenience and should guide all future architectural decisions.
