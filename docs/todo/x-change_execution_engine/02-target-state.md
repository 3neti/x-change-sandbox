# Codex Instruction — Draft `02-target-state.md`

## Objective

Create:

```text
docs/architecture/execution-engine/02-target-state.md
```

This document describes the desired future architecture of the voucher execution platform after the execution-engine evolution is complete.

This is a **target-state architecture document**, not an implementation guide.

The goal is to define where the architecture is heading so that all future refactoring slices can be evaluated against a single vision.

---

# Architectural Vision

The future architecture shall evolve `3neti/voucher` from a voucher issuance and redemption package into a programmable transaction execution platform.

The central thesis is:

```text
A Pay Code is an executable instruction contract.
```

Not:

```text
A Pay Code is merely a cash voucher.
```

A voucher may represent:

```text
cash disbursement
stored value
authority
batch settlement
claimable value
future transaction types
```

without requiring new voucher species for every use case.

The execution behavior shall be expressed through execution instructions and execution drivers.

---

# Guiding Principles

## Principle 1

Voucher types should not multiply endlessly.

Avoid:

```text
CashVoucher
AuthorityVoucher
SettlementVoucher
TransportVoucher
StoredValueVoucher
...
```

Prefer:

```text
Voucher
    +
ExecutionInstructionData
    +
ExecutionDriver
```

---

## Principle 2

Redemption activates execution.

A voucher does not necessarily move money when redeemed.

Instead:

```text
Redeem Voucher
        ↓
Execution Engine
        ↓
Execution Driver
        ↓
Execution Outcome
```

Examples:

```text
redeem → disburse funds
redeem → execute settlement envelope
redeem → activate stored value
redeem → authorize batch execution
```

---

## Principle 3

Execution behavior belongs to voucher.

Execution architecture belongs primarily to:

```text
3neti/voucher
```

not:

```text
3neti/x-change
```

x-change becomes a consumer of execution contracts.

---

## Principle 4

Claim UX is not execution.

The claim journey remains:

```text
claim compiler
claim experience payload
claim Vue pages
claim API
```

Execution begins only after claim completion.

---

# Target Package Ownership

## Voucher Package

Owns:

```text
Voucher
Cash
Instructions
Validation
Execution
Execution Drivers
Execution Registry
Execution Context
Execution Results
```

---

## x-change

Owns:

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

## Settlement Envelope Package

Owns:

```text
Readiness
Artifacts
Approvals
Evidence
Checklists
Audit
Gates
Settlement State
```

The settlement envelope does not become the execution engine.

The settlement envelope becomes a participant in execution.

---

# Target Execution Architecture

## High-Level Flow

```text
Generate Voucher
        ↓
Voucher Issued
        ↓
Redeem Voucher
        ↓
Execution Engine
        ↓
Driver Resolution
        ↓
Execution Pipeline
        ↓
Execution Outcome
```

---

## Target Runtime Flow

```text
Claim UI
        ↓
Claim Submit
        ↓
RedeemVoucherContract
        ↓
Execution Engine
        ↓
Execution Driver
        ↓
Pipeline
        ↓
Events
        ↓
Persistence
        ↓
Visibility
        ↓
Outcome
```

---

# Execution Engine

## Purpose

The execution engine becomes the runtime responsible for executing voucher instructions.

Responsibilities:

```text
resolve driver
build execution context
run execution pipeline
capture results
emit events
persist execution metadata
```

The execution engine does not own:

```text
claim UI
settlement envelope logic
provider-specific payout logic
```

Those remain external concerns.

---

# Target DTO Shapes

## ExecutionInstructionData

```php
final class ExecutionInstructionData extends Data
{
    public function __construct(
        public string $driver,
        public ?string $mode = null,
        public ?string $pipeline = null,
        public array $visibility = [],
        public ?array $fallback = null,
        public array $meta = [],
    ) {}
}
```

---

## ExecutionContextData

```php
final class ExecutionContextData extends Data
{
    public Voucher $voucher;

    public ?Contact $contact;

    public array $inputs;

    public array $meta;
}
```

---

## ExecutionResultData

```php
final class ExecutionResultData extends Data
{
    public bool $success;

    public array $events;

    public array $meta;
}
```

