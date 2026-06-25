# 02-target-state.md

# x-journal — Target State Architecture

## Purpose

This document describes the desired end state of `3neti/x-journal`.

It defines:

- the long-term vision
- the architectural role of the package
- package boundaries
- ecosystem integration
- evidentiary model
- verification model
- visibility model
- recovery model

This document intentionally describes the destination rather than the implementation sequence.

Implementation sequencing is addressed separately in `03-evolution-plan.md`.

---

# Executive Summary

The target state of `x-journal` is to become the official evidentiary layer of the 3neti ecosystem.

Every significant business execution shall be capable of producing:

- an execution journal entry
- a human-auditable execution record
- a verifiable execution artifact
- a recoverable execution history

The journal becomes the canonical source of evidentiary truth.

---

# Core Philosophy

The ecosystem distinguishes between two forms of truth.

## Operational Truth

Operational truth exists inside:

- databases
- wallet ledgers
- voucher records
- claim records
- settlement records

Operational truth answers:

> What is true right now?

---

## Evidentiary Truth

Evidentiary truth exists inside:

- execution journal entries
- execution statements
- execution artifacts
- verification records

Evidentiary truth answers:

> What happened, who did it, when did it occur, and how can it be proven?

---

## Architectural Principle

```text
Database = Operational Truth

Execution Journal = Evidentiary Truth
```

Both are first-class concepts.

Neither replaces the other.

---

# Target Ecosystem Position

The target package ecosystem becomes:

```text
3neti/x-change
    Execution Platform

3neti/x-journal
    Evidentiary Platform

3neti/settlement-envelope
    Settlement Readiness Platform

3neti/x-ray
    Technical Observability Platform

3neti/x-rider
    Experience Platform
```

---

# Package Responsibilities

## x-change

Responsible for:

```text
execution
orchestration
workflow
wallets
claims
collections
disbursements
settlements
```

---

## x-journal

Responsible for:

```text
capture
normalization
persistence
verification
visibility
rendering
statement generation
recovery evidence
```

---

## settlement-envelope

Responsible for:

```text
readiness
evidence collection
document assembly
settlement packaging
```

---

## x-ray

Responsible for:

```text
technical logs
API traces
debugging
observability
```

---

# Target Execution Flow

Every meaningful business execution follows a common path.

```text
Execution Event
        ↓
Transformer
        ↓
Execution Journal Entry
        ↓
Visibility Governance
        ↓
Execution Artifact
        ↓
Verification
```

---

# Execution Event Layer

Execution events originate from:

```text
x-change
settlement-envelope
webhooks
jobs
provider callbacks
manual operations
future ecosystem packages
```

Examples:

```text
pay_code.issued
claim.submitted
kyc.approved
document.attached
settlement.ready
disbursement.completed
statement.closed
```

Execution events are machine-oriented.

They are not intended for direct human consumption.

---

# Transformation Layer

Every significant execution event is transformed into a canonical journal entry.

Purpose:

```text
normalize inputs
unify semantics
preserve evidence
reduce coupling
```

Transformers become the primary boundary between source systems and journal persistence.

---

# Journal Layer

The execution journal becomes the canonical evidentiary store.

Each journal entry receives:

```text
Execution Reference Number (ERN)
```

Examples:

```text
ERN-2026-000000001
ERN-2026-000000002
ERN-2026-000000003
```

The ERN becomes the universal reference throughout the ecosystem.

---

# Journal Entry Structure

Every journal entry contains:

```text
Identity
Actor
Subject
Execution
Evidence
Money
References
Integrity
```

Journal entries are:

```text
immutable
append-only
searchable
verifiable
renderable
```

---

# Artifact Architecture

Human users do not consume journal entries directly.

They consume artifacts.

```text
Execution Artifact
    ├─ Receipt
    ├─ Certificate
    ├─ Instrument
    ├─ Statement
    ├─ Timeline
    └─ Envelope
```

Artifacts are renderings.

Artifacts are not truth.

The journal remains the source of truth.

---

# Execution Receipt

Execution receipts represent completed transactional executions.

Examples:

```text
Wallet Funding Receipt
Claim Submission Receipt
Collection Receipt
Disbursement Receipt
```

Receipts answer:

> What execution occurred?

---

# Execution Certificate

Execution certificates represent completed verification, authorization, or compliance executions.

Examples:

```text
KYC Certificate
Authorization Certificate
Settlement Readiness Certificate
Medical Eligibility Certificate
```

Certificates answer:

> What condition was satisfied?

---

# Execution Instrument

Execution instruments represent claimable, redeemable, reservable, or entitlement-bearing executions.

Examples:

```text
Pay Code Certificate
Gift Check
Traveler's Check
Benefit Card
Transit Card
Escrow Certificate
Allowance Certificate
Treasury Warrant
```

Instruments answer:

> What right or entitlement was created?

