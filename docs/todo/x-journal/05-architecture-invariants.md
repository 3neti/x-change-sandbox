# 05-architecture-invariants.md

# x-journal — Architecture Invariants

## Purpose

This document defines the non-negotiable architectural rules for `3neti/x-journal`.

These invariants exist to prevent architectural drift as the package evolves.

Future implementation decisions, Codex instructions, refactors, integrations, and feature additions must preserve these rules.

---

# 1. x-journal is the evidentiary layer

`x-journal` exists to preserve evidentiary truth.

It does not replace:

- operational databases
- wallet ledgers
- accounting ledgers
- provider records
- settlement envelopes
- technical logs

It provides a curated, human-auditable, machine-verifiable record of business executions.

---

# 2. The database remains operational truth

Operational systems continue to operate from their normal database records.

Examples:

- vouchers
- claims
- wallets
- settlements
- provider transactions

The journal is not the runtime source of state.

---

# 3. The journal is evidentiary truth

The journal answers:

> What happened, when, by whom, under what authority, with what evidence, and with what result?

The journal exists for:

- humans
- auditors
- regulators
- finance teams
- compliance teams
- issuers
- redeemers
- recovery teams

---

# 4. Every journal entry must have an ERN

Every execution journal entry must have an Execution Reference Number.

Example:

```text
ERN-2026-000000001
```

No journal entry should exist without an ERN.

---

# 5. Journal entries are append-only

Journal entries must not be mutated after creation.

Corrections, reversals, amendments, annotations, and cancellations must be recorded as new journal entries.

---

# 6. Corrections are new executions

A correction is not an update.

A correction is a new execution that references the original execution.

---

# 7. Reversals are new executions

A reversal is not deletion.

A reversal is a new execution that references the original execution.

---

# 8. Monolog is a sink, not the journal

Monolog may receive structured journal records.

However, Monolog is not the canonical execution journal.

The canonical store is the journal persistence layer.

---

# 9. RDBMS is the default canonical store

The default canonical store is an append-only relational database table.

Supported default databases:

- MySQL
- PostgreSQL
- SQLite for testing

MongoDB, OpenSearch, Elasticsearch, SIEM, and object storage are optional projections or sinks.

---

# 10. Object storage is an archive, not the primary journal

Object storage may preserve immutable JSON copies.

It is not the primary source of truth unless explicitly configured by the host application.

---

# 11. Search stores are projections

Search indexes exist for querying and discovery.

They do not define truth.

---

# 12. SIEM integrations are sinks

SIEM systems may receive journal events.

They do not own the execution journal.

---

# 13. Transformers normalize before persistence

Source events should be transformed into canonical journal entries before persistence.

Raw system events must not dictate journal schema.

---

# 14. Not every system log becomes a journal entry

Only business-significant executions become journal entries.

Debug logs, cache events, low-level traces, and noisy technical events should remain in observability systems.

---

# 15. x-ray never authorizes journal visibility

`x-ray` may render or observe journal data.

It must not decide whether a user is allowed to see journal data.

Visibility decisions belong to `x-journal` policies, with business role context supplied by host applications such as `x-change`.

---

# 16. x-journal governs evidence visibility

Visibility governance is a first-class responsibility of `x-journal`.

Every rendered artifact or journal view must pass through a visibility profile.

---

# 17. Developers do not automatically receive full access

Developer access is not equivalent to full evidentiary access.

Developer and support views must be redacted by default.

---

# 18. Finance does not automatically receive full evidence

Finance users may see amounts, balances, statements, and settlement summaries.

They should not automatically see raw KYC payloads, medical documents, ID images, selfies, or sensitive evidence.

---

# 19. Compliance access must be auditable

Compliance and regulator-level access may expose full evidence.

Such access should be logged, auditable, and ideally reason-required.

---

# 20. Public verification is minimal by default

Public verification must expose only the minimum necessary information.

It should confirm validity without disclosing sensitive evidence.

---

# 21. Every public artifact must be verifiable

If an artifact is publicly shareable, it must support verification.

At minimum:

```text
ERN
Verification URL
Verification Token or Artifact Hash
```

---

# 22. The artifact is not the truth

Receipts, certificates, instruments, statements, timelines, and envelopes are renderings.

The journal entry remains the source of truth.

---

# 23. Artifacts must trace back to ERNs

Every artifact must reference one or more ERNs.

No artifact should be orphaned from the journal.

---

# 24. Execution receipts are renderings

An execution receipt is a receipt-style rendering of a journal entry.

It must not introduce independent business truth.

---

# 25. Execution certificates are renderings

An execution certificate is a certificate-style rendering of a journal entry.

It must not introduce independent business truth.

---

# 26. Execution instruments are first-class artifacts

Voucher issuance may create an instrument-style artifact.

Examples:

- Pay Code Certificate
- Gift Check
- Traveler's Check
- Benefit Card
- Transit Card
- Escrow Certificate
- Allowance Certificate

These are renderings of journal entries, not separate truth sources.

---

# 27. Statements are frozen snapshots

Statements are not live reports.

A statement is a frozen snapshot of journal entries for a period, subject, wallet, issuer, program, or settlement scope.

---

# 28. Statements may serve as recovery anchors

Statements should be usable for:

- reconciliation
- recovery
- audit review
- regulator review

