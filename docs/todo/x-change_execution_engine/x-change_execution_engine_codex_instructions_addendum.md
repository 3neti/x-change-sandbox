# Addendum — Execution Engine Compass Management

## Purpose

The Execution Engine migration is expected to span multiple slices, multiple commits, and potentially multiple Codex sessions.

To prevent architectural drift, implementation drift, and loss of context, Codex must maintain a living Compass document throughout the migration.

The Compass is the operational source of truth for:

```text
where we are
what is completed
what remains
what changed
what risks exist
what decision points exist
```

The Compass supplements:

```text
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
docs/architecture/execution-engine/EXECUTION_ENGINE_COMPASS.md
```

If the architecture documentation lives elsewhere, place the Compass beside the architecture documents.

---

# Compass Update Frequency

Update the Compass whenever any of the following occur:

```text
A slice begins
A slice completes
A significant discovery is made
A new risk is identified
A new architectural decision is made
A test strategy changes
A migration plan changes
A package boundary changes
```

At minimum:

```text
One Compass update per completed slice
```

---

# Required Compass Structure

## 1. Mission

A concise statement of the migration goal.

Example:

```text
Evolve voucher redemption into a programmable execution runtime while preserving existing behavior.
```

---

## 2. Current Position

Example:

```text
Current Slice:
    Slice 0 — Characterization Baseline

Status:
    In Progress

Last Updated:
    YYYY-MM-DD HH:MM
```

---

## 3. Slice Progress Table

Example:

| Slice | Name | Status |
|---------|---------|---------|
| 0 | Characterization Baseline | In Progress |
| 1 | Contract Extraction | Pending |
| 2 | Execution Instruction Introduction | Pending |
| 3 | Execution Engine Introduction | Pending |
| 4 | Default Driver Extraction | Pending |
| 5 | Driver Registry | Pending |
| 6 | Architecture Stabilization | Pending |
| 7 | Settlement Envelope Driver | Pending |
| 8 | Stored Value Driver | Pending |
| 9 | Driver-Composed Runtime | Pending |

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
tests added
files created
architecture decisions implemented
commits made
```

Prefer concise bullet lists.

---

## 5. Current Discoveries

Capture important findings discovered during inspection.

Examples:

```text
x-change directly imports RedeemVoucher in N locations
voucher-pipeline.php already behaves like a default execution pipeline
claim submit behavior differs from documentation
```

These discoveries should survive across Codex sessions.

---

## 6. Risks

Record active risks.

Examples:

```text
RedeemVoucher tightly coupled to disbursement
Existing tests do not fully protect withdraw branching
Provider integration lacks characterization coverage
```

Include mitigation plans where possible.

---

## 7. Architectural Decisions

Record decisions that affect implementation.

Examples:

```text
Execution architecture belongs primarily to voucher.
x-change consumes voucher contracts.
Settlement envelope remains a gate, not the execution engine.
DefaultExecutionDriver must preserve legacy behavior.
```

This section becomes the operational ADR log for the migration.

---

## 8. Test Coverage Status

Track:

```text
characterization tests
contract tests
architecture tests
feature tests
```

Example:

```text
Characterization Coverage:
    70%

Contract Coverage:
    Not Started

Architecture Invariant Tests:
    Not Started
```

Values do not need to be exact percentages.

Reasonable estimates are acceptable.

---

## 9. Next Recommended Slice

Example:

```text
Recommended Next Slice:
    Slice 1 — Contract Extraction

Reason:
    Characterization coverage is sufficient and all tests are green.
```

This allows a future Codex session to resume quickly.

---

## 10. Open Questions

Track unresolved architectural questions.

Examples:

```text
Should ExecutionDriverRegistry be package-extensible?
Should execution instructions be versioned?
Should stored-value spending be implemented as a driver or sub-driver?
```

These become decision points for future review.

---

# Required Reporting Behavior

Whenever a slice completes, include a Compass summary in the status report.

Example:

```text
Compass Update

Current Slice:
    Slice 0 Complete

Completed:
    Added characterization coverage for redemption pipeline.

Risks:
    Redeem vs withdraw branching still requires deeper coverage.

Next Slice:
    Contract Extraction
```

---

# Compass Preservation Rule

Before beginning work in a new Codex session:

```text
1. Read EXECUTION_ENGINE_COMPASS.md
2. Read the five architecture documents
3. Reconcile differences
4. Continue from the latest recorded position
```

Do not rely solely on conversation history.

The Compass is the persisted migration memory.

---

# Compass Quality Bar

A future AI agent with no prior conversation history should be able to open:

```text
EXECUTION_ENGINE_COMPASS.md
```

and immediately understand:

```text
where the migration currently stands,
what has been completed,
what remains,
what risks exist,
and what should happen next.
```
