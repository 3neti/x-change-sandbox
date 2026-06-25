# ADDENDUM TO x-journal_functional_specifications.md

## 4A. New Core Vocabulary

### Execution Artifact

An execution artifact is a human-facing rendering derived from one or more execution journal entries.

Execution artifacts are evidence objects intended for:

- beneficiaries
- issuers
- auditors
- regulators
- compliance officers
- support teams
- investigators
- public verifiers

An execution artifact is not the source of truth.

The execution journal remains the source of truth.

Artifacts merely provide specialized representations of journal data.

Examples:

```text
Execution Artifact
    ├─ Execution Receipt
    ├─ Execution Certificate
    ├─ Execution Instrument
    ├─ Execution Statement
    ├─ Execution Timeline
    └─ Settlement Envelope
```

---

### Execution Instrument

An execution instrument is a special artifact representing a transferable, redeemable, claimable, reservable, or entitlement-bearing execution.

Examples:

```text
Pay Code Certificate
Gift Check
Traveler's Check
Benefit Card
Transit Card
Escrow Certificate
Treasury Warrant
Allowance Certificate
Coupon Book
```

Execution instruments are intended to visually communicate rights, entitlements, reservations, allocations, and future claimability.

Execution instruments are distinct from receipts.

---

### Visibility Profile

A visibility profile defines what information may be seen by a particular audience.

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

Visibility profiles govern artifact rendering.

---

### Verification Profile

A verification profile defines how an artifact can be validated.

Examples:

```text
verification_url
verification_token
receipt_hash
digital_signature
journal_chain
statement_anchor
envelope_anchor
```

---

## 12A. Artifact Taxonomy

The package shall support artifact classification.

```text
Execution Artifact
    ├─ Receipt
    ├─ Certificate
    ├─ Instrument
    ├─ Statement
    ├─ Timeline
    └─ Envelope
```

### Receipt Examples

```text
Wallet Funding Receipt
Claim Submission Receipt
Disbursement Receipt
Collection Receipt
```

### Certificate Examples

```text
KYC Certificate
OTP Authorization Certificate
Settlement Readiness Certificate
Medical Assistance Eligibility Certificate
```

### Instrument Examples

```text
Pay Code Certificate
Gift Check
Traveler's Check
Transit Card
Benefit Card
Escrow Certificate
Allowance Certificate
```

---

## 13A. Event Transformers

The package shall support transformers for normalizing execution events into canonical journal entries.

Purpose:

- decouple source events from journal schema
- support multiple packages
- simplify integrations
- ensure consistent journal entries

Contract:

```php
interface ExecutionJournalTransformer
{
    public function transform(object $event): ?ExecutionJournalEntryData;
}
```

Initial transformers:

```text
PayCodeIssuedTransformer
ClaimSubmittedTransformer
ClaimCompletedTransformer
KycApprovedTransformer
AuthorizationApprovedTransformer
DisbursementCompletedTransformer
SettlementReadyTransformer
DocumentAttachedTransformer
```

---

## 13B. DTO Casting Strategy

The package shall support DTO-backed casting for stable journal components.

Recommended DTO casts:

```text
ExecutionActorData
ExecutionSubjectData
ExecutionMoneyData
ExecutionReferenceData
ExecutionIntegrityData
```

Flexible payloads remain JSON:

```text
payload_json
evidence_json
metadata_json
```

to avoid over-constraining future execution types.

---

## 20A. Artifact Rendering System

The package shall introduce artifact renderers.

Contract:

```php
interface ExecutionArtifactRenderer
{
    public function render(
        ExecutionJournalEntryData $entry,
        string $artifactType,
        string $format
    ): mixed;
}
```

Supported artifact types:

```text
receipt
certificate
instrument
statement
timeline
envelope
```

Supported formats:

```text
array
json
markdown
html
text
pdf
```

PDF support is optional.

---

## 20B. Artifact Profiles

The package shall support artifact profiles.

Purpose:

Allow one execution record to render differently depending on context.

Examples:

```text
GIFT_CHECK
TRAVELERS_CHECK
BENEFIT_CARD
TRANSIT_CARD
ESCROW_CERTIFICATE
TREASURY_WARRANT
ALLOWANCE_CERTIFICATE
STANDARD_RECEIPT
STANDARD_CERTIFICATE
```

Example:

```text
pay_code.issued
```

may render as:

```text
STANDARD_RECEIPT
```

or

```text
GIFT_CHECK
```

depending on issuer configuration.

---

## 20C. Instrument Rendering

Execution instruments shall support specialized layouts.

Examples:

### Pay Code Certificate

Visual inspiration:

