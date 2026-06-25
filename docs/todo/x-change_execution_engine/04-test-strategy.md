# Codex Instruction — Draft `04-test-strategy.md`

## Objective

Create:

```text
docs/architecture/execution-engine/04-test-strategy.md
```

This document defines the test strategy for evolving the voucher architecture toward an execution engine.

The purpose is to ensure that the refactor is:

```text
test-first
behavior-preserving
incremental
green after every slice
```

This document must guide Codex before any execution-engine scaffolding begins.

---

# Core Testing Philosophy

This migration must use **Architectural TDD**.

That means tests must protect both:

```text
behavior
```

and:

```text
architecture
```

The risk is not only that the code breaks.

The deeper risk is that the architecture drifts while tests still pass.

Therefore, this migration needs:

```text
1. characterization tests
2. contract tests
3. feature tests
4. architecture invariant tests
5. regression tests
```

---

# Prime Rule

Assertions come first.

Before creating any new production classes, write failing tests that define the expected behavior or architecture.

Do not scaffold:

```text
ExecutionEngine
ExecutionDriverContract
DefaultExecutionDriver
ExecutionInstructionData
ExecutionDriverRegistry
```

until the relevant tests exist.

---

# Test Layers

## 1. Characterization Tests

Purpose:

```text
Freeze current behavior before refactor.
```

These tests describe what the system currently does.

They are not meant to improve behavior.

They are meant to prevent accidental behavior change.

Target areas:

```text
GenerateVoucher
RedeemVoucher
voucher-pipeline.php
claim submit
redeem vs withdraw
disbursement
redemption validation
```

---

## 2. Contract Tests

Purpose:

```text
Ensure consumers depend on stable interfaces instead of concrete classes.
```

Target areas:

```text
GenerateVoucherContract
RedeemVoucherContract
future VoucherExecutionContract
```

---

## 3. Architecture Invariant Tests

Purpose:

```text
Protect architectural direction.
```

Examples:

```text
x-change must not instantiate concrete voucher actions directly.
execution drivers must be resolved through a registry.
vouchers without execution instructions must use the default driver.
execution instructions must be immutable after issuance.
```

---

## 4. Feature Tests

Purpose:

```text
Confirm end-to-end behavior.
```

Target areas:

```text
claim/start
claim/complete
claim/submit
redeem
withdraw
disburse
```

---

## 5. Unit Tests

Purpose:

```text
Verify small execution-engine components.
```

Target areas:

```text
ExecutionInstructionData
ExecutionContextData
ExecutionResultData
ExecutionDriverRegistry
DefaultExecutionDriver
```

---

# Slice-Based Test Plan

## Slice 0 — Baseline Characterization

Before any architecture changes, add tests that prove current behavior.

### Required Tests

```php
it('generates vouchers using the current generation path');

it('redeems vouchers using the current redemption path');

it('runs the configured post-redemption pipeline after voucher redemption');

it('enforces redemption contract presence requirements');

it('enforces redemption contract semantic validation requirements');

it('preserves redeem versus withdraw branching');

it('preserves disbursement behavior for disbursable vouchers');

it('preserves current claim submit behavior in x-change');
```

### Success Criteria

All tests pass before refactor begins.

---

## Slice 1 — Contract Extraction Tests

Before creating contracts, write tests that expect contracts to exist.

### Voucher Package Tests

```php
it('resolves GenerateVoucher through GenerateVoucherContract');

it('resolves RedeemVoucher through RedeemVoucherContract');

it('binds GenerateVoucherContract to the current GenerateVoucher implementation');

it('binds RedeemVoucherContract to the current RedeemVoucher implementation');
```

### x-change Tests

```php
it('uses voucher contracts for voucher generation');

it('uses voucher contracts for voucher redemption');

it('does not instantiate concrete voucher action classes directly');
```

### Success Criteria

x-change consumes contracts.

Current behavior unchanged.

---

## Slice 2 — Execution Instruction Tests

Before adding `ExecutionInstructionData`, write tests for its intended behavior.

### Required Tests

```php
it('creates a default execution instruction when no execution block is provided');

it('hydrates execution instructions from voucher instructions');

it('defaults the execution driver to default');

it('preserves legacy voucher instruction hydration');

it('serializes execution instructions into voucher instruction payloads');
```

### Success Criteria

Legacy vouchers still work.

---

## Slice 3 — Execution Engine Tests

Before scaffolding `ExecutionEngine`, write tests describing the expected runtime.

### Required Tests

```php
it('builds execution context from a redeemed voucher');

it('resolves a driver from execution instructions');

it('executes the resolved driver');

it('returns an execution result');

it('records execution metadata without changing legacy behavior');
```

### Success Criteria

Engine exists but behavior remains compatible.

---

## Slice 4 — Default Driver Tests

Before adding `DefaultExecutionDriver`, write tests proving compatibility.

### Required Tests

```php
it('uses the default driver when no execution instruction exists');

it('uses the default driver when execution driver is default');

it('delegates to the current redemption behavior');

it('runs the existing voucher-pipeline post-redemption steps');

it('preserves disbursement behavior under the default driver');

it('preserves withdraw behavior under the default driver');
```

