# Codex Instruction — Draft `01-current-state.md`

## Objective

Create `docs/architecture/execution-engine/01-current-state.md`.

This document must describe the **current voucher generation, redemption, claim, withdrawal, and post-redemption execution architecture** before any refactor toward an execution engine.

This is a discovery and characterization document only.

Do **not** scaffold new execution-engine classes yet.

---

# Primary Goal

Document the current state so that future refactoring can proceed safely.

The document must answer:

1. How are vouchers currently generated?
2. How are vouchers currently redeemed?
3. How does x-change currently initiate and complete claim flows?
4. Where does redeem vs withdraw branching happen?
5. Where does disbursement happen?
6. Where does `voucher-pipeline.php` participate?
7. Which parts belong to `3neti/voucher`?
8. Which parts belong to `3neti/x-change`?
9. Which parts belong to host app / provider integrations?
10. Which behaviors must be protected by characterization tests before refactoring?

---

# Repositories / Packages to Inspect

Inspect the current codebase, especially:

```text
packages/voucher
packages/x-change
config/voucher-pipeline.php
config/form-flow-drivers/*
routes related to claim / redeem / disburse
tests related to voucher generation, redemption, claim, withdraw, disbursement
```

If package paths differ, locate the equivalent package directories.

---

# Important Context

We are exploring a future architecture where:

```text
Voucher redemption activates an execution engine.
The execution engine resolves an execution driver.
The default driver preserves current behavior.
Future drivers may support settlement-envelope execution, stored-value vouchers, batch remittance, authority vouchers, and other programmable transaction flows.
```

However, `01-current-state.md` must **not design the future state**.

It must faithfully describe the present.

---

# Required Sections

## 1. Current Architectural Summary

Explain the present architecture in plain language.

Include a concise diagram like:

```text
x-change claim UI/API
    ↓
claim/start or equivalent
    ↓
claim/complete or equivalent
    ↓
claim/submit or redeem endpoint
    ↓
voucher package redemption action
    ↓
voucher observer / post-redemption pipeline
    ↓
redeem / withdraw / disburse side effects
```

Adjust the diagram to match actual code.

---

## 2. Voucher Generation Path

Document the actual current path for voucher generation.

Identify:

- controller / API entry point
- action class
- DTOs used
- instruction DTO used
- cash/value creation
- events fired
- post-generation pipeline, if any
- tests covering this path

Expected areas to inspect include but are not limited to:

```text
GenerateVoucher
GenerateVouchers
VoucherInstructionsData
voucher-pipeline.php post-generation
voucher-pipeline.php mint-cash
```

Write exact class names and namespaces.

---

## 3. Voucher Redemption Path

Document the actual current path for voucher redemption.

Identify:

- public entry point
- action class
- redemption context shape
- contact / redeemer resolution
- voucher state mutation
- observer behavior
- events fired
- post-redemption pipeline
- tests covering this path

Expected areas to inspect include but are not limited to:

```text
RedeemVoucher
VoucherObserver
HandleRedeemedVoucher
voucher-pipeline.php post-redemption
```

Write exact class names and namespaces.

---

## 4. Claim UI / Claim API Path in x-change

Document the current x-change claim path.

Identify:

- route names / URIs
- controllers
- request classes
- actions
- DTOs
- Vue / Inertia pages if applicable
- claim preparation
- claim completion
- claim submit
- how collected data is passed into voucher redemption

Important: distinguish clearly between:

```text
claim UX / form-flow / compiler concerns
```

and

```text
voucher execution / redemption concerns
```

The Claim UI/UX should be treated as a consumer of voucher redemption, not as the future execution engine itself.

---

## 5. Redeem vs Withdraw Branching

Document how the system currently decides whether a claim becomes:

```text
redeem
```

or

```text
withdraw
```

Identify:

- factory or branching logic
- service classes
- DTOs
- conditions
- voucher instruction fields involved
- tests that prove the behavior

Do not rename these concepts.

Only document current behavior.

---

## 6. Current Disbursement Behavior

Document where actual money movement or payout request creation happens.

Identify:

- disbursement classes
- payout provider abstractions
- Paynamics / Netbank / null provider hooks if present
- provider transaction metadata
- disbursement status handling
- fallback behavior if present
- tests covering this path

Clarify whether disbursement is synchronous, queued, event-driven, pipeline-driven, or provider-driven.

