# Current Refactor Phase — Step-by-Step Execution Checklist

## Goal of This Phase

Stabilize naming, preserve the green baseline, and begin the **safe extraction of financial execution behavior** from `x-change` and `voucher` into `cash` without breaking the current claim-first lifecycle. This follows the agreed architecture where:
- `x-change` remains orchestration/API
- `voucher` remains contract/lifecycle
- `cash` becomes execution/value authority

---

## Phase Rules

- One focused change at a time
- Rename-only and behavior-change steps must not be mixed
- After every meaningful step:
    - run nearest relevant tests
    - if claim/redemption/withdraw/disburse behavior is touched, run lifecycle console commands
- Do not remove legacy code until adapter-based delegation is proven

---

## Track 0 — Baseline Lock

### Step 0.1 — Freeze baseline
- Confirm all current test suites are green
- Confirm lifecycle console commands are green
- Record the exact commands used as the refactor baseline

### Step 0.2 — Snapshot current behavior
Create a short baseline note containing:
- canonical public API routes in use
- legacy compatibility routes still active
- lifecycle console commands to rerun
- core passing suites for:
    - claim/start
    - claim/complete
    - claim/submit
    - redeem branch
    - withdraw branch
    - voucher lifecycle scenarios

### Step 0.3 — Protect the baseline
- Do not start extraction work until the baseline note exists
- Treat any failing baseline command as a stop condition

---

## Track 1 — Nomenclature Alignment

### Step 1.1 — Adopt the terminology charter
Lock the following as canonical:
- public umbrella action: `claim`
- internal execution paths: `redeem`, `withdraw`
- money movement: `disburse`, `collect`, `settle`
- product noun externally: `Pay Code`
- internal noun: `voucher`

### Step 1.2 — Inventory current names
Create a checklist of existing names across:
- actions
- controllers
- requests
- services
- contracts
- DTOs/resources
- enums
- events
- tests
- config bindings

Mark each as:
- keep
- rename now
- keep for compatibility
- deprecate later

### Step 1.3 — Do rename-only slices
Rename only where terminology is clearly wrong or unstable.

Suggested order:
1. contracts/interfaces
2. actions
3. services
4. controllers/requests/resources
5. enums/DTOs
6. tests

For each rename slice:
- update imports
- update bindings
- update config references
- update route/controller references
- update tests
- run relevant tests immediately

### Step 1.4 — Keep compatibility aliases where needed
If a legacy name is still widely used:
- preserve a compatibility binding, alias, or wrapper
- defer removal until after extraction is complete

---

## Track 2 — Refactor Boundary Inventory

### Step 2.1 — Build the extraction map
Categorize current code into:

#### Stay in `x-change`
- API surface
- idempotency
- audit logging
- reconciliation
- completion context loading
- payload normalization
- lifecycle orchestration

#### Stay in `voucher`
- voucher identity
- instructions contract
- redemption contract model
- lifecycle state
- current redeem compatibility path

#### Move to `cash`
- claim routing decision support
- owner/original claimant checks
- withdrawal eligibility
- slice semantics
- amount resolution
- authorization decisioning
- settlement/disbursement eligibility
- delegated execution behavior

### Step 2.2 — Prioritize high-confidence extractions
Mark these as first-wave candidates:
1. claim routing logic
2. withdrawal validation logic
3. owner verification/original redeemer checks
4. amount resolution and slice rules

These are the safest because they are already domain behavior, not API shape.

---

## Track 3 — Introduce Cash-Side Contracts First

### Step 3.1 — Add contracts in `cash`
Introduce new interfaces before moving logic.

Suggested initial contracts:
- `CashClaimRouterContract`
- `CashClaimPolicyContract`
- `CashOwnershipContract`
- `CashWithdrawalExecutionContract`
- `CashSettlementPolicyContract`
- `CashPayoutTargetResolverContract`

### Step 3.2 — Add DTOs in `cash`
Introduce neutral DTOs that `x-change` can call without knowing implementation details.

Suggested initial DTOs:
- `ClaimRequestData`
- `ClaimExecutionResultData`
- `CashValidationResultData`
- `CashSettlementInstructionData`
- `CashOwnerData`

### Step 3.3 — Bind default no-op or delegated implementations
In the first pass, implementations may still delegate to existing `x-change` or `voucher` logic.

Goal:
- establish the new package boundary
- keep behavior unchanged

### Step 3.4 — Test contract wiring
Run:
- `cash` package tests for new contracts/bindings
- host integration tests proving `x-change` can resolve the new interfaces

---

## Track 4 — First Extraction Slice: Claim Routing

### Step 4.1 — Identify current routing logic
Locate all logic deciding whether a claim becomes:
- redeem
- withdraw
- later collect/settle

This is currently part of the unified claim-first lifecycle and should remain public-API stable while internal branching stays explicit.

### Step 4.2 — Implement `DefaultCashClaimRouter`
Move branch-decision logic into `cash`, but keep inputs and outputs compatible with current orchestration.

### Step 4.3 — Replace `x-change` routing with adapter
Make the existing `x-change` execution factory call the new `cash` router instead of deciding directly.

### Step 4.4 — Run tests
Run:
- claim submit tests
- redeem branch tests
- withdraw branch tests
- lifecycle console commands

### Step 4.5 — Stop if anything drifts
Do not continue until the routing behavior is identical to baseline.

---

## Track 5 — Second Extraction Slice: Withdrawal Validation