```text
Gift Check
Traveler's Check
Cash Voucher
```

### Multi-Slice Voucher

Visual inspiration:

```text
Transit Card
Benefit Card
Stored Value Card
```

### Zero-Denominated Voucher

Visual inspiration:

```text
Authorization Letter
Benefit Certificate
Eligibility Certificate
```

---

## 21A. Visibility Governance

The package shall own visibility policy definitions.

The package shall not depend on x-ray for visibility decisions.

Principle:

```text
x-journal authorizes visibility
x-ray renders visibility
```

---

## 21B. Visibility Profiles

Recommended profiles:

```text
none
metadata
masked
summary
finance
issuer
redeemer
redeemer_summary
compliance
regulator
internal_full
public_verify
```

Visibility profiles determine:

- visible fields
- redacted fields
- hidden sections
- artifact rendering rules

---

## 21C. Visibility Matrix

Each execution type may define visibility rules.

Example:

```text
pay_code.issued
    public_verify
    issuer
    finance
    compliance

kyc.approved
    redeemer
    compliance
    internal_full

disbursement.completed
    redeemer
    issuer
    finance
    compliance
    public_verify
```

---

## 21D. Journal Visibility Maintenance

The package shall support visibility configuration.

Recommended concepts:

```text
Capture Point Registry
Visibility Profiles
Role Mapping
Artifact Policies
Public Verification Rules
```

The package should expose configuration contracts.

Host applications such as x-change may provide the UI.

---

## 21E. Access Policies

Recommended contracts:

```php
ExecutionJournalPolicy
ExecutionArtifactPolicy
ExecutionVisibilityProfile
ExecutionRedactionService
ExecutionAccessReasonLogger
```

---

## 21F. Role Responsibilities

### Developer

Default access:

```text
technical metadata
correlation IDs
ERNs
statuses
errors
```

PII should be masked by default.

---

### Finance

Default access:

```text
amounts
balances
fees
statements
settlement summaries
```

No raw KYC evidence.

---

### Compliance

Default access:

```text
full evidentiary access
```

Access should be auditable.

---

### Redeemer

Default access:

```text
own receipts
own certificates
own timelines
```

---

### Public Verifier

Default access:

```text
verification status only
```

No sensitive evidence.

---

## 22A. Public Verification

The package shall support public verification.

Recommended verification URL:

```text
/verify/{ern}
```

Optional:

```text
/verify/{ern}?token=...
```

Verification page shall be controlled by visibility profile.

---

## 22B. Verification Metadata

Artifacts may include:

```text
ERN
Verification Token
Receipt Hash
Verification QR
Verification URL
```

---

## 22C. Verification Levels

### Level 1

```text
ERN + Verification URL
```

### Level 2

```text
Verification Token
```

### Level 3

```text
Receipt Hash
```

### Level 4

```text
Digital Signature
```

### Level 5

```text
Journal Chain Verification
```

### Level 6

```text
Statement Anchoring
```

### Level 7

```text
Settlement Envelope Anchoring
```

---

## 27A. Statement Anchoring

Statements shall be capable of acting as recovery anchors.

Statements may include:

```text
entries_hash
journal_range
statement_hash
previous_statement_hash
```

Purpose:

- disaster recovery
- reconciliation
- audit validation
- regulatory review

---

## 32A. Additional DTOs

Add:

```text
ExecutionArtifactData
ExecutionVisibilityData
ExecutionVerificationData
ExecutionAccessContextData
ExecutionRenderContextData
```

---

## 33A. Additional Contracts

Add:

```text
ExecutionJournalTransformer
ExecutionArtifactRenderer
ExecutionVisibilityResolver
ExecutionVerificationService
ExecutionArtifactProfileResolver
ExecutionRedactionService
```

---

## 34A. Additional Models

Add:

```text
ExecutionArtifact
ExecutionVisibilityProfile
ExecutionVerificationToken
```

Verification token model may be deferred.

---

## 37A. Additional Architecture Invariants

16. Visibility is governed by x-journal.
17. x-ray never authorizes visibility.
18. Every public artifact must be verifiable.
19. Every artifact originates from journal entries.
20. Verification must not expose sensitive evidence.
21. Artifact profiles determine presentation, not journal truth.
22. Transformers normalize events before persistence.
23. DTO casts are used for stable structures only.
24. Flexible evidence remains JSON-based.
25. Execution instruments are first-class artifacts.
26. Statements may serve as recovery anchors.
27. Settlement envelopes may anchor verification chains.
28. Public verification must support redaction.
29. Developers do not automatically receive full evidentiary access.
30. Compliance access should be auditable.
