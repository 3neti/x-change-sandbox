# Refactor Execution Checklist (v2 – Cost-Aware & Test-Gated)

## 🎯 Objective
Safely extract and relocate logic from `x-change` and `voucher` into `3neti/cash` **without breaking behavior**, using `composer test` as the primary validation gate and minimizing costly lifecycle command runs.

---

## 🧱 Baseline Principle

> **If `composer test` passes, the system is considered stable for that slice.**

This is valid because:
- Lifecycle scenarios are already covered in Pest
- Open-slice, withdraw, redeem, and claim flows are tested
- API + Service + Console layers are exercised end-to-end

---

## ✅ Gate Hierarchy

### 1. Default Gate (EVERY STEP)
```bash
composer test
```

Covers:
- Lifecycle flows (redeem, withdraw, claim)
- Open-slice scenarios (including enforced interval)
- API + Service integration
- Ledger + reconciliation behavior

---

### 2. Optional Focused Test (WHEN NEEDED)
```bash
./vendor/bin/pest --filter=<specific_test>
```

Use when:
- Refactoring a specific domain (e.g., withdrawal, validation)
- Debugging failures faster before full suite run

---

### 3. Gold Standard (MILESTONES ONLY 💸)

```bash
php artisan xchange:lifecycle:run divisible_open_three_slices_enforced_interval --timeout=1 --poll=1 --accept-pending --json
```

Use ONLY when:
- Completing a major refactor phase
- Validating real integration behavior
- Suspecting drift between package tests and host app

---

## 🔒 Behavioral Baseline (DO NOT BREAK)

The following must always remain true:

### Open Slice Scenario
- 3 successful withdrawals
- Amounts: `75 → 50 → 25`
- Remaining balance: `75 → 25 → 0`
- Final state: `fully_claimed = true`

### Claim Execution
- Correct routing:
    - `redeem` vs `withdraw`
- Idempotency preserved
- Validation rules enforced

### Ledger Integrity
- Each claim produces:
    - Ledger entry
    - Disbursement record
- Remaining balance tracked correctly

### System Integrity
- All tests pass (`~261 passing`)
- No regression in:
    - API responses
    - Lifecycle routes
    - Pricing
    - Reconciliation

---

## 🔁 Step-by-Step Refactor Loop

For **every extraction or rename**:

### 1. Identify Logic to Extract
- From `x-change` or `voucher`
- Candidate:
    - validation
    - claim execution
    - withdrawal processing
    - state transitions

---

### 2. Move to `3neti/cash`
- Introduce:
    - Service / Action / Value Object
- Keep interfaces clean
- Avoid Laravel-specific coupling if possible

---

### 3. Wire Back into Orchestrator
- Replace original logic with:
    - adapter
    - service call
- Maintain same input/output contract

---

### 4. Run Tests (MANDATORY)
```bash
composer test
```

If ❌ fails:
- Fix immediately
- Do NOT proceed

---

### 5. Verify No Behavior Drift
Check:
- Claim flow still works
- Withdraw logic intact
- No silent changes in amounts or status

---

### 6. Commit (Small & Atomic)
- One logical change per commit
- Message format:
  ```
  refactor: extract <feature> to cash package (no behavior change)
  ```

---

## 🚫 Hard Rules

- ❌ No multiple extractions per step
- ❌ No skipping tests
- ❌ No “temporary breakage”
- ❌ No behavior changes unless explicitly planned

---

## 🧠 Refactor Strategy

### Extract in this order:
1. **Validation logic** (pure, easiest)
2. **Execution logic** (withdraw/redeem)
3. **State transitions**
4. **Ledger-related logic**
5. **Disbursement orchestration**

---

## 💡 Cost Optimization Rule

> Treat the lifecycle console command as a **production simulation**, not a unit test.

- Use it sparingly
- Trust Pest for iteration speed
- Validate externally only when needed

---

## 🏁 Exit Criteria for Phase

You are done with this phase when:

- [ ] Core logic lives in `3neti/cash`
- [ ] `x-change` becomes orchestration-only
- [ ] `voucher` becomes data/config-centric
- [ ] All tests still pass
- [ ] Lifecycle scenario still behaves identically

---

## 🧭 Guiding Philosophy

> **Refactor = Move, Not Change**

If behavior changes, it’s not a refactor — it’s a feature.

---

## 🔜 Next Step

Proceed with the **first extraction slice**:
👉 Move **withdrawal validation + amount resolution** into `3neti/cash`

Then run:
```bash
composer test
```

And repeat the loop.