### Step 5.1 — Extract validation responsibilities
Move logic such as:
- withdrawable or not
- open-slice only
- amount required
- amount > 0
- amount <= remaining balance
- min withdrawal
- max slices
- claim interval throttling

into `cash` policy/evaluator services

### Step 5.2 — Introduce `DefaultCashClaimPolicy`
Implement cash-side validation methods such as:
- `validateWithdrawal(...)`
- `validateDebit(...)`
- `validateCollection(...)`

### Step 5.3 — Delegate old validator to new policy
Keep the existing `x-change` service as an adapter/wrapper for one slice first.

### Step 5.4 — Run tests
Run:
- withdrawal feature tests
- open-slice tests
- partial claim tests
- lifecycle console commands

### Step 5.5 — Preserve public failure behavior
Do not change:
- response shapes
- error codes/messages
- lifecycle console expectations

unless explicitly scheduled later

---

## Track 6 — Third Extraction Slice: Ownership and Authorization

### Step 6.1 — Introduce ownership service in `cash`
Add:
- `claimOwner(...)`
- `verifyOwner(...)`
- `assertClaimantMayWithdraw(...)`

### Step 6.2 — Move original-redeemer checks
Any rule that says “only the original claimant/redeemer may continue this withdrawal path” should move to `cash`.

### Step 6.3 — Keep `voucher` as lifecycle source, not identity authority
`voucher` may still know lifecycle state, but `cash` should own execution entitlement.

### Step 6.4 — Run tests
Run:
- claimant ownership tests
- withdrawal authorization tests
- lifecycle console commands

---

## Track 7 — Fourth Extraction Slice: Amount Resolution and Slice Semantics

### Step 7.1 — Move amount resolution to `cash`
Extract logic for:
- requested amount vs fixed slice amount
- open-slice required amount
- remaining balance ceiling
- remaining slices
- interval logic where applicable

### Step 7.2 — Add explicit `cash` methods or collaborators
Examples:
- `resolveWithdrawAmount(...)`
- `assertWithdrawable()`
- `getRemainingBalance()`
- `getRemainingSlices()`

### Step 7.3 — Delegate old processor to cash-side amount logic
Keep processor wiring intact in `x-change`, but remove local decisioning.

### Step 7.4 — Run tests
Run:
- slice mode tests
- withdrawal amount tests
- redeem-vs-withdraw edge tests
- lifecycle console commands

---

## Track 8 — Voucher Contract Preservation

### Step 8.1 — Keep the redemption contract boundary intact
Do not break the current rule:
- `inputs.fields` = presence contract
- `validation.*` = semantic contract

### Step 8.2 — Do not silently migrate validation semantics into ad hoc cash code
If validation logic moves, it must still respect:
- collected evidence normalization
- explicit semantic validators
- no hidden implications, especially `kyc` vs `face_match`

### Step 8.3 — Treat voucher as compatibility shell during transition
Voucher continues to expose:
- instructions
- lifecycle state
- redemption contract compatibility
- existing observer/pipeline path

until cash-side execution is fully proven

---

## Track 9 — Lifecycle Verification Gate After Each Slice

After each extraction slice, run:

### Required
- nearest unit/feature tests
- relevant integration tests
- lifecycle console commands

### Recommended milestone checkpoints
- full package test suite for `x-change`
- full package test suite for `voucher`
- full package test suite for `cash`
- combined end-to-end suite where available

If any of these regress:
- revert or fix before proceeding
- do not stack the next extraction on a shaky slice

---

## Track 10 — Documentation and Traceability

### Step 10.1 — Update docs only when the slice is proven
For any accepted responsibility shift, update:
- Terminology Charter
- lifecycle guide
- architectural note
- any package README impacted by bindings or extension points

### Step 10.2 — Keep a migration ledger
For each completed slice, record:
- what moved
- what stayed
- adapter introduced
- tests run
- lifecycle commands run
- compatibility notes
- cleanup still deferred

---

## Track 11 — Cleanup Phase (Only After Proven Delegation)

### Step 11.1 — Identify dead or duplicate logic
Only after a slice is stably delegated to `cash`.

### Step 11.2 — Remove wrappers carefully
Delete legacy logic only when:
- no callers remain
- tests are green
- lifecycle commands are green
- compatibility path is intentionally preserved or intentionally removed

### Step 11.3 — Run the full checkpoint
Before merging or tagging a cleanup milestone:
- run full suite
- run lifecycle console commands
- verify no configuration drift

---

## Suggested Immediate Next Steps

1. Freeze and document the green baseline
2. Complete the nomenclature inventory
3. Do rename-only slices where needed
4. Add the first set of `cash` contracts and DTOs
5. Extract claim routing first
6. Re-test and rerun lifecycle commands
7. Then proceed to withdrawal validation
8. Then ownership/authorization
9. Then amount resolution/slice semantics

---

## Stop Conditions

Pause the refactor immediately if:
- claim-first public behavior changes unintentionally
- redeem and withdraw branches become blurred without explicit plan
- voucher validation contract boundaries are broken
- lifecycle console commands stop passing
- compatibility wrappers are removed too early

---

## Definition of Success for This Phase

This phase succeeds when:
- terminology is stable
- `cash` owns the first extracted execution behaviors
- `x-change` becomes thinner and more orchestration-focused
- `voucher` remains stable as contract/lifecycle compatibility layer
- all tests remain green
- lifecycle console commands remain green

---

## One-Line Operating Rule

**Rename carefully, extract in slices, delegate before deleting, and return to green after every move.**
