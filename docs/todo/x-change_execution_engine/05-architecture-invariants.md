# Codex Instruction — Draft `05-architecture-invariants.md`

## Objective

Create:

```text
docs/architecture/execution-engine/05-architecture-invariants.md
```

This document defines the architectural invariants that must remain true during and after the execution-engine migration.

These invariants are non-negotiable guardrails.

They should guide implementation, review, tests, and future feature development.

---

# Purpose

The execution-engine migration is not merely a refactor.

It changes the architectural center of gravity from:

```text
claim/redeem action directly causes outcome
```

to:

```text
voucher redemption activates executable instructions
```

Because of that, we need explicit invariants to prevent drift.

A future AI agent must be able to read this document and know:

```text
what must always remain true
what must never be coupled
what tests should guard the architecture
what changes are forbidden without deliberate redesign
```

---

# Core Thesis

The foundational thesis is:

```text
A Pay Code is an executable instruction contract.
```

A voucher may carry value, authority, stored-value behavior, settlement-envelope behavior, fallback behavior, or future programmable transaction behavior.

However, the voucher remains the domain contract.

Execution behavior is selected by instruction, not by uncontrolled branching.

---

# Invariant 1 — Voucher Owns Execution Semantics

## Statement

Execution semantics belong primarily to:

```text
3neti/voucher
```

not:

```text
3neti/x-change
```

## Meaning

The voucher package owns:

```text
VoucherInstructionsData
ExecutionInstructionData
ExecutionEngine
ExecutionDriverContract
ExecutionDriverRegistry
ExecutionContextData
ExecutionResultData
DefaultExecutionDriver
```

x-change consumes these through contracts.

## Test Guard

Add architecture tests proving that x-change does not instantiate concrete voucher execution classes directly.

---

# Invariant 2 — x-change Consumes Voucher Contracts

## Statement

x-change must consume voucher behavior through interfaces/contracts.

## Allowed

```php
GenerateVoucherContract
RedeemVoucherContract
VoucherExecutionContract
```

## Avoid

```php
GenerateVoucher
RedeemVoucher
ExecutionEngine
DefaultExecutionDriver
```

directly inside x-change application services.

## Test Guard

A source-scan architecture test should fail when x-change imports forbidden concrete voucher actions where a contract exists.

---

# Invariant 3 — Claim UX Is Not Execution

## Statement

The claim UI/UX, claim compiler, and form-flow journey are not the execution engine.

## Meaning

These concerns remain outside voucher execution:

```text
claim/start
claim/complete
claim experience payload
form-flow driver
Vue claim pages
```

They prepare and collect evidence.

They do not decide execution consequences.

## Correct Boundary

```text
Claim UX
    ↓
Collected data / completion context
    ↓
RedeemVoucherContract
    ↓
Execution Engine
```

---

# Invariant 4 — Redemption Activates Execution

## Statement

Voucher redemption is the activation point for execution.

## Meaning

After a voucher is validly redeemed, the system resolves and runs the configured execution behavior.

Examples:

```text
redeem → disburse
redeem → execute settlement envelope
redeem → activate stored value
redeem → authorize batch
```

## Non-Meaning

This does not mean every redemption sends money immediately.

---

# Invariant 5 — Default Behavior Must Remain Compatible

## Statement

A voucher with no explicit execution instruction must behave exactly as current vouchers behave before the migration.

## Required Default

```yaml
execution:
  driver: default
```

may be implicit.

## Test Guard

Existing voucher generation, redemption, claim submit, withdraw, and disbursement tests must pass unchanged.

---

# Invariant 6 — New Drivers Must Not Break Existing Drivers

## Statement

Adding a new execution driver must not alter default voucher behavior.

## Meaning

Settlement-envelope and stored-value drivers are additive.

They must not change:

```text
default claim
default redeem
default withdraw
default disbursement
```

## Test Guard

Every new driver suite must run alongside the full legacy compatibility suite.

---

# Invariant 7 — Drivers Are Resolved Through a Registry

## Statement

Execution drivers must be resolved through an execution driver registry.

## Avoid

```php
if ($driver === 'settlement_envelope') {
    ...
}
```

spread across application code.

## Prefer

```php
ExecutionDriverRegistry::resolve($instruction)
```

## Test Guard

Architecture tests should verify driver registration and resolution behavior.

---

# Invariant 8 — Drivers May Compose Pipelines

## Statement

Execution drivers may assemble modular execution steps.

## Meaning

Drivers can use pipeline blocks such as:

```text
validate_contract
verify_balance
load_envelope
lock_envelope
generate_child_vouchers
auto_redeem_children
disburse
fallback_to_claim
record_timeline
reconcile
```

## Boundary

Pipeline blocks are execution internals.

They should not leak into claim UI code.

---

# Invariant 9 — `voucher-pipeline.php` Remains a Compatibility Pipeline

## Statement

`voucher-pipeline.php` should not be deleted or casually rewritten.

## Meaning

Initially, it becomes the default / legacy pipeline registry used by the default execution driver.

## Target Interpretation

```text
DefaultExecutionDriver
    ↓
uses existing voucher-pipeline.php post-redemption behavior
```

Future driver-specific pipelines may exist, but legacy pipeline behavior must remain stable.

---