---

# Target Driver Architecture

## Driver Contract

```php
interface ExecutionDriverContract
{
    public function key(): string;

    public function supports(
        ExecutionInstructionData $instruction
    ): bool;

    public function pipeline(
        ExecutionContextData $context
    ): array;

    public function execute(
        ExecutionContextData $context
    ): ExecutionResultData;
}
```

---

## Driver Registry

Execution drivers shall be resolved through a registry.

Avoid:

```php
if (...)
```

chains.

Prefer:

```text
ExecutionDriverRegistry
        ↓
Driver
```

resolution.

---

# Initial Driver Set

## DefaultExecutionDriver

Purpose:

```text
Preserve all current behavior.
```

This is the compatibility driver.

It delegates to existing redemption behavior.

No functional changes.

---

## SettlementEnvelopeExecutionDriver

Purpose:

```text
Execute settlement-envelope instructions.
```

Example:

```text
Redeem authority voucher
        ↓
Load envelope
        ↓
Verify gates
        ↓
Lock envelope
        ↓
Generate child vouchers
        ↓
Execute child vouchers
```

---

## StoredValueExecutionDriver

Purpose:

```text
Activate stored-value behavior.
```

Example:

```text
Redeem
        ↓
Claim ownership
        ↓
Activate balance
        ↓
Enable spending
```

---

# Target Pipeline Architecture

Drivers may assemble pipeline blocks.

Example:

```yaml
pipeline:
  - validate_contract
  - validate_owner
  - verify_balance
  - disburse
  - reconcile
```

Execution steps become modular.

---

# Target Instruction Shape

## Example Default Voucher

```yaml
cash:
  amount: 5000

execution:
  driver: default
```

---

## Example Settlement Envelope Voucher

```yaml
cash:
  amount: 0

execution:
  driver: settlement_envelope

settlement_envelope:
  source: csv
  auto_redeem_children: true
  fallback_to_claim: true
```

---

## Example Stored Value Voucher

```yaml
cash:
  amount: 10000

execution:
  driver: stored_value

stored_value:
  replenishable: true
  max_balance: 10000

spend_controls:
  otp_required_above: 1000
  daily_limit: 3000

holder:
  fields:
    - name
    - address
    - birth_date
```

---

# Settlement Envelope Vision

Settlement envelopes are not voucher types.

Settlement envelopes are not execution engines.

Settlement envelopes are readiness and authorization structures.

Example:

```text
Envelope
        ↓
Approval
        ↓
Execution Driver
        ↓
Voucher Generation
        ↓
Settlement
```

---

# Stored Value Vision

A voucher may evolve into a reusable balance instrument.

Example:

```text
Issue ₱10,000 Voucher
        ↓
Claim Ownership
        ↓
Stored Value Activated
        ↓
Spend via QR
        ↓
Debit Balance
        ↓
Credit Merchant
```

The redemption event activates ownership.

Subsequent spending consumes value.

---

# Interface-Based Consumption

Target dependency direction:

```text
x-change
        ↓
Voucher Contracts
        ↓
Voucher Implementations
```

Avoid:

```text
x-change
        ↓
Concrete Voucher Classes
```

Direct dependency on implementation classes should disappear over time.

---

# Target Visibility Model

Execution drivers may declare visibility requirements.

Examples:

```yaml
visibility:
  timeline: true
  xray: true
  reconciliation: true
```

Execution consequences become observable.

---

# Architectural End-State Summary

The target architecture transforms voucher redemption into programmable execution.

The future model becomes:

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

while preserving:

```text
claim UX
claim compiler
provider integrations
settlement envelopes
```

as separate concerns.

---

# Non-Goals

The target state does not imply:

```text
rewriting claim UX
removing voucher-pipeline.php
removing RedeemVoucher
moving settlement-envelope into voucher
creating dozens of voucher subclasses
```

The architecture should evolve incrementally through compatibility-preserving slices.

---

# Quality Bar

A future AI agent reading this document should understand:

```text
what the end-state architecture looks like,
where execution responsibility lives,
how voucher execution differs from claim UX,
why drivers exist,
why execution instructions exist,
and how settlement-envelope and stored-value use cases fit into the same model.
```
