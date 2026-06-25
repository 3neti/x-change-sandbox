# Addendum — x-journal Compass Management

## Purpose

The `3neti/x-journal` implementation is expected to span multiple slices, multiple commits, and potentially multiple Codex sessions.

Because `x-journal` is intended to become the evidentiary layer of the 3neti ecosystem, architectural continuity is critical.

To prevent:

```text
architectural drift
scope creep
implementation drift
loss of evidentiary intent
loss of package memory
```

Codex must maintain a living Compass document throughout the package lifecycle.

The Compass becomes the operational source of truth for:

```text
where we are
what is completed
what remains
what decisions have been made
what risks exist
what future slices are expected
```

The Compass supplements:

```text
x-journal_functional_specifications.md
x-journal_functional_specifications_addendum.md

01-current-state.md
02-target-state.md
03-evolution-plan.md
04-test-strategy.md
05-architecture-invariants.md
```

Those documents are relatively stable.

The Compass is expected to evolve continuously.

---

# Compass Location

Create and maintain:

```text
docs/architecture/x-journal/X_JOURNAL_COMPASS.md
```

If architecture documentation lives elsewhere, place the Compass beside the architecture documents.

---

# Compass Update Frequency

Update the Compass whenever any of the following occur:

```text
A slice begins
A slice completes
A significant discovery is made
A new architectural decision is made
A new package boundary is clarified
A testing strategy changes
A roadmap changes
A future slice is re-scoped
A risk is identified
```

Minimum requirement:

```text
One Compass update per completed slice
```

---

# Required Compass Structure

## 1. Mission

A concise statement of the package mission.

Example:

```text
Provide the official evidentiary journal layer for the 3neti ecosystem.
```

---

## 2. Current Position

Example:

```text
Current Slice:
    Phase 1 — Core Journal Foundation

Status:
    In Progress

Last Updated:
    YYYY-MM-DD HH:MM
```

---

## 3. Phase Progress Table

Example:

| Phase | Name | Status |
|---------|---------|---------|
| 0 | Architectural Foundation | Completed |
| 1 | Core Journal Foundation | In Progress |
| 2 | Event Transformation Layer | Pending |
| 3 | Sink Architecture | Pending |
| 4 | Integrity and Idempotency | Pending |
| 5 | Visibility Governance | Pending |
| 6 | Artifact Rendering | Pending |
| 7 | Artifact Profiles | Pending |
| 8 | Verification Framework | Pending |
| 9 | Statement Engine | Pending |
| 10 | Timeline Engine | Pending |
| 11 | Settlement Envelope Integration | Pending |
| 12 | Recovery Anchors | Pending |
| 13 | Digital Signatures | Pending |
| 14 | Regulatory Exports | Pending |
| 15 | Public Trust Layer | Pending |

Status values:

```text
Pending
In Progress
Blocked
Completed
Deferred
```

---

## 4. Completed Work

Record:

```text
files created
contracts introduced
DTOs introduced
models introduced
migrations created
tests added
architecture decisions implemented
commits completed
```

Use concise bullet lists.

---

## 5. Current Discoveries

Capture discoveries that may affect future slices.

Examples:

```text
Existing x-change events already map naturally to journal entry types.

Settlement-envelope already behaves as a journal consumer rather than a journal owner.

Monolog sink should remain secondary to the canonical journal.

Visibility governance is broader than originally anticipated.
```

These discoveries must survive across Codex sessions.

---

## 6. Risks

Record active risks.

Examples:

```text
Journal schema may become over-normalized too early.

Visibility model may leak into x-change.

Artifact rendering may introduce package coupling.

Future verification requirements may affect persistence design.
```

Include mitigation strategies where practical.

---

## 7. Architectural Decisions

Record decisions that affect implementation.

Examples:

```text
RDBMS is the canonical journal store.

Monolog is a sink, not the journal.

Journal entries are append-only.

Artifacts are renderings, not truth.

Settlement envelopes consume journal entries.

Visibility belongs to x-journal.

Verification is journal-driven.

Flexible evidence remains JSON-first.
```

This becomes the package ADR log.

---

## 8. Test Coverage Status

Track:

```text
unit coverage
feature coverage
contract coverage
integration coverage
architecture invariant coverage
```

Example:

```text
Journal Foundation Coverage:
    High

Contract Coverage:
    Partial

Architecture Invariant Tests:
    Not Started
```

Reasonable estimates are acceptable.

---

## 9. Phase Deliverables Status

Track major deliverables.

Example:

```text
ExecutionJournalEntry
    Completed

ExecutionJournalEntryData
    Completed

DatabaseJournalSink
    In Progress

ExecutionJournalRecorder
    Pending
```

This provides a quick implementation snapshot.

---

## 10. Next Recommended Slice

Example:

```text
Recommended Next Slice:
    Phase 2 — Event Transformation Layer

Reason:
    Core journal persistence is complete and tests are green.
```

This allows future sessions to resume immediately.

---

## 11. Open Questions

Track unresolved decisions.

Examples:

```text
Should ERN sequences be globally unique or yearly segmented?

Should artifact rendering remain package-native or adapter-driven?

Should verification tokens be persisted or generated dynamically?

Should visibility matrices live entirely in configuration or database-backed maintenance tables?
```

These become future review points.

---

# Required Reporting Behavior

Whenever a phase completes, include a Compass summary.

Example:

```text
Compass Update

Current Phase:
    Phase 1 Complete

Completed:
    Package skeleton
    Config
    Migration
    ExecutionJournalEntry
    ERN Generator

Risks:
    None currently identified.

Next Phase:
    Event Transformation Layer
```

---

# Compass Preservation Rule

Before beginning work in a new Codex session:

```text
1. Read X_JOURNAL_COMPASS.md
2. Read x-journal_functional_specifications.md
3. Read x-journal_functional_specifications_addendum.md
4. Read:
    01-current-state.md
    02-target-state.md
    03-evolution-plan.md
    04-test-strategy.md
    05-architecture-invariants.md
5. Reconcile differences
6. Continue from the latest recorded position
```

Do not rely solely on conversation history.

The Compass is the persisted memory of the package.

---

# Compass Quality Bar

A future AI agent with no prior conversation history should be able to open:

```text
X_JOURNAL_COMPASS.md
```

and immediately understand:

```text
what x-journal is,
why it exists,
where implementation currently stands,
what has been completed,
what remains,
what risks exist,
what architectural decisions have been made,
and what should happen next.
```

---

# Additional Requirement — Architectural Alignment Check

At the end of every completed phase, Codex must verify that implementation remains aligned with:

```text
02-target-state.md
05-architecture-invariants.md
```

and record any deviations in the Compass.

If implementation diverges from architecture:

```text
Record it.
Explain it.
Do not silently drift.
```

---

# Additional Requirement — Package Identity Check

The Compass must continuously reinforce the package identity:

```text
x-change
    = Execution Operating System

x-journal
    = Execution Memory
```

The package must not gradually devolve into:

```text
a logging package
a reporting package
a PDF package
a compliance package
```

Those are downstream capabilities.

The primary identity remains:

```text
The Evidentiary Layer of the 3neti Ecosystem.
```