They do not replace database backups.

---

# 29. The journal supports recovery but does not replace backups

The journal helps reconstruct business truth.

It does not replace:

- database backups
- object storage backups
- provider records
- infrastructure disaster recovery

---

# 30. Idempotency is mandatory for money-sensitive executions

Money-sensitive entries must support idempotency.

Examples:

- Pay Code issuance
- claim submission
- disbursement completion
- collection completion
- reconciliation
- statement close

---

# 31. Same idempotency key and same payload returns existing entry

Duplicate submissions with identical payloads should not create duplicate entries.

---

# 32. Same idempotency key and different payload is a conflict

A changed payload under the same idempotency key must be treated as a conflict.

---

# 33. Correlation IDs group journeys

Related entries should be grouped through correlation IDs.

Examples:

- voucher journey
- claim journey
- settlement journey
- disbursement journey

---

# 34. Causation IDs explain sequence

Causation IDs should identify which event or entry caused the next entry.

---

# 35. Journal entries must be serializable

Every journal entry must be serializable into canonical JSON.

This supports:

- storage
- hashing
- archive
- verification
- exports
- replay
- recovery

---

# 36. Stable structures may use DTO casts

Stable structures may be represented through DTOs and casters.

Examples:

- actor
- subject
- money
- references
- integrity

---

# 37. Flexible evidence remains JSON-first

Evidence, payload, and metadata should remain flexible JSON structures.

Do not over-model every evidence shape too early.

---

# 38. Renderers must respect visibility

Renderers must not read directly from raw journal data without applying visibility profiles.

---

# 39. Redaction must happen before rendering

Sensitive fields must be removed or masked before the artifact is rendered.

---

# 40. Verification must not expose hidden data

Verification confirms authenticity and validity.

It must not become a bypass for access control.

---

# 41. Settlement envelopes consume journal entries

The settlement envelope may include journal entries, receipts, certificates, statements, and timelines.

It does not own or replace the journal.

---

# 42. x-change emits into x-journal

`x-change` should emit business-significant executions into `x-journal`.

`x-change` should not duplicate journal logic.

---

# 43. x-journal must remain useful outside x-change

The package should not depend on x-change-specific models or terminology.

Integrations should be adapter-driven.

---

# 44. Package-first, UI-second

The package must expose services, DTOs, contracts, actions, and models first.

UI may be added later or supplied by host applications.

---

# 45. PDF generation is optional

PDF rendering should not be required for the core package.

PDF may be implemented through optional adapters or host applications.

---

# 46. Electronic signatures are optional

Digital signatures and electronic signing workflows should be optional extensions.

The core package should store signature metadata and verification data, not require a signing provider.

---

# 47. Artifact profiles determine presentation only

Artifact profiles control visual and semantic presentation.

They do not alter the underlying journal entry.

---

# 48. Capture points must be registered

Business-significant capture points should be registered or declared.

Examples:

```text
pay_code.issued
claim.started
claim.completed
claim.submitted
kyc.approved
authorization.approved
document.attached
settlement.ready
disbursement.requested
disbursement.completed
collection.completed
reconciliation.completed
statement.closed
```

---

# 49. Capture point visibility must be governed

Each capture point should have configurable visibility rules.

---

# 50. Maintenance configuration must not bypass policy

Administrative configuration may define visibility and artifact behavior.

It must not allow unsafe exposure without explicit configuration.

---

# 51. Tests enforce invariants

The test suite must enforce architecture.

Important invariants must be covered by tests.

Examples:

- append-only behavior
- idempotency behavior
- visibility redaction
- public verification limits
- hash stability
- artifact traceability

---

# 52. Visibility leaks are critical defects

Any accidental exposure of sensitive information is a critical failure.

---

# 53. Verification bypasses are critical defects

Any path that allows invalid artifacts to verify as valid is a critical failure.

---

# 54. Duplicate financial journal entries are critical defects

Duplicate money-sensitive journal entries are critical failures.

---

# 55. Broken integrity chains are critical defects

If hash or chain validation fails unexpectedly, the system must surface it clearly.

---

# 56. The package evolves by slices

Implementation must proceed incrementally.

Do not build statements, public verification, artifacts, visibility, and signatures before the core journal exists.

---

# 57. The first slice is the canonical journal

The first implementation slice must focus on:

- package skeleton
- config
- migration
- model
- DTO
- ERN generation
- database sink
- recorder
- tests

---

# 58. Future features must not weaken the core journal

Every new feature must preserve:

- append-only entries
- ERN identity
- canonical JSON
- visibility governance
- artifact traceability
- idempotency
- integrity

---

# 59. x-journal is not a workflow engine

`x-journal` records executions.

It does not decide business workflows.

Workflow decisions belong to `x-change`, settlement-envelope, or host applications.

---

# 60. x-journal is not an accounting ledger

The journal may support statements and reconciliation.

However, it is not a replacement for wallet ledgers or formal accounting ledgers.

---

# 61. x-journal is not merely a log package

The journal is curated, business-significant, human-auditable, and machine-verifiable.

It is not a generic technical logging wrapper.

---

# Final Invariant

The central invariant of `x-journal` is:

```text
Every meaningful business execution should be capable of becoming an immutable, traceable, visible-by-policy, verifiable execution journal entry from which human artifacts can be safely rendered.
```
