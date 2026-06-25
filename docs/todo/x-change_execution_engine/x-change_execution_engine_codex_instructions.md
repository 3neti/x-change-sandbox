# Execution Engine Migration — Master Codex Instruction

## Mission

You are tasked with executing the **Execution Engine architecture migration** for:

```text
3neti/voucher
```

and its consuming package:

```text
3neti/x-change
```

This is a foundational architectural evolution.

Treat this as a long-running architectural program, not as a one-shot feature ticket.

The goal is to evolve voucher redemption into a programmable execution runtime while preserving all existing behavior.

---

# Repository Locations

## x-change package

All `x-change` package work must be done inside:

```text
/Users/rli/PhpstormProjects/x-change-sandbox/packages/x-change
```

Do not scaffold `x-change` execution-engine work inside the host app.

Do not move x-change work to another package.

---

## Other packages

All other packages are located under:

```text
/Users/rli/PhpstormProjects/packages
```

Important packages may include:

```text
voucher
settlement-envelope
wallet
contact
form-flow
emi-core
related payout/provider packages
```

Use this directory for the `3neti/voucher` package and related package inspection.

---

# Tooling Expectations

Laravel Boost is available and should be used as the primary Laravel inspection mechanism.

Use Laravel Boost to inspect:

```text
routes
controllers
requests
service providers
bindings
models
actions
DTOs
tests
package structure
configuration
```

Also use normal repository tools when helpful:

```bash
git status
git diff
git log
git branch
rg
find
composer test
npm run test
npm run test:frontend
```

Discover the actual test commands before assuming them.

---

# Required Reading

Before making any code changes, read and internalize the following documents.

They should exist under the execution-engine architecture documentation directory:

```text
docs/architecture/execution-engine/01-current-state.md
docs/architecture/execution-engine/02-target-state.md
docs/architecture/execution-engine/03-evolution-plan.md
docs/architecture/execution-engine/04-test-strategy.md
docs/architecture/execution-engine/05-architecture-invariants.md
```

If these documents are in the x-change package, read them from:

```text
/Users/rli/PhpstormProjects/x-change-sandbox/packages/x-change/docs/architecture/execution-engine
```

If equivalent documents exist in the voucher package, inspect those as well.

These documents are authoritative.

Do not redesign them casually.

If implementation findings contradict the documents, stop and report the discrepancy before proceeding.

---

# Core Thesis

The architectural direction is:

```text
A Pay Code is an executable instruction contract.
```

The future architecture becomes:

```text
Voucher
    ↓
Execution Instruction
    ↓
Execution Engine
    ↓
Execution Driver
    ↓
Execution Pipeline
    ↓
Execution Outcome
```

while preserving the following as separate concerns:

```text
Claim UI
Claim Compiler
Claim Experience
Settlement Envelope
Provider Integrations
Lifecycle Scenarios
```

---

# Package Ownership

## Primary Refactor Target

```text
/Users/rli/PhpstormProjects/packages/voucher
```

The voucher package should own:

```text
VoucherInstructionsData
ExecutionInstructionData
ExecutionEngine
ExecutionDriverContract
ExecutionDriverRegistry
ExecutionContextData
ExecutionResultData
DefaultExecutionDriver
voucher execution semantics
```

---

## Secondary Adaptation Target

```text
/Users/rli/PhpstormProjects/x-change-sandbox/packages/x-change
```

The x-change package should consume voucher behavior through contracts.

x-change must not own execution-driver logic.

x-change owns:

```text
Claim Experience
Claim Compiler
Claim UI
Disbursement Providers
Lifecycle Scenarios
Analytics
Reporting
Settlement Integrations
```

---

# Full Roadmap

This is the full migration roadmap.

You must understand the whole roadmap, but you are only authorized to execute the currently approved slice.

## Slice 0 — Characterization Baseline

Freeze current behavior before architecture changes.

Protect:

```text
GenerateVoucher
RedeemVoucher
voucher-pipeline behavior
claim submit
redeem vs withdraw branching
disbursement behavior
redemption validation
```

No architecture changes.

---

## Slice 1 — Contract Extraction

Introduce stable voucher contracts.

Expected direction:

```text
GenerateVoucherContract
RedeemVoucherContract
```

Bind them to existing implementations.

Update x-change to consume contracts instead of concrete voucher action classes.

---

## Slice 2 — Execution Instruction Introduction

Introduce:

```text
ExecutionInstructionData
```

Add optional execution instruction support inside:

```text
VoucherInstructionsData
```

Legacy behavior must remain unchanged.

Implicit default:

```yaml
execution:
  driver: default
```

---

## Slice 3 — Execution Engine Introduction

Introduce:

```text
ExecutionEngine
ExecutionContextData
ExecutionResultData
```

The engine may initially proxy current behavior.

No behavior change.

---

## Slice 4 — Default Driver Extraction

Introduce:

```text
ExecutionDriverContract
DefaultExecutionDriver
```

Current redemption behavior becomes:

```text
RedeemVoucher
    ↓
ExecutionEngine
    ↓
DefaultExecutionDriver
    ↓
Existing behavior
```

This must be a no-behavior-change extraction.

---

## Slice 5 — Driver Registry

Introduce:

```text
ExecutionDriverRegistry
```

Driver resolution must be registry-based.

Initially only the default driver is required.

---

## Slice 6 — Architecture Stabilization

Review and stabilize:

```text
contracts
engine
registry
driver
context
results
tests
documentation
```

No feature additions.

---