---

## 7. Current `voucher-pipeline.php` Role

Inspect `voucher-pipeline.php`.

Document every configured lifecycle bucket, such as:

```php
'post-generation'
'mint-cash'
'post-redemption'
```

For each bucket, list:

- configured pipeline steps
- package/class ownership
- what each step appears to do
- whether it is generation-time, redemption-time, or disbursement-time behavior

Important conclusion to verify:

```text
voucher-pipeline.php currently acts as the global/default lifecycle pipeline configuration.
```

Do not replace it.

Just document it.

---

## 8. Current Redemption Contract Validation

Document how redemption validation currently works.

Inspect:

```text
ValidateRedemptionContract
RedemptionContractEngine
RedemptionEvidenceExtractor
RequiredInputFieldsValidator
semantic validators
```

Document the key rule:

```text
inputs.fields = presence contract
validation.* = semantic contract
```

Include examples from actual tests if available.

Identify existing test coverage.

---

## 9. Current Package Boundary Map

Create a table:

| Concern | Current Owner | Notes |
|---|---|---|
| Voucher identity | voucher | |
| Voucher generation | voucher | |
| Voucher redemption | voucher | |
| Redemption validation | voucher | |
| Claim UI/UX | x-change | |
| Claim API | x-change | |
| Claim compiler / form-flow | x-change / form-flow | |
| Disbursement provider integration | x-change / provider packages | |
| Settlement envelope readiness | settlement-envelope | |
| Lifecycle scenario runner | x-change | |

Adjust based on actual source code.

---

## 10. Direct Concrete Dependencies

Identify where `x-change` currently depends directly on concrete voucher classes.

Search for imports/usages such as:

```php
use ...\GenerateVoucher;
use ...\GenerateVouchers;
use ...\RedeemVoucher;
use ...\ValidateRedemptionContract;
```

Document:

- file path
- class
- concrete dependency
- recommended future contract seam

Do not change code yet.

This will feed the later `03-evolution-plan.md`.

---

## 11. Current Test Coverage Inventory

List existing tests related to:

- voucher generation
- voucher redemption
- redemption contract validation
- claim start
- claim complete
- claim submit
- redeem vs withdraw
- disbursement
- voucher pipeline
- provider payout
- lifecycle scenario runner

For each test file, summarize what behavior it protects.

---

## 12. Baseline Characterization Test Gaps

Identify missing tests that should be added before refactoring.

Focus only on the refactor danger zone.

Suggested gap categories:

```text
- x-change should preserve current claim submit behavior
- voucher redemption should trigger current post-redemption pipeline
- vouchers without future execution block should behave exactly as today
- redeem vs withdraw branching should remain unchanged
- disbursement pipeline should still run under current conditions
- redemption validation should continue enforcing presence and semantic rules
- concrete x-change dependencies should be identified before contract migration
```

Do not implement these tests yet.

Just list them as baseline gaps.

---

## 13. Current-State Risks

Document risks in the current design that motivate the future execution-engine refactor.

Examples:

```text
- x-change may know too much about concrete voucher actions
- post-redemption behavior is global rather than driver-specific
- voucher-pipeline.php has no explicit driver selection
- authority voucher / settlement-envelope execution has no clean seam
- stored-value voucher behavior has no clean execution model
- current architecture may conflate redemption activation with disbursement consequence
```

Keep this section factual and grounded in actual code.

---

## 14. Non-Goals for This Document

Explicitly state that this document does not:

```text
- introduce ExecutionInstructionData
- introduce ExecutionEngine
- introduce ExecutionDriverContract
- refactor RedeemVoucher
- refactor x-change claim UI
- implement settlement-envelope driver
- implement stored-value driver
- change voucher-pipeline.php
```

---

# Output Requirements

The final document must be:

```text
docs/architecture/execution-engine/01-current-state.md
```

Use Markdown.

Use exact class names and file paths.

Use diagrams where helpful.

Mark uncertain findings clearly as:

```text
Observation requiring confirmation:
```

Do not guess silently.

---

# Quality Bar

This document is acceptable only if a future AI agent can read it and understand:

```text
what exists today,
what must not break,
where the dangerous seams are,
and what tests need to be added before the execution-engine refactor begins.
```

Do not make architecture changes while creating this document.

Run tests only if needed for discovery, but do not modify source code except to add the Markdown document.
