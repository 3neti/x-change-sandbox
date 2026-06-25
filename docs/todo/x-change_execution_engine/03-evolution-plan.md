# Codex Instruction — Draft `03-evolution-plan.md`

## Objective

Create:

```text
docs/architecture/execution-engine/03-evolution-plan.md
```

This document defines the migration strategy from the current architecture documented in:

```text
01-current-state.md
```

to the target architecture documented in:

```text
02-target-state.md
```

The evolution must be:

```text
incremental
test-driven
reversible
compatibility-preserving
green-after-every-slice
```

This document is the implementation roadmap.

---

# Prime Directive

At no point during this migration may we intentionally break:

```text
voucher generation
voucher redemption
claim submit
redeem vs withdraw branching
existing disbursement behavior
existing claim UX
existing public APIs
existing lifecycle scenarios
```

Every slice must end with:

```text
all tests green
```

before the next slice begins.

---

# Migration Philosophy

This migration is not a rewrite.

This migration is an extraction.

Current behavior already works.

The goal is:

```text
preserve behavior
extract architecture
enable future drivers
```

The execution engine must emerge behind existing behavior.

Users should not notice the change.

---

# Evolution Rules

## Rule 1

Behavior first.

Architecture second.

Never introduce architecture that changes behavior unless protected by tests.

---

## Rule 2

No speculative drivers.

Only implement:

```text
DefaultExecutionDriver
```

initially.

Future drivers remain design artifacts until the compatibility driver is complete.

---

## Rule 3

No claim UX redesign.

The claim compiler and Vue experience are not part of this migration.

---

## Rule 4

No settlement-envelope implementation until execution architecture is stable.

Settlement-envelope integration is a later slice.

---

# Migration Overview

```text
Slice 0
    Characterization Baseline

Slice 1
    Contract Extraction

Slice 2
    Execution Instruction Introduction

Slice 3
    Execution Engine Introduction

Slice 4
    Default Driver Extraction

Slice 5
    Registry and Driver Resolution

Slice 6
    Architecture Stabilization

Slice 7
    Settlement Envelope Driver

Slice 8
    Stored Value Driver

Slice 9
    Optional Driver-Composed Runtime
```

Only proceed to the next slice if all tests pass.

---

# Slice 0 — Characterization Baseline

## Goal

Freeze current behavior.

## Deliverables

Additional tests protecting:

```text
GenerateVoucher
RedeemVoucher
voucher-pipeline behavior
claim submit
redeem vs withdraw
disbursement behavior
redemption validation
```

## Success Criteria

```text
Current behavior documented.
Current behavior protected by tests.
```

## No Architecture Changes

Do not introduce:

```text
contracts
drivers
execution engine
```

yet.

---

# Slice 1 — Contract Extraction

## Goal

Create dependency seams.

## Package

```text
3neti/voucher
```

## Deliverables

Introduce:

```php
GenerateVoucherContract
RedeemVoucherContract
```

Bind existing implementations.

Example:

```php
GenerateVoucherContract
    → GenerateVoucher

RedeemVoucherContract
    → RedeemVoucher
```

## x-change Changes

Replace direct dependencies on concrete voucher classes with contracts.

Example:

```php
use GenerateVoucherContract;
use RedeemVoucherContract;
```

instead of:

```php
use GenerateVoucher;
use RedeemVoucher;
```

## Success Criteria

```text
No behavior change.
All tests green.
```

---

# Slice 2 — Execution Instruction Introduction

## Goal

Introduce execution metadata.

## Deliverables

Create:

```php
ExecutionInstructionData
```

Add optional support inside:

```php
VoucherInstructionsData
```

Default:

```yaml
execution:
  driver: default
```

may be implicit.

## Important

No execution engine yet.

No behavior change.

No driver resolution yet.

## Success Criteria

Existing vouchers continue behaving exactly as before.

---

# Slice 3 — Execution Engine Introduction

## Goal

Create execution runtime.

## Deliverables

Introduce:

```php
ExecutionEngine
ExecutionContextData
ExecutionResultData
```

## Important

Execution engine is not yet responsible for behavior.