## Slice 7 — Settlement Envelope Driver

Introduce:

```text
SettlementEnvelopeExecutionDriver
```

Target behavior:

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

Do not implement this before the default driver architecture is stable and green.

---

## Slice 8 — Stored Value Driver

Introduce:

```text
StoredValueExecutionDriver
```

Target behavior:

```text
Issue Voucher
    ↓
Claim Ownership
    ↓
Activate Stored Value
    ↓
Spend via QR / slice spending
```

Do not implement this before earlier slices are stable and green.

---

## Slice 9 — Optional Driver-Composed Runtime

Allow drivers to assemble modular execution pipelines.

Example:

```yaml
pipeline:
  - validate_contract
  - verify_balance
  - disburse
  - reconcile
```

This is optional and should only be considered after previous slices are stable.

---

# Current Authorization

You are authorized to execute only:

```text
Slice 0 — Characterization Baseline
```

Do not proceed to Slice 1 without explicit human approval.

You may read, understand, and reference the full roadmap.

You may not implement contracts, execution instructions, execution engine, drivers, or driver registry yet.

---

# Slice 0 Mission

Create a behavior-preserving baseline before architectural changes.

The goal is not to improve behavior.

The goal is to freeze current behavior so later refactors can be performed safely.

---

# Slice 0 Required Activities

## 1. Read architecture documents

Read:

```text
01-current-state.md
02-target-state.md
03-evolution-plan.md
04-test-strategy.md
05-architecture-invariants.md
```

Summarize the actionable implications.

---

## 2. Inspect current source code

Inspect both package areas:

```text
/Users/rli/PhpstormProjects/packages/voucher
/Users/rli/PhpstormProjects/x-change-sandbox/packages/x-change
```

Use Laravel Boost where useful.

Inspect:

```text
voucher generation actions
voucher redemption actions
voucher observers
voucher-pipeline.php
redemption contract validators
claim submit path
redeem vs withdraw branching
disbursement behavior
tests
service providers
bindings
```

---

## 3. Produce a Slice 0 discovery report

Create or update:

```text
docs/architecture/execution-engine/reports/000-slice-0-characterization-baseline.md
```

Put this report in the most appropriate package documentation location after inspection.

If the existing architecture docs live in x-change, place the report beside them.

The report must include:

```text
files inspected
current test inventory
coverage observations
current danger zones
recommended characterization tests
test commands discovered
risks / uncertainties
```

---

## 4. Add characterization tests

Add tests only for current behavior.

Suggested test themes:

```text
voucher generation uses the current path
voucher redemption uses the current path
post-redemption pipeline runs after redemption
redemption contract presence rules are enforced
redemption contract semantic rules are enforced
redeem vs withdraw branching is preserved
disbursement behavior is preserved
claim submit behavior is preserved
legacy voucher behavior is unchanged
```

Do not add speculative tests for future drivers yet.

---

## 5. Run relevant test suites

Discover and run the correct test commands.

Likely commands may include:

```bash
composer test
vendor/bin/pest
npm run test
npm run test:frontend
```

Run package-specific tests as appropriate.

Document exact commands and results in the Slice 0 report.

---

# Forbidden During Slice 0

Do not introduce:

```text
GenerateVoucherContract
RedeemVoucherContract
ExecutionInstructionData
ExecutionEngine
ExecutionContextData
ExecutionResultData
ExecutionDriverContract
ExecutionDriverRegistry
DefaultExecutionDriver
SettlementEnvelopeExecutionDriver
StoredValueExecutionDriver
```

Do not change public behavior.

Do not refactor production code except when absolutely necessary to support tests, and if necessary, stop and report first.

Do not rewrite claim UI.

Do not change voucher-pipeline.php behavior.

Do not change redemption validation semantics.

Do not change disbursement behavior.

---

# Architecture Invariants To Preserve

During Slice 0, preserve all invariants from:

```text
05-architecture-invariants.md
```

Especially:

```text
Claim UX is not execution.
Settlement envelope is a gate, not the engine.
voucher-pipeline.php remains a compatibility pipeline.
Presence and semantic validation remain separate.
No silent behavior change.
Every slice must stay green.
```

---

# Expected First Response From Codex

Do not start coding immediately.

First respond with:

```text
1. repository locations confirmed
2. architecture documents found
3. tooling detected, including Laravel Boost availability
4. test commands discovered or to be discovered
5. Slice 0 inspection plan
6. risks and caution areas
```

Then proceed with inspection and the Slice 0 report.

---

# Commit Discipline

Use coherent commits.

Preferred rule:

```text
1 slice = 1 green commit
```

Since Slice 0 may involve multiple characterization tests, commit only after:

```text
tests are added
tests are passing
report is updated
git diff is reviewed
```

Suggested commit message:

```text
test(execution-engine): add slice 0 characterization baseline
```

---

# Stop Conditions

Stop and report immediately if:

```text
architecture docs are missing
repository paths differ significantly
Laravel Boost is unavailable
test suite has unrelated pre-existing failures
current behavior is unclear
a production code change appears necessary
a test reveals behavior that conflicts with the architecture documents
```

Do not paper over these.

Do not silently guess.

---

# Slice 0 Definition of Done

Slice 0 is complete only when:

```text
current behavior has been inspected
Slice 0 discovery report exists
characterization tests have been added
all relevant tests pass or pre-existing failures are documented
no execution-engine production classes have been introduced
no public behavior has changed
```

At completion, stop and request approval before proceeding to:

```text
Slice 1 — Contract Extraction
```
