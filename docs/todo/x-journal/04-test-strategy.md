# 04-test-strategy.md

# x-journal — Test Strategy

## Purpose

This document defines the testing philosophy, testing standards, coverage expectations, and testing architecture for `3neti/x-journal`.

The package occupies a foundational position within the 3neti ecosystem.

Failures in the journal may affect:

- auditability
- recoverability
- compliance
- financial reporting
- evidentiary trust
- regulatory confidence

For this reason, the package adopts a stricter testing posture than ordinary application packages.

---

# Testing Philosophy

The journal is the source of evidentiary truth.

Therefore:

```text id="8zdhc4"
Every journal entry must be trustworthy.

Every artifact must be reproducible.

Every verification result must be deterministic.
```

The package favors:

```text id="p7s61i"
correctness
determinism
traceability
immutability
```

over implementation convenience.

---

# Coverage Objective

Target:

```text id="m81c0d"
100% test coverage
```

for:

- services
- actions
- transformers
- DTOs
- renderers
- visibility rules
- verification services
- integrity services

Coverage is considered part of the architecture.

---

# Testing Framework

The package shall use:

```text id="ztczlg"
Pest
```

as the primary testing framework.

---

# Test Organization

Recommended structure:

```text id="4w3wzd"
tests/

Feature/
    Recording/
    Transformers/
    Visibility/
    Verification/
    Statements/
    Rendering/
    Recovery/

Unit/
    DTOs/
    Models/
    Generators/
    Hashing/
    Policies/
```

---

# Testing Style

The package follows:

```text id="gq7cz8"
Arrange
Act
Assert
```

for all tests.

Example:

```php id="w6a1oz"
it('records a journal entry', function () {

    // Arrange

    // Act

    // Assert

});
```

The testing style should remain consistent across all modules.

---

# Dataset Philosophy

The package should prefer datasets over repetitive tests.

Example:

```text id="1gx8w5"
pay_code.issued
claim.submitted
kyc.approved
disbursement.completed
```

should often be tested using shared datasets.

---

# Deterministic Testing

All tests must be deterministic.

Avoid:

```text id="8vktbz"
random IDs
random timestamps
uncontrolled UUIDs
uncontrolled clock access
```

Use:

```text id="k8n5u6"
fixed timestamps
frozen clocks
known UUIDs
known ERNs
```

---

# Phase-Based Testing Strategy

The testing approach evolves alongside the package.

---

# Phase 1 — Journal Foundation

## Goal

Validate journal persistence.

---

## Journal Recording Tests

Verify:

```text id="a6qqp9"
records journal entry
persists ERN
persists payload
persists actor
persists subject
persists references
```

---

## ERN Tests

Verify:

```text id="q04oqo"
generates ERN
increments sequence
resets sequence when configured
supports custom prefixes
```

---

## DTO Tests

Verify:

```text id="7mqsdg"
construction
serialization
array conversion
JSON conversion
```

---

## Database Sink Tests

Verify:

```text id="yl3nho"
writes entry
stores payload
stores references
stores timestamps
```

---

# Phase 2 — Transformer Testing

## Goal

Validate normalization.

---

## Transformer Tests

For each transformer:

```text id="0r9f5z"
input event
    ↓
journal entry
```

Verify:

```text id="9w31qr"
execution type
actor
subject
money
references
metadata
```

are correctly populated.

---

## Supported Transformers

Examples:

```text id="9h4n89"
PayCodeIssuedTransformer
ClaimSubmittedTransformer
KycApprovedTransformer
DisbursementCompletedTransformer
```

Each transformer should have dedicated coverage.

---

# Phase 3 — Sink Testing

## Goal

Validate multi-sink behavior.

---

## Sink Contract Tests

Verify:

```text id="hqxejl"
all sinks implement contract
```

---

## Database Sink

Verify:

```text id="1s5zpw"
writes correctly
```

---

## Monolog Sink

Verify:

```text id="o4dg1i"
creates structured records
contains ERN
contains execution type
```

---

## Object Storage Sink

Verify:

```text id="45sdjm"
path generation
JSON generation
archive writing
```

---

# Phase 4 — Integrity Testing

## Goal

Validate evidentiary trust.

---

## Hash Tests

Verify:

```text id="ztlm2v"
hash generation
hash stability
hash reproducibility
```

---

## Chain Tests

Verify:

```text id="yy0k5k"
previous hash linkage
chain validation
chain failure detection
```

---

## Tamper Detection Tests

Verify:

```text id="wxmbu5"
modified entry
broken hash
broken chain
```

are detected.

---

# Phase 5 — Idempotency Testing

## Goal

Prevent duplicate journal entries.

---

## Positive Tests

Verify:

```text id="hmv7d6"
same key
same payload
returns existing entry
```

---

## Conflict Tests

Verify:

```text id="qlsfrq"
same key
different payload
raises conflict
```

---

## Replay Tests

Verify:

```text id="v93dvl"
duplicate event
does not duplicate journal
```

---

# Phase 6 — Visibility Testing