---

# Execution Statement

Execution statements summarize groups of executions.

Examples:

```text
Wallet Statement
Issuer Statement
Program Statement
Settlement Statement
Regulatory Statement
```

Statements answer:

> What happened during a period?

---

# Execution Timeline

Execution timelines render a business journey chronologically.

Examples:

```text
Voucher Journey
Claim Journey
Settlement Journey
Disbursement Journey
```

Timelines answer:

> What sequence of executions occurred?

---

# Settlement Envelope Integration

Settlement envelopes become consumers of journal entries.

The envelope references:

```text
ERNs
Certificates
Receipts
Evidence
Statements
```

The envelope does not become the journal.

The envelope assembles journal-derived evidence.

---

# Visibility Architecture

Visibility becomes a first-class concept.

Every artifact is rendered through a visibility profile.

---

## Visibility Profiles

Examples:

```text
public_verify
redeemer_copy
issuer_copy
support_view
finance_view
compliance_view
regulator_view
internal_full
```

Visibility determines:

```text
visible fields
hidden fields
redacted values
available artifacts
```

---

# Visibility Governance

Visibility decisions belong to x-journal.

Principle:

```text
x-journal governs access

x-ray renders access
```

No external package should bypass visibility governance.

---

# Verification Architecture

Every public-facing artifact shall be verifiable.

Verification becomes a first-class feature.

---

## Verification Levels

### Level 1

```text
ERN
Verification URL
```

---

### Level 2

```text
Verification Token
```

---

### Level 3

```text
Artifact Hash
```

---

### Level 4

```text
Digital Signature
```

---

### Level 5

```text
Journal Chain Verification
```

---

### Level 6

```text
Statement Anchoring
```

---

### Level 7

```text
Settlement Envelope Anchoring
```

---

# Verification Principle

Verification should never depend upon trusting the artifact itself.

Verification should depend upon:

```text
Artifact
    ↓
ERN
    ↓
Execution Journal
```

The journal remains authoritative.

---

# Recovery Architecture

The journal becomes a recovery anchor.

The journal does not replace backups.

The journal supplements recovery.

---

## Recovery Layers

```text
Execution Events
        ↓
Execution Journal
        ↓
Execution Statements
        ↓
Recovery Anchors
```

---

## Recovery Objectives

Enable:

```text
timeline reconstruction
execution verification
statement reconciliation
forensic analysis
regulatory review
post-disaster recovery
```

---

# Statement Architecture

Statements become frozen snapshots of execution history.

Statements contain:

```text
opening position
activity summary
closing position
entries hash
statement hash
```

Statements become recovery checkpoints.

---

# Integrity Architecture

The journal shall support immutable verification.

Future capabilities include:

```text
entry hashing
journal chaining
statement chaining
tamper detection
archive verification
```

The goal is evidentiary confidence rather than blockchain-style consensus.

---

# Archive Architecture

Journal entries may be archived to:

```text
S3
R2
object storage
compliance archives
```

Archive copies remain secondary to the canonical journal.

Archives exist for:

```text
recovery
compliance
long-term retention
```

---

# Search Architecture

The target system supports:

```text
ERN search
voucher search
claim search
timeline search
correlation search
statement search
artifact search
```

Search engines may include:

```text
database
OpenSearch
Elasticsearch
MongoDB projections
```

The relational journal remains authoritative.

---

# Public Verification Architecture

The ecosystem supports public verification experiences.

Examples:

```text
Receipt Verification
Certificate Verification
Instrument Verification
Statement Verification
```

Public views expose only approved information.

Sensitive evidence remains protected.

---

# Role-Based Evidence Access

Different audiences receive different evidence views.

Examples:

```text
Developer
Support
Operations
Finance
Compliance
Regulator
Issuer
Redeemer
Public
```

The same journal entry may render differently for each audience.

---

# Regulatory Readiness

The target architecture supports:

```text
financial audits
government audits
program audits
compliance reviews
external investigations
```

without requiring custom reporting pipelines.

The journal becomes the central evidentiary layer.

---

# x-change as an Operating System

The long-term vision is:

```text
x-change
    = Execution Operating System
```

while:

```text
x-journal
    = Execution Memory
```

Every execution becomes:

```text
captured
normalized
persisted
verifiable
renderable
recoverable
```

through a unified journal architecture.

---

# Target State Conclusion

In the target state:

```text
Every Meaningful Execution
        ↓
Execution Journal Entry
        ↓
Execution Record
        ↓
Execution Artifact
        ↓
Verification
        ↓
Recovery
```

The ecosystem gains:

- evidentiary truth
- verification
- visibility governance
- artifact generation
- recovery anchors
- regulatory confidence
- public trust

while preserving the existing separation of concerns between execution, settlement, observability, and user experience.

The execution journal becomes the permanent memory of the 3neti ecosystem.
