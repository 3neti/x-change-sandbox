# 03-evolution-plan.md

# x-journal — Evolution Plan

## Purpose

This document defines the implementation roadmap for `3neti/x-journal`.

The goal is to:

- establish a safe implementation sequence
- minimize architectural risk
- maximize early value
- avoid premature complexity
- preserve long-term flexibility

The target state is described in `02-target-state.md`.

This document describes how the package evolves from:

```text
No Package
```

to

```text
Full Evidentiary Platform
```

through incremental delivery slices.

---

# Guiding Principles

The package should evolve in layers.

We do not begin with:

- PDFs
- signatures
- public portals
- statements
- compliance exports

We begin with:

```text
Execution Journal
```

because every future capability depends on it.

---

# Evolution Philosophy

The implementation sequence follows:

```text
Capture
    ↓
Normalize
    ↓
Persist
    ↓
Govern
    ↓
Render
    ↓
Verify
    ↓
Recover
```

Each stage builds upon previous stages.

---

# Phase 0 — Architectural Foundation

## Objective

Freeze architecture before code generation.

---

## Deliverables

Persist:

```text
x-journal_functional_specifications.md

01-current-state.md
02-target-state.md
03-evolution-plan.md
04-test-strategy.md
05-architecture-invariants.md
```

---

## Success Criteria

Architecture documents are approved.

No package code exists yet.

---

# Phase 1 — Core Journal Foundation

## Objective

Establish the canonical execution journal.

---

## Deliverables

Package skeleton:

```text
3neti/x-journal
```

---

Core model:

```text
ExecutionJournalEntry
```

---

Migration:

```text
execution_journal_entries
```

---

Core DTOs:

```text
ExecutionJournalEntryData
ExecutionActorData
ExecutionSubjectData
ExecutionMoneyData
ExecutionReferenceData
ExecutionIntegrityData
```

---

Core services:

```text
ExecutionJournalRecorder
ExecutionReferenceNumberGenerator
```

---

Default sink:

```text
DatabaseJournalSink
```

---

ERN generation:

```text
ERN-2026-000000001
```

---

## Deferred

Not included:

```text
renderers
statements
verification
visibility
PDF
public URLs
```

---

## Success Criteria

The package can record immutable journal entries.

---

# Phase 2 — Event Transformation Layer

## Objective

Normalize business events into journal entries.

---

## Deliverables

Transformer contract:

```text
ExecutionJournalTransformer
```

---

Initial transformers:

```text
PayCodeIssuedTransformer
ClaimSubmittedTransformer
ClaimCompletedTransformer
KycApprovedTransformer
AuthorizationApprovedTransformer
DisbursementCompletedTransformer
DocumentAttachedTransformer
SettlementReadyTransformer
```

---

Registration mechanism:

```text
event → transformer → journal entry
```

---

## Success Criteria

Applications can emit events without understanding journal internals.

---

# Phase 3 — Sink Architecture

## Objective

Support multiple persistence targets.

---

## Deliverables

Sink contract:

```text
ExecutionJournalSink
```

---

Initial sinks:

```text
DatabaseJournalSink
MonologJournalSink
ObjectStorageJournalSink
NullJournalSink
```

---

Configuration-driven sink enablement.

---

## Success Criteria

One execution can be persisted to multiple destinations.

---

# Phase 4 — Integrity and Idempotency

## Objective

Introduce evidentiary reliability.

---

## Deliverables

Entry hashing:

```text
hash
```

---

Optional chain support:

```text
previous_hash
```

---

Idempotency support:

```text
idempotency_key
```

---

Verification command:

```text
journal:verify-integrity
```

---

## Deferred

No digital signatures yet.

No statement chaining yet.

---

## Success Criteria

Duplicate executions can be detected.

Tampering can be detected.

---

# Phase 5 — Visibility Governance

## Objective

Establish evidence access control.

---

## Deliverables

Visibility profile model:

```text
ExecutionVisibilityProfile
```

---

Visibility resolver:

```text
ExecutionVisibilityResolver
```

---

Redaction service:

```text
ExecutionRedactionService
```

---

Initial profiles:

```text
public_verify
redeemer_copy
issuer_copy
support_view
finance_view
compliance_view
internal_full
```

---

## Success Criteria

The same journal entry can render differently depending on audience.

---

# Phase 6 — Artifact Rendering

## Objective

Introduce human-readable evidence.

---

## Deliverables

Artifact contract:

```text
ExecutionArtifactRenderer
```

---

Supported artifact types:

```text
receipt
certificate
instrument
timeline
```

---

Supported formats:

```text
array
json
markdown
html
text
```

---

## Deferred

PDF generation.

---

## Success Criteria

Journal entries can produce human-readable artifacts.

---

# Phase 7 — Artifact Profiles

## Objective

Support specialized renderings.

---

## Deliverables

Artifact profile registry.

