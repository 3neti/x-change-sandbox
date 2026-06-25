# 01-current-state.md

# x-journal — Current State Assessment

## Purpose

This document describes the current state of the 3neti ecosystem prior to the introduction of `3neti/x-journal`.

The goal is to establish:

- what currently exists
- where execution evidence is currently stored
- what gaps exist
- why a dedicated execution journal package is necessary

This document intentionally describes reality before implementation of `x-journal`.

---

# Executive Summary

The 3neti ecosystem currently contains multiple sources of operational truth but does not contain a unified source of evidentiary truth.

Applications can determine what happened by inspecting:

- database records
- voucher records
- wallet transactions
- settlement envelope records
- application logs
- Monolog output
- provider callbacks
- audit tables

However, there is no single business-oriented execution journal capable of answering:

> What execution occurred, when did it occur, who initiated it, what evidence existed, and what official record proves it occurred?

As the ecosystem evolves toward financial, settlement, compliance, and public-sector workflows, the absence of a unified evidentiary journal becomes increasingly significant.

---

# Current Ecosystem

The current ecosystem consists of several packages with clearly defined responsibilities.

```text
3neti/x-change
    Execution Platform

3neti/settlement-envelope
    Settlement Readiness Layer

3neti/x-ray
    Technical Observability

3neti/x-rider
    Experience Layer
```

Each package solves a different problem.

None currently serves as the authoritative evidentiary journal.

---

# Current State of x-change

`x-change` currently functions as the execution platform.

It orchestrates:

- wallet funding
- voucher issuance
- claim journeys
- redemption workflows
- disbursement workflows
- collection workflows
- reconciliation workflows

The platform already emits many business-significant events.

Examples:

```text
wallet funded
pay code issued
claim started
claim submitted
kyc approved
otp approved
document attached
disbursement completed
collection completed
statement closed
```

These events are currently persisted indirectly through various domain records.

Examples:

```text
wallet transactions
voucher tables
claim records
redemption metadata
provider transaction tables
```

The system can reconstruct many business journeys.

However, reconstruction is not standardized.

---

# Current State of Settlement Envelope

The settlement envelope provides a structured package for settlement readiness.

It captures:

- evidence
- supporting documents
- readiness evaluations
- settlement metadata

Examples:

```text
LOA
medical certificate
KYC evidence
supporting documentation
settlement package exports
```

Settlement envelopes provide excellent settlement-focused evidence.

However:

```text
settlement envelope != execution journal
```

A settlement envelope answers:

> Is this settlement ready?

It does not answer:

> What are all executions that occurred across the platform?

---

# Current State of x-ray

`x-ray` provides technical observability.

Examples:

```text
request logs
response logs
API traces
integration diagnostics
debugging information
```

This information is useful for:

- developers
- support engineers
- troubleshooting

However:

```text
technical observability != evidentiary observability
```

Technical logs are optimized for debugging.

They are not optimized for:

- auditors
- regulators
- finance officers
- compliance officers
- courts
- public verification

---

# Current State of Logging

The ecosystem currently relies primarily upon:

```text
Monolog
Laravel Logs
Application Logs
Provider Logs
Webhook Logs
```

These logs are valuable but have limitations.

Problems include:

```text
high noise
technical formatting
limited business context
limited lifecycle visibility
limited verification capability
```

A log entry may indicate:

```text
pay_code.generate.succeeded
```

but may not provide a complete business record suitable for:

- auditors
- finance teams
- regulators
- beneficiaries

---

# Current State of Auditability

The ecosystem currently supports auditability through:

```text
database records
audit tables
wallet transactions
voucher records
settlement artifacts
provider references
```

Auditability exists.

However, it is distributed.

An auditor frequently needs to traverse multiple systems to reconstruct a business journey.

Example:

```text
Voucher Table
    ↓
Wallet Ledger
    ↓
Claim Metadata
    ↓
Settlement Envelope
    ↓
Provider Transaction
```

The resulting audit trail is fragmented.

---

# Current State of Human Artifacts

The ecosystem currently produces limited human-facing artifacts.

Examples:

```text
voucher codes
claim confirmations
provider confirmations
settlement documents
```

However, there is no standardized artifact model.

The system lacks first-class support for:

```text
Execution Receipts
Execution Certificates
Execution Instruments
Execution Statements
Execution Timelines
```

As a result, each workflow tends to create its own bespoke output.

---

# Current State of Verification

Verification currently relies on:

```text
database lookup
provider references
voucher lookup
claim lookup
```

There is no generalized verification framework.

Missing capabilities include:

```text
verification URLs
verification tokens
receipt fingerprints
artifact hashes
statement anchors
journal chain verification
```

Public verification experiences are therefore limited.

---

# Current State of Visibility Governance

Visibility decisions are currently application-driven.

There is no unified visibility model.

Questions such as:

```text
Can Support see this?
Can Finance see this?
Can Compliance see this?
Can the Public verify this?
```

are answered inconsistently depending on the workflow.

There is currently no concept of:

```text
Visibility Profile
Artifact Policy
Verification Policy
Redaction Policy
```

---

# Current State of Recovery

Recovery currently relies primarily upon:

```text
database backups
application backups
provider records
wallet ledger records
```

The ecosystem lacks a dedicated recovery-oriented journal.

There is currently no concept of:

```text
Execution Statement
Recovery Anchor
Journal Chain
Statement Hash
Statement Reconciliation
```

As transaction volume grows, recovery confidence increasingly depends on these capabilities.

---

# Current State of Regulatory Readiness

The ecosystem already contains many of the necessary ingredients for regulatory reporting:

```text
wallet transactions
voucher records
claim records
settlement records
provider references
```

However, there is no dedicated layer designed specifically for:

```text
regulatory review
financial review
external audit
compliance reporting
```

Evidence remains operationally distributed.

---

# Identified Gap

The ecosystem currently lacks a package whose sole responsibility is:

```text
Capture
Normalize
Persist
Verify
Render
Govern
Export
```

business-significant executions.

This gap affects:

- auditability
- recoverability
- verification
- compliance
- public trust
- artifact generation
- regulatory confidence

---

# Why x-journal Exists

`x-journal` exists to become the evidentiary layer of the ecosystem.

Its purpose is to transform:

```text
Operational Truth
```

into:

```text
Evidentiary Truth
```

by creating a unified execution journal capable of producing:

```text
Execution Records
Execution Receipts
Execution Certificates
Execution Instruments
Execution Statements
Execution Timelines
Settlement Evidence
```

from a single canonical source.

---

# Current State Conclusion

Today:

```text
Operational Truth Exists
```

through:

- databases
- ledgers
- vouchers
- claims
- settlements
- logs

However:

```text
Evidentiary Truth Is Fragmented
```

The ecosystem lacks a dedicated journal layer that can:

- unify execution evidence
- govern visibility
- support verification
- produce human artifacts
- support recovery
- support compliance
- support regulatory confidence

`3neti/x-journal` is intended to become that layer.