It may simply proxy to existing logic.

## Success Criteria

All behavior unchanged.

---

# Slice 4 — Default Driver Extraction

## Goal

Move current execution behavior behind a compatibility driver.

## Deliverables

Introduce:

```php
DefaultExecutionDriver
ExecutionDriverContract
```

Current redemption path becomes:

```text
RedeemVoucher
        ↓
ExecutionEngine
        ↓
DefaultExecutionDriver
        ↓
Existing behavior
```

## Critical Rule

Do not rewrite behavior.

Delegate to existing actions.

Example:

```php
DefaultExecutionDriver
    → existing redemption/disbursement implementation
```

## Success Criteria

Every existing test still passes.

---

# Slice 5 — Driver Registry

## Goal

Introduce driver resolution.

## Deliverables

Create:

```php
ExecutionDriverRegistry
```

Allow:

```text
execution.driver
```

resolution.

## Supported Driver

Only:

```text
default
```

at first.

## Success Criteria

Current behavior preserved.

---

# Slice 6 — Architecture Stabilization

## Goal

Validate architecture before new capabilities.

## Activities

Review:

```text
contracts
engine
registry
driver
context
results
```

Remove duplication.

Improve documentation.

Strengthen tests.

## Success Criteria

Stable execution architecture.

No feature additions.

---

# Slice 7 — Settlement Envelope Driver

## Goal

First non-default driver.

## Deliverables

Introduce:

```php
SettlementEnvelopeExecutionDriver
```

## Expected Behavior

```text
Redeem authority voucher
        ↓
Load settlement envelope
        ↓
Verify readiness
        ↓
Lock envelope
        ↓
Generate child vouchers
        ↓
Execute child vouchers
```

## Example Target Instruction

```yaml
execution:
  driver: settlement_envelope

settlement_envelope:
  source: csv
  auto_redeem_children: true
  fallback_to_claim: true
```

## Important

No changes to default vouchers.

---

# Slice 8 — Stored Value Driver

## Goal

Support reusable voucher balances.

## Deliverables

Introduce:

```php
StoredValueExecutionDriver
```

## Example Flow

```text
Issue Voucher
        ↓
Claim Ownership
        ↓
Activate Stored Value
        ↓
Spend via QR
```

## Example Target Instruction

```yaml
execution:
  driver: stored_value

stored_value:
  replenishable: true
```

## Important

Do not change default vouchers.

---

# Slice 9 — Driver-Composed Runtime (Optional)

## Goal

Allow drivers to assemble modular execution pipelines.

## Example

```yaml
pipeline:
  - validate_contract
  - verify_balance
  - disburse
  - reconcile
```

## Note

This slice is optional.

Only begin after previous slices are stable.

---

# Rollback Strategy

Every slice must be individually reversible.

Example:

```text
Remove execution engine.
Restore direct behavior.
```

must remain possible.

Avoid giant commits.

Prefer:

```text
one slice
one PR
one green build
```

---

# Risk Register

## High Risk

```text
Changing RedeemVoucher behavior.
Changing claim submit behavior.
Changing voucher-pipeline behavior.
```

Mitigation:

```text
Characterization tests first.
```

---

## Medium Risk

```text
Contract extraction.
Driver registry introduction.
```

Mitigation:

```text
Compatibility bindings.
```

---

## Low Risk

```text
DTO introduction.
Documentation.
```

---

# Definition of Done

The migration is considered complete when:

```text
x-change consumes voucher contracts,
voucher owns execution architecture,
all current behaviors remain intact,
default execution driver preserves existing behavior,
settlement-envelope execution is possible,
stored-value execution is possible,
all tests remain green.
```

---

# Explicit Non-Goals

This evolution plan does not include:

```text
claim UX redesign
Vue rewrites
provider rewrites
settlement-envelope redesign
dashboard redesign
reporting redesign
new payout providers
```

Those may occur later but are outside the scope of the execution-engine migration.

---

# Quality Bar

A future AI agent should be able to execute this migration one slice at a time while maintaining:

```text
green tests,
behavioral compatibility,
architectural integrity,
and rollback safety.
```