---

Initial profiles:

```text
STANDARD_RECEIPT
STANDARD_CERTIFICATE

GIFT_CHECK
TRAVELERS_CHECK

BENEFIT_CARD
TRANSIT_CARD

ESCROW_CERTIFICATE
TREASURY_WARRANT

ALLOWANCE_CERTIFICATE
```

---

Example:

```text
pay_code.issued
```

can render as:

```text
Gift Check
```

or

```text
Traveler's Check
```

depending on issuer configuration.

---

## Success Criteria

Visual identity becomes configurable.

---

# Phase 8 — Verification Framework

## Objective

Introduce artifact verification.

---

## Deliverables

Verification service.

---

Verification metadata:

```text
ERN
Verification Token
Verification URL
Artifact Hash
```

---

Verification endpoint support.

---

Public verification renderer.

---

## Verification Level

Target:

```text
Level 1
Level 2
Level 3
```

Only.

---

## Deferred

Digital signatures.

Statement anchoring.

Journal chaining verification UI.

---

## Success Criteria

Public artifacts can be independently verified.

---

# Phase 9 — Statement Engine

## Objective

Introduce frozen evidentiary snapshots.

---

## Deliverables

Model:

```text
ExecutionStatementSnapshot
```

---

Table:

```text
execution_statement_snapshots
```

---

Generators:

```text
Wallet Statement
Issuer Statement
Program Statement
Settlement Statement
```

---

Statement hash support.

---

## Success Criteria

Execution history can be frozen into auditable statements.

---

# Phase 10 — Timeline Engine

## Objective

Support investigation and support workflows.

---

## Deliverables

Timeline generation.

---

Grouping support:

```text
voucher
claim
issuer
wallet
beneficiary
correlation_id
```

---

Timeline rendering.

---

## Success Criteria

Complex execution journeys can be reconstructed visually.

---

# Phase 11 — Settlement Envelope Integration

## Objective

Connect evidentiary and settlement systems.

---

## Deliverables

Settlement envelopes reference:

```text
ERNs
Certificates
Receipts
Statements
```

---

Envelope assembly helpers.

---

## Success Criteria

Settlement evidence becomes journal-driven.

---

# Phase 12 — Recovery Anchors

## Objective

Improve disaster recovery confidence.

---

## Deliverables

Statement anchoring.

---

Recovery snapshots.

---

Cross-check commands:

```text
journal vs statements
journal vs wallets
journal vs settlements
```

---

## Success Criteria

Recovery confidence improves beyond backups alone.

---

# Phase 13 — Digital Signatures

## Objective

Introduce cryptographic authenticity.

---

## Deliverables

Artifact signatures.

---

Signature metadata:

```text
signed_at
signature_hash
signature_provider
```

---

Verification support.

---

## Success Criteria

Artifacts can prove authenticity.

---

# Phase 14 — Regulatory Exports

## Objective

Support compliance workflows.

---

## Deliverables

Export formats:

```text
JSON
CSV
PDF
```

---

Regulatory statement generation.

---

Audit package generation.

---

## Success Criteria

Regulatory evidence can be produced from the journal.

---

# Phase 15 — Public Trust Layer

## Objective

Make x-journal externally trustworthy.

---

## Deliverables

Verification portal.

---

Receipt verification.

---

Certificate verification.

---

Instrument verification.

---

Statement verification.

---

Settlement verification.

---

## Success Criteria

Third parties can verify artifacts without accessing internal systems.

---

# Integration Roadmap

## First Integration

```text
x-change
```

Initial events:

```text
pay_code.issued
claim.submitted
disbursement.completed
```

---

## Second Integration

```text
settlement-envelope
```

Initial events:

```text
envelope.created
evidence.attached
settlement.ready
```

---

## Third Integration

```text
future packages
```

Examples:

```text
onboarding
identity
track-ai
future provider packages
```

---

# Out-of-Scope for V1

The following should not block initial release.

```text
Blockchain
Distributed Consensus
Multi-region Replication
PKI Infrastructure
External Notarization
Government Integration
Complex Workflow Engines
```

The package should remain simple initially.

---

# Recommended First Codex Slice

Codex should be authorized to build only:

```text
Phase 1
```

specifically:

```text
Package Skeleton

Config

Migration

ExecutionJournalEntry Model

ExecutionJournalEntryData

ERN Generator

DatabaseJournalSink

ExecutionJournalRecorder

Tests
```

No visibility.

No renderers.

No statements.

No verification.

No PDFs.

No public endpoints.

The objective is to establish the canonical journal before building anything on top of it.

---

# Evolution Plan Conclusion

The package evolves through fifteen phases.

Each phase introduces a new capability while preserving the central principle:

```text
Every Meaningful Execution
        ↓
Execution Journal Entry
        ↓
Evidentiary Truth
```

The journal is established first.

Everything else is built on top of it.