# Invariant 10 — Execution Instructions Are Immutable After Issuance

## Statement

Issued voucher execution instructions must not be silently mutated after issuance.

## Meaning

The behavior of an issued voucher must be stable and auditable.

If instruction changes are ever allowed, they must be explicit, audited, versioned, and outside this migration.

## Test Guard

Add tests proving execution instruction payloads are not modified during execution.

---

# Invariant 11 — Presence and Semantic Validation Remain Separate

## Statement

Redemption contract validation must preserve the distinction:

```text
inputs.fields = presence contract
validation.* = semantic contract
```

## Meaning

Collecting a field does not imply semantic validation.

Examples:

```text
inputs.fields includes otp
    means OTP must be present

validation.otp.required
    means OTP must be verified
```

```text
inputs.fields includes kyc
    means KYC payload must be present

validation.face_match.required
    means face match must pass
```

## Test Guard

Existing redemption contract tests must remain green.

New drivers must use the same validation boundary.

---

# Invariant 12 — Settlement Envelope Is a Gate, Not the Engine

## Statement

The settlement envelope package is not the execution engine.

## Meaning

Settlement envelope owns:

```text
readiness
artifacts
approvals
evidence
checklists
gates
audit
settlement state
```

Voucher execution owns:

```text
when and how an envelope is executed
```

## Correct Model

```text
Voucher
    ↓
ExecutionInstructionData
    ↓
SettlementEnvelopeExecutionDriver
    ↓
Settlement Envelope package
```

---

# Invariant 13 — Stored Value Is a Driver Behavior, Not a New Voucher Species

## Statement

Stored-value behavior should be modeled as execution behavior.

## Meaning

Avoid creating separate voucher species unless lifecycle divergence absolutely requires it.

Preferred model:

```yaml
execution:
  driver: stored_value
```

not:

```text
StoredValueVoucher
```

---

# Invariant 14 — Voucher Type Explosion Is Forbidden

## Statement

Do not create a new voucher type for every new use case.

## Meaning

Avoid:

```text
AuthorityVoucher
SettlementVoucher
TransportVoucher
MealVoucher
FuelVoucher
```

Prefer:

```text
Voucher
    +
ExecutionInstructionData
    +
Driver
```

---

# Invariant 15 — Money Movement Must Remain Auditable

## Statement

Any execution path that moves or reserves value must emit auditable state.

## Required Audit Concerns

```text
who initiated
which voucher
which driver
which amount
which recipient
which provider reference
which envelope
which child voucher
which status
which failure reason
```

## Test Guard

Execution result tests should assert metadata and events are captured.

---

# Invariant 16 — Drivers Must Return Structured Results

## Statement

Execution drivers must return structured result data.

## Required Shape

```php
ExecutionResultData
```

must include at minimum:

```text
success
events
meta
```

and may later include:

```text
status
failure_code
provider_reference
child_results
reconciliation_reference
```

---

# Invariant 17 — Execution Visibility Must Be Declared

## Statement

Execution instructions or drivers may declare visibility requirements.

Examples:

```yaml
visibility:
  timeline: true
  xray: true
  reconciliation: true
```

## Meaning

Visibility should be intentional and configurable, not incidental.

---

# Invariant 18 — Lifecycle Scenario Runner Must Use Public/Contracted Seams

## Statement

The lifecycle scenario runner must exercise the system through stable seams.

## Prefer

```text
contracts
actions
public APIs
documented orchestration services
```

## Avoid

```text
direct mutation of voucher internals
bypassing validation
bypassing execution engine
```

---

# Invariant 19 — Every Slice Must Stay Green

## Statement

No migration slice is complete until all relevant tests pass.

## Rule

Do not stack slices while tests are red.

---

# Invariant 20 — No Silent Behavior Change

## Statement

Any behavior change must be explicit, tested, and documented.

## Meaning

The default driver extraction must be behavior-preserving.

If behavior changes, the change must be deliberate and approved.

---

# Suggested Architecture Tests

Create architecture tests that assert:

```text
x-change does not import concrete voucher action classes when contracts exist
legacy vouchers resolve default execution driver
unknown execution drivers fail clearly
execution instructions remain immutable during execution
driver registry resolves drivers by key
default driver preserves voucher-pipeline behavior
settlement-envelope driver does not affect default vouchers
stored-value driver does not affect default vouchers
claim UI code does not instantiate execution drivers
```

---

# Forbidden Moves

Do not:

```text
rewrite claim UI as part of execution-engine migration
remove voucher-pipeline.php
move settlement-envelope internals into voucher
create new voucher types for every use case
allow x-change to own execution-driver logic
implement settlement-envelope driver before default driver compatibility is green
change redemption validation semantics
mutate execution instructions after issuance
bypass contracts in x-change
proceed to the next slice with failing tests
```

---

# Final Architectural Statement

The desired end-state is:

```text
Voucher
    owns the executable instruction contract.

Redemption
    activates execution.

Execution Engine
    resolves drivers.

Drivers
    compose execution behavior.

x-change
    provides product experience, APIs, lifecycle scenarios, and integrations.

Settlement Envelope
    gates readiness and evidence.

Tests
    preserve behavior and architectural direction.
```

This is the invariant foundation for evolving x-change into a programmable financial transaction operating system.

