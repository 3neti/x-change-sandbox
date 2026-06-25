# 04-test-strategy.md

# x-campaign Test Strategy
## Testing the Beneficiary Distribution Platform

**Package:** `3neti/x-campaign`  
**Status:** Test Strategy  
**Version:** 1.0

---

# Purpose of this Document

This document defines the testing philosophy, coverage expectations, testing layers, and architectural testing rules for `x-campaign`.

The objective is not merely to achieve code coverage.

The objective is to guarantee:

- architectural integrity
- package boundary enforcement
- deterministic campaign execution
- analytics correctness
- attribution correctness
- scalability confidence

throughout the evolution of the package.

---

# Guiding Principles

## Principle 1

Test architecture before implementation.

The package exists primarily to enforce a clean separation between:

```text id="m4x25g"
Distribution
```

and

```text id="m1fxv6"
Execution
```

Therefore architecture tests are first-class tests.

---

## Principle 2

Analytics correctness is a core feature.

Campaign analytics are not optional reporting.

Analytics are part of the product.

Analytics calculations must be tested as rigorously as business workflows.

---

## Principle 3

Attribution must never be inferred.

Attribution must be explicitly captured and verifiable.

Tests should verify attribution chains.

---

## Principle 4

Every integration point must be mockable.

The package should remain testable without:

- SMS providers
- email providers
- webhook providers
- x-change runtime

---

## Principle 5

Package boundaries must be enforceable through tests.

x-campaign must never silently absorb x-change responsibilities.

---

# Testing Framework

## Test Framework

Use:

```text id="n8f9on"
Pest
```

as the primary testing framework.

---

## Style

Use:

```text id="e2v4mr"
Arrange
Act
Assert
```

for all tests.

---

## Naming

Prefer:

```php id="z4s6zl"
it('imports recipients from csv')
```

instead of:

```php id="pvvl8u"
test_csv_import
```

---

# Testing Layers

The package should maintain five testing layers:

```text id="a2s0dg"
Unit
Integration
Workflow
Analytics
Architecture
```

---

# Layer 1
# Unit Tests

## Purpose

Verify individual classes in isolation.

---

## Coverage Targets

### DTOs

Test:

- construction
- serialization
- normalization
- defaults

Examples:

```text id="vf9w9v"
CampaignData
CampaignRecipientData
CampaignDeliveryResultData
```

---

### Services

Test:

- calculations
- transformations
- aggregations

---

### Resolvers

Test:

- Feature Profile resolution
- Channel resolution
- Program Blueprint resolution

---

## Goal

100% deterministic.

No external dependencies.

---

# Layer 2
# Integration Tests

## Purpose

Verify collaboration between components.

---

## Campaign Creation

Verify:

```text id="o0pr4g"
Campaign
    ↓
Execution
    ↓
Recipients
```

relationships.

---

## Audience Imports

Verify:

```text id="5rlp0p"
CSV
Excel
API
Manual Entry
```

imports create recipients correctly.

---

## Channel Drivers

Verify:

```text id="9zsyxp"
Driver
    ↓
Delivery Result
```

contract behavior.

---

## Goal

Ensure components collaborate correctly.

---

# Layer 3
# Workflow Tests

## Purpose

Verify complete campaign flows.

---

## Campaign Execution Workflow

Verify:

```text id="n74yff"
Campaign
    ↓
Audience
    ↓
Recipients
    ↓
Distribution
```

works end-to-end.

---

## Pay Code Delegation Workflow

Verify:

```text id="mkj5q6"
Campaign
    ↓
PayCodeGenerationGateway
```

delegates to x-change.

---

## Important Rule

Tests must verify:

```text id="q1g1ho"
delegation
```

not:

```text id="0tzvlv"
voucher generation
```

because voucher generation belongs to x-change.

---

## Delivery Workflow

Verify:

```text id="mw9joh"
queued
sent
delivered
failed
```

transitions.

---

## CTA Workflow

Verify:

```text id="dtxq4s"
link
    ↓
click
    ↓
redirect
```

behavior.

---

## QR Workflow

Verify:

```text id="f1ml8m"
qr asset
    ↓
scan event
```

tracking behavior.

---

# Layer 4
# Analytics Tests

## Purpose

Verify all analytics calculations.

This layer is critical.

---

# Campaign Analytics

Verify:

```text id="d2dl13"
recipient_count
generated_count
sent_count
delivered_count
failed_count
claimed_count
claim_rate
```

---

## Example

Given:

```text id="a6m2z8"
100 recipients
80 claimed
```

Expect:

```text id="7a3hkl"
claim_rate = 80%
```

---

# Delivery Analytics

Verify:

- delivery rate
- failure rate
- channel performance

---

# Engagement Analytics

Verify:

- click rate
- scan rate
- open rate

---

# Recipient Analytics

Verify:

- total value sent
- total value claimed
- campaign count

---

# Program Analytics

Verify aggregation by:

```text id="ggov02"
Program Blueprint
```

---

# Rider Analytics

Verify:

```text id="vw2t3y"
message performance
splash performance
cta performance
```

aggregation logic.

---

# Important Rule

Every analytics calculation must have:

```text id="89a90m"
positive test
negative test
edge case test
```

---

# Layer 5
# Architecture Tests

## Purpose

Protect package boundaries.

This is the most important layer.

---

# Boundary Rule

x-campaign must not own:

- Pay Code generation
- voucher creation
- claim execution
- redemption
- disbursement
- settlement

---

## Tests

Verify:

```text id="6k0drw"
Campaign
    ↓
Gateway
    ↓
x-change
```

delegation exists.

---

Verify:

```text id="7bq8c8"
no voucher generation service
```

exists inside x-campaign.

---

Verify:

```text id="4m6jqz"
no claim execution service
```

exists inside x-campaign.

---

# Program Blueprint Ownership

Verify:

Program Blueprints are consumed but never owned.

---

## Tests

Ensure:

```text id="i5p8mk"
ProgramBlueprint
```

references are used.

No duplicate blueprint implementation exists.

---

# Feature Profile Tests

## Purpose

Verify institution-specific behavior.

---

## Cases

Test:

```text id="n9g1jc"
default
dbp
lgu
dswd
private-bank
```

resolution.

---

## Verify

Correct:

- branding
- CTA content
- rider content
- splash content

selection.

---

# Recipient Intelligence Tests

## Purpose

Verify recipient history calculations.

---

## Cases

Recipient receives:

```text id="yj14m8"
Campaign A
Campaign B
Campaign C
```

Verify:

- participation count
- value totals
- claim totals

---

## Timeline Tests

Verify:

```text id="c4mh38"
delivery
click
claim
```

ordering.

---

# Snapshot Tests

## Purpose

Protect historical accuracy.

---

## Verify

Stored snapshot remains unchanged even if:

- Program Blueprint changes
- rider content changes
- Feature Profile changes

---

## Important Rule

Historical records must remain immutable.

---

# Campaign Link Tests

## Purpose

Verify attribution.

---

## Cases

Track:

```text id="6w8jht"
campaign
recipient
delivery
cta
```

through a click.

---

## Verify

Attribution survives redirect.

---

# QR Analytics Tests

## Purpose

Verify QR attribution.

---

## Cases

Track:

```text id="l7ud8f"
campaign
recipient
qr asset
```

through scan events.

---

# Queue Tests

## Purpose

Verify scalability assumptions.

---

## Cases

Campaign sizes:

```text id="bxbpv6"
1
10
100
1,000
10,000
```

recipients.

---

## Verify

Batch creation.

---

Verify:

queue dispatching.

---

# Failure Handling Tests

## Purpose

Verify resiliency.

---

## Cases

### SMS Failure

Delivery marked failed.

---

### Email Failure

Delivery marked failed.

---

### Redirect Failure

Attribution preserved.

---

### Provider Timeout

Retry behavior.

---

# Cockpit Tests

## Purpose

Verify integration contracts.

---

## Verify

Campaign analytics providers expose:

```text id="w6qu0i"
summary
funnel
recipient data
```

to the cockpit.

---

## Important Rule

Cockpit tests should verify:

```text id="z7v3zh"
contract compliance
```

not UI rendering.

UI rendering belongs to frontend tests.

---

# Frontend Tests

## Future Scope

When UI modules exist:

Test:

- Campaign Dashboard
- Campaign Explorer
- Recipient Explorer
- Analytics Views

using:

```text id="6rkf6d"
Vitest
```

and component testing.

---

# Coverage Expectations

## Critical Components

Target:

```text id="jq2nzt"
100%
```

coverage.

Examples:

- analytics
- attribution
- aggregation
- recipient intelligence

---

## Standard Components

Target:

```text id="b2h2vf"
90%+
```

coverage.

Examples:

- actions
- services
- DTOs

---

## UI Components

Target:

```text id="i8b6k2"
meaningful coverage
```

rather than arbitrary percentages.

---

# Test Data Strategy

Use:

```text id="drig9j"
small
medium
large
```

datasets.

---

## Large Dataset Tests

Simulate:

```text id="pj4gq8"
1,000+
recipients
```

without provider dependencies.

---

# Success Criteria

The test suite is successful when it can guarantee:

```text id="gax52z"
campaign correctness
analytics correctness
attribution correctness
recipient intelligence correctness
boundary correctness
```

without requiring live providers or live x-change execution.

---

# One-Line Summary

The x-campaign test strategy prioritizes architecture protection, analytics correctness, attribution integrity, and recipient intelligence while ensuring the package remains a clean distribution layer that never absorbs execution responsibilities owned by x-change.