### Success Criteria

Default driver is a no-behavior-change extraction.

---

## Slice 5 — Driver Registry Tests

Before adding `ExecutionDriverRegistry`, write tests for driver resolution.

### Required Tests

```php
it('registers execution drivers by key');

it('resolves the default driver by key');

it('throws a clear exception for unknown execution drivers');

it('does not use if-else chains for driver selection');

it('allows package consumers to extend driver registrations');
```

### Success Criteria

Driver selection is registry-based.

---

## Slice 6 — Architecture Stabilization Tests

Add invariant tests after the default driver is stable.

### Required Tests

```php
it('all voucher redemption paths pass through the execution engine');

it('x-change does not depend on concrete voucher execution implementations');

it('execution instructions are not mutated during execution');

it('driver execution results are persisted consistently');

it('legacy vouchers remain compatible');
```

### Success Criteria

Architecture is stable enough for new drivers.

---

## Slice 7 — Settlement Envelope Driver Tests

Only after the default driver is stable, add failing tests for the settlement-envelope driver.

### Required Tests

```php
it('executes a settlement-envelope authority voucher');

it('loads the configured settlement envelope');

it('verifies settlement envelope gates before execution');

it('locks the settlement envelope before generating child vouchers');

it('generates child vouchers from settlement envelope entries');

it('auto-redeems child vouchers when configured');

it('falls back failed child executions to claim vouchers when configured');
```

### Success Criteria

Settlement-envelope behavior is driver-specific.

Default vouchers remain unchanged.

---

## Slice 8 — Stored Value Driver Tests

Only after settlement-envelope tests pass, add stored-value driver tests.

### Required Tests

```php
it('activates stored value ownership on redemption');

it('does not disburse cash on ownership claim');

it('allows slice spending after activation');

it('rejects spending above remaining balance');

it('requires OTP above configured spend threshold');

it('supports replenishable vouchers when configured');

it('rejects restricted merchant categories when configured');
```

### Success Criteria

Stored-value behavior is driver-specific.

Default vouchers remain unchanged.

---

# Characterization Coverage Target

Do not attempt meaningless 100% repository coverage.

Instead, target near-100% coverage for the danger zone:

```text
voucher generation
voucher redemption
post-redemption pipeline
redemption validation
claim submit
redeem/withdraw branching
disbursement side effects
```

The goal is:

```text
cover behavior that must not change.
```

not:

```text
cover every line.
```

---

# Architecture Invariant Test Examples

## No Concrete Voucher Action Dependency in x-change

Example assertion concept:

```php
it('does not import concrete voucher action classes in x-change application services');
```

Suggested implementation:

```text
scan x-change source files for forbidden imports
```

Forbidden examples:

```php
use LBHurtado\Voucher\Actions\GenerateVoucher;
use LBHurtado\Voucher\Actions\RedeemVoucher;
```

Allowed examples:

```php
use LBHurtado\Voucher\Contracts\GenerateVoucherContract;
use LBHurtado\Voucher\Contracts\RedeemVoucherContract;
```

---

## Default Driver Compatibility

```php
it('routes legacy vouchers through the default execution driver');
```

The assertion should prove:

```text
no execution block
    ↓
default driver
    ↓
existing behavior
```

---

## Execution Instruction Immutability

```php
it('does not mutate execution instructions after voucher issuance');
```

This protects issued vouchers from silent behavior drift.

---

## Registry-Based Driver Resolution

```php
it('resolves drivers through the execution driver registry');
```

Avoid hidden conditionals.

---

# Test Naming Standard

Use descriptive Pest test names.

Prefer:

```php
it('runs the post-redemption pipeline for legacy vouchers through the default driver');
```

Avoid:

```php
it('works');
```

---

# Test Style

Use Arrange–Act–Assert.

Example:

```php
it('uses the default driver when no execution block exists', function () {
    // Arrange

    // Act

    // Assert
});
```

Use dataset labels where scenarios multiply.

---

# CI / Green Rule

Every slice must end with:

```bash
composer test
```

or the package-specific equivalent.

If frontend or x-change behavior is affected, also run:

```bash
npm run test
```

or the relevant frontend test command.

Do not proceed to the next slice while red.

---

# Failure Handling

When tests fail:

```text
1. Stop.
2. Identify whether the failure is behavior change or test mistake.
3. Fix within the current slice.
4. Re-run full relevant suite.
5. Do not stack another slice on top of red tests.
```

---

# Test Documentation

Each slice should update:

```text
03-evolution-plan.md
```

or a changelog note with:

```text
tests added
behavior protected
status
```

---

# Non-Goals

This test strategy does not require:

```text
100% repository coverage
frontend redesign tests
new provider certification tests
settlement-envelope package redesign tests
stored-value production readiness tests
```

The goal is architectural safety, not test theater.

---

# Quality Bar

This test strategy is acceptable only if it enables Codex to:

```text
write assertions before scaffolding,
protect current behavior,
introduce execution architecture safely,
and keep the system green after every slice.
```