## Goal

Validate evidence governance.

---

## Visibility Profile Tests

Verify:

```text id="q44khj"
public_verify
issuer_copy
redeemer_copy
finance_view
compliance_view
```

render correctly.

---

## Redaction Tests

Verify:

```text id="hcbx3k"
masked fields
hidden fields
visible fields
```

for each profile.

---

## Access Context Tests

Verify:

```text id="k5vwyl"
same journal entry
different audiences
different outputs
```

---

# Phase 7 — Artifact Rendering Tests

## Goal

Validate artifact generation.

---

## Receipt Tests

Verify:

```text id="m31a7f"
receipt rendering
receipt sections
receipt references
```

---

## Certificate Tests

Verify:

```text id="mdbrzw"
certificate rendering
certificate metadata
certificate evidence
```

---

## Instrument Tests

Verify:

```text id="j05e10"
gift check profile
traveler's check profile
benefit card profile
```

render correctly.

---

## Timeline Tests

Verify:

```text id="p30hnd"
chronological ordering
grouping logic
event rendering
```

---

# Phase 8 — Verification Testing

## Goal

Validate public trust mechanisms.

---

## Verification URL Tests

Verify:

```text id="lscicw"
valid artifact
invalid artifact
expired artifact
```

---

## Token Tests

Verify:

```text id="fw5g2p"
valid token
invalid token
missing token
```

---

## Hash Verification Tests

Verify:

```text id="u4ehwy"
matching artifact
modified artifact
```

---

# Phase 9 — Statement Testing

## Goal

Validate frozen snapshots.

---

## Statement Generation Tests

Verify:

```text id="h10jli"
opening balance
activity summary
closing balance
```

---

## Snapshot Tests

Verify:

```text id="qwecld"
frozen state
reproducibility
```

---

## Statement Hash Tests

Verify:

```text id="b3al3s"
entries hash
statement hash
```

remain stable.

---

# Phase 10 — Recovery Testing

## Goal

Validate reconstruction capabilities.

---

## Timeline Reconstruction

Verify:

```text id="7zfljj"
reconstruct execution history
```

from journal entries.

---

## Statement Reconciliation

Verify:

```text id="e4s7bx"
journal
vs
statement
```

consistency.

---

## Recovery Anchor Tests

Verify:

```text id="3s5zba"
statement anchor integrity
```

remains valid.

---

# Visibility Regression Suite

A dedicated regression suite shall exist.

Purpose:

Prevent accidental exposure of:

```text id="gv8j1m"
PII
KYC evidence
medical documents
sensitive references
```

Visibility regressions are considered critical defects.

---

# Verification Regression Suite

A dedicated regression suite shall exist.

Purpose:

Prevent accidental weakening of:

```text id="d7o14u"
verification URLs
verification tokens
artifact hashes
```

Verification regressions are considered critical defects.

---

# Integrity Regression Suite

A dedicated regression suite shall exist.

Purpose:

Prevent accidental weakening of:

```text id="stfbl0"
hash generation
hash validation
chain validation
```

Integrity regressions are considered critical defects.

---

# Contract Testing

Every public contract shall have dedicated tests.

Examples:

```text id="xbyi6p"
ExecutionJournalSink
ExecutionJournalTransformer
ExecutionArtifactRenderer
ExecutionVisibilityResolver
ExecutionVerificationService
```

The goal is preventing package evolution from breaking integrations.

---

# Integration Testing

The package shall include integration tests with representative ecosystem events.

Examples:

```text id="8s6n4g"
pay_code.issued
claim.submitted
kyc.approved
disbursement.completed
settlement.ready
```

The goal is validating realistic workflows.

---

# Performance Testing

Performance testing is secondary during early phases.

However:

```text id="s9h6l6"
recording
searching
statement generation
```

should have baseline benchmarks.

---

# Security Testing

Security tests should focus on:

```text id="p9m9ux"
visibility
redaction
verification
token validation
```

rather than infrastructure concerns.

---

# Mutation Testing

Future objective:

Introduce mutation testing for:

```text id="wqgok9"
integrity
verification
visibility
```

because these systems must be highly reliable.

---

# Continuous Integration Requirements

Every pull request must execute:

```text id="j5feeh"
unit tests
feature tests
integration tests
```

No pull request may reduce coverage.

---

# Failure Classification

The following are considered critical failures:

```text id="ep4u3s"
incorrect ERN
duplicate journal entry
hash mismatch
visibility leak
verification bypass
statement inconsistency
```

Critical failures block release.

---

# Architecture Validation

The test suite is expected to enforce architecture.

Tests should verify:

```text id="k3whn6"
append-only behavior
artifact derivation
visibility governance
verification integrity
```

rather than only implementation details.

---

# Test Strategy Conclusion

The journal is an evidentiary platform.

Therefore the test suite must validate:

```text id="qz13sh"
correctness
immutability
visibility
verification
recoverability
```

at every phase of the package lifecycle.

The objective is not merely working code.

The objective is trustworthy evidence.
