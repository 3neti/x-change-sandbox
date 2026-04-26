# 🧭 Withdrawal Architecture – TODO Roadmap

This outlines the remaining work to complete the migration and fully realize the intended architecture:

```text
cash     = financial truth / rules engine
x-change = orchestration / adapters / execution
voucher  = container / claimable instrument
```

---

# ✅ CURRENT STATUS

### ✔ Validation (financial)
- [x] Eligibility (withdrawable, active, not expired)
- [x] Amount bounds (min, >0, <= balance)
- [x] Min withdrawal → moved to cash
- [x] Slice exhaustion → cash
- [x] Interval throttling → cash
- [x] x-change delegates correctly

### ✔ Pipeline Architecture
- [x] WithdrawalPipeline
- [x] Step grouping + tagging
- [x] `shouldRun()` filtering
- [x] Observability / trace
- [x] Thin processor

### ✔ Execution Flow
- [x] Disbursement step
- [x] Pending recorder
- [x] Wallet settlement
- [x] Result factory

---

# 🚧 NEXT: AUTHORIZATION POLICY (HIGH PRIORITY)

## Goal
Move **“who is allowed to approve withdrawal”** into structured, pluggable policy.

## Tasks

### 1. Define contract (cash or shared)

```php
interface WithdrawalAuthorizationPolicyContract
{
    public function authorize(
        WithdrawableInstrumentContract $instrument,
        array $context
    ): void;
}
```

---

### 2. Implement baseline policies

#### OTP to Owner
- [ ] Send OTP
- [ ] Validate OTP
- [ ] Enforce ownership

#### Trusted Vendor Mandate
- [ ] Allow vendor to debit within limits
- [ ] Store mandate in voucher/cash metadata
- [ ] Validate vendor identity

#### Threshold Rules
- [ ] Define threshold amount
- [ ] Require additional approval above threshold

---

### 3. Pipeline integration

Add new steps:

```text
AuthorizeWithdrawalClaimantStep
→ OTPAuthorizationStep
→ VendorMandateAuthorizationStep
→ ThresholdAuthorizationStep
```

- [ ] Make steps configurable via config
- [ ] Use `shouldRun()` for conditional execution

---

### 4. Tests

- [ ] OTP success/failure
- [ ] Vendor allowed vs rejected
- [ ] Threshold triggers additional requirement

---

# 🚧 NEXT: VALIDATION POLICY (NON-FINANCIAL)

## Goal
Separate **input requirements** from financial rules.

### Tasks

- [ ] Introduce `WithdrawalInputValidationService`
- [ ] Validate presence of:
    - mobile
    - selfie
    - location
    - kyc
    - secret
- [ ] Make requirements configurable per flow

Example:

```php
config('x-change.withdrawal.requirements', [
    'mobile' => true,
    'selfie' => false,
    'kyc' => true,
]);
```

---

# 🚧 NEXT: EXECUTION POLICY IMPROVEMENTS

## 1. Fee Strategy → move to cash

### Tasks

- [ ] Define:

```php
interface CashWithdrawalFeeStrategyContract
```

- [ ] Implement:
    - fixed fee
    - percentage fee
    - tiered fee

- [ ] Integrate into:
    - payout request
    - settlement
    - result

---

## 2. Instrument-driven configuration

Remove remaining x-change config dependencies:

### Replace:

```php
config('x-change.withdrawal.open_slice_min_interval_seconds')
```

### With:

- [ ] `instrument->getIntervalSeconds()`
- [ ] `instrument->getMinWithdrawal()`
- [ ] `instrument->getFeeStrategy()`

---

## 3. Multi-rail execution policy

- [ ] Support:
    - INSTAPAY
    - PESONET
    - WALLET (GCash, Maya)
- [ ] Strategy per rail
- [ ] Retry/fallback logic

---

# 🚧 NEXT: DOMAIN CLEANUP

## 1. Remove legacy logic from x-change

- [ ] Any remaining:
    - amount validation
    - interval checks
    - slice logic

## 2. Ensure adapters are thin

Adapters should only:
- map Voucher → Instrument
- fetch external data (claims, contacts)

---

# 🚧 NEXT: ADVANCED CAPABILITIES

## 1. Fraud / Risk Layer

- [ ] Introduce `FraudScoringStep`
- [ ] Velocity checks
- [ ] Device/location anomaly detection

---

## 2. Rate Limiting

- [ ] Per user
- [ ] Per voucher
- [ ] Per vendor

---

## 3. Audit + Compliance

- [ ] Full audit trail per pipeline step
- [ ] Persist observability trace
- [ ] Exportable logs

---

# 🚧 FUTURE (STRATEGIC)

## 1. Cash as a standalone financial engine

- [ ] Multi-institution support
- [ ] Policy per bank/EMI
- [ ] Regulatory compliance hooks

---

## 2. Smart instruments

- [ ] Time-locked vouchers
- [ ] Geo-restricted withdrawals
- [ ] Conditional claims

---

# 🧠 SUMMARY

You are **~80–85% done with the core architecture**.

Remaining work is:

```text
1. Authorization Policy  ← NEXT BIG SLICE
2. Non-financial Validation
3. Fee Strategy → cash
4. Instrument-driven config
5. Advanced controls (fraud, rate limits)
```

---

# 🚀 RECOMMENDED NEXT STEP

👉 **Start with Authorization Policy**

Because:
- it completes the control model (who can withdraw)
- it unlocks real-world deployment scenarios (banks, vendors)
- it naturally fits your pipeline architecture

---

You're no longer building a feature.

You're building a **financial orchestration engine**.
