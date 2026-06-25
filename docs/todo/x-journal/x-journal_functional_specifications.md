# 3neti/x-journal Functional Specification

## 1. Package Purpose

`3neti/x-journal` is a Laravel package for capturing, persisting, logging, rendering, and exporting business-level execution records.

It provides the official human-auditable journal layer for systems such as:

- `3neti/x-change`
- `3neti/settlement-envelope`
- `3neti/x-ray`
- `3neti/x-rider`
- host applications using financial workflows

The package is not merely an application log package.

It is an execution journal package.

Its purpose is to answer:

> What business execution occurred, when, by whom, under what authority, with what evidence, and with what result?

---

## 2. Core Positioning

`x-journal` is the evidentiary layer of the 3neti ecosystem.

It is designed for:

- human audit
- regulatory review
- CTO confidence
- disaster recovery
- reconstruction support
- receipt generation
- certificate generation
- statement generation
- settlement envelope assembly

It complements but does not replace:

- database tables
- application logs
- Monolog logs
- wallet ledgers
- accounting ledgers
- settlement envelopes
- observability systems
- SIEM pipelines

---

## 3. Foundational Concept

The system already knows operational truth through its database.

Humans, regulators, auditors, banks, and courts need evidentiary truth.

Therefore:

```text
Database = Operational Truth
Execution Journal = Evidentiary Truth
```

The journal is a human-readable and machine-verifiable official log of business executions.

---

## 4. Core Vocabulary

### Execution

A business operation that changes, records, confirms, authorizes, or settles a meaningful state.

Examples:

- Pay Code issued
- wallet funded
- claim submitted
- KYC approved
- OTP authorized
- LOA uploaded
- medical certificate attached
- disbursement requested
- disbursement completed
- settlement reconciled
- statement closed

---

### Execution Event

A machine-level occurrence emitted by an application, package, webhook, job, or integration.

Examples:

```text
pay_code.generate.succeeded
claim.submit.succeeded
disbursement.completed
kyc.approved
settlement.ready
```

Execution events may originate from:

- Laravel Events
- domain actions
- webhook handlers
- payout callbacks
- wallet transactions
- Monolog records
- scheduled jobs
- settlement-envelope events

---

### Execution Journal Entry

A curated business-level record derived from one or more execution events.

Not every system log becomes a journal entry.

Only business-significant executions become journal entries.

---

### Execution Record

The human-readable conceptual representation of an execution journal entry.

Examples:

```text
Voucher Issuance Record
Claim Record
KYC Record
Settlement Record
Medical Assistance Record
Disbursement Record
```

---

### Execution Receipt

A receipt-style rendering of one execution record.

Best for:

- issuance
- collection
- disbursement
- claim submission
- wallet funding
- voucher escrow/reservation

---

### Execution Certificate

A certification-style rendering of one execution record.

Best for:

- KYC approval
- OTP authorization
- document verification
- settlement readiness
- medical assistance eligibility
- compliance completion

---

### Execution Statement

A periodic summary of many execution records.

Best for:

- statement of account
- issuer statement
- wallet statement
- program statement
- settlement statement
- regulatory statement
- recovery anchor

---

### Execution Timeline

A chronological rendering of execution records connected by subject, voucher, claim, settlement envelope, issuer, wallet, or correlation ID.

Best for:

- support
- investigation
- cockpit UI
- audit review
- dispute resolution

---

### Settlement Envelope

A compiled proof package that may include selected execution records, receipts, certificates, statements, documents, evidence, readiness checks, and settlement metadata.

The settlement envelope consumes journal entries.

It should not own the journal itself.

---

## 5. Package Boundary

### `3neti/x-journal` owns:

- execution journal entry model
- execution reference number generation
- journal recording service
- journal sinks
- journal projections
- journal rendering contracts
- artifact generation contracts
- statement snapshot contracts
- hash-chain support
- archive support
- retrieval APIs
- search/filter APIs
- export APIs

---

### `3neti/x-change` uses `x-journal` to record:

- issuer onboarding
- wallet opening
- wallet funding
- Pay Code estimation
- Pay Code issuance
- claim start
- claim complete
- claim submit
- redeem execution
- withdraw execution
- disbursement requested
- disbursement completed
- collection completed
- reconciliation completed
- statement closed

---

### `3neti/settlement-envelope` uses `x-journal` to record:

- envelope created
- evidence attached
- readiness check performed
- readiness passed
- readiness failed
- settlement request prepared
- settlement package exported
- envelope closed

---

### `3neti/x-ray` may consume or observe `x-journal`, but should not own it.

`x-ray` remains technical observability.

`x-journal` is business evidentiary observability.

---

## 6. System Architecture

The package follows this conceptual flow:

```text
Raw System Event
    ↓
Execution Journal Projector
    ↓
Execution Journal Entry
    ↓
Journal Sinks
    ├─ Database
    ├─ Monolog
    ├─ Object Storage
    ├─ Search Index
    ├─ SIEM/Webhook
    └─ Hash Chain
    ↓
Human Artifacts
    ├─ Execution Receipt
    ├─ Execution Certificate
    ├─ Execution Statement
    ├─ Execution Timeline
    └─ Settlement Envelope
```

---

## 7. Persistence Strategy

### Default Canonical Store

The default canonical store should be an append-only relational database table.

Supported default databases:

- MySQL
- PostgreSQL
- SQLite for testing

Reason:

- strong transactional guarantees
- ordering
- idempotency
- relational references
- reporting
- statements
- reconstruction
- compatibility with Laravel apps

---

### Optional Secondary Stores

The package should allow optional sinks for:

- Monolog
- object storage such as S3/R2
- OpenSearch / Elasticsearch
- MongoDB
- SIEM webhook
- external compliance archive

MongoDB should not be the required default.

MongoDB may be a projection sink for flexible JSON querying.

---

### Archive Store

The package should support immutable JSON archive export to object storage.

Each journal entry may be archived as:

```text
/journal/YYYY/MM/DD/{ern}.json
```

Optional future support:

- object lock
- WORM-compatible storage
- signed archive manifests
- hash-chained archive bundles

---

## 8. Journal Entry Table

Recommended table:

```text
execution_journal_entries
```

Recommended columns:

```text
id
ern
execution_type
execution_stage
status
occurred_at
recorded_at

actor_type
actor_id
actor_name

subject_type
subject_id
subject_label

issuer_id
voucher_id
voucher_code
wallet_id
settlement_envelope_id

amount
currency
direction

idempotency_key
correlation_id
causation_id
source_event
source_channel

summary
payload_json
evidence_json
money_json
parties_json
references_json
metadata_json

hash
previous_hash

created_at
updated_at
```

---

## 9. Canonical Journal Entry JSON Format

Each journal entry should be serializable into a canonical JSON structure.

Example:

```json
{
  "ern": "ERN-2026-000000123",
  "type": "voucher.issued",
  "title": "Pay Code Issued",
  "status": "committed",
  "occurred_at": "2026-06-22T08:15:00+08:00",
  "recorded_at": "2026-06-22T08:15:01+08:00",
  "actor": {
    "type": "issuer_user",
    "id": "usr_123",
    "name": "Maria Santos"
  },
  "subject": {
    "type": "voucher",
    "id": "vch_456",
    "label": "Pay Code ABC123"
  },
  "money": {
    "direction": "reserved",
    "amount": "5000.00",
    "currency": "PHP",
    "source_wallet": "wallet_issuer_001"
  },
  "execution": {
    "stage": "issuance",
    "contract_persisted": true,
    "cash_funded": true,
    "instructions_persisted": true
  },
  "evidence": [
    {
      "type": "voucher_instructions",
      "hash": "sha256:..."
    },
    {
      "type": "idempotency_key",
      "value": "idem_..."
    }
  ],
  "references": {
    "voucher_code": "ABC123",
    "correlation_id": "corr_...",
    "source_event": "pay_code.generate.succeeded"
  },
  "integrity": {
    "hash": "sha256:...",
    "previous_hash": "sha256:..."
  }
}
```

---

## 10. Execution Reference Number

Each journal entry receives an Execution Reference Number.

Recommended format:

```text
ERN-YYYY-000000001
```

Examples:

```text
ERN-2026-000000001
ERN-2026-000000002
ERN-2026-000000003
```

The package should support configurable ERN formats.

Config example:

```php
'ern' => [
    'prefix' => 'ERN',
    'yearly_sequence' => true,
    'padding' => 9,
],
```

---

## 11. Journal Entry Types

Initial supported execution types:

```text
wallet.funded
wallet.opened
pay_code.estimated
pay_code.issued
pay_code.expired
pay_code.cancelled

claim.started
claim.completed
claim.submitted
claim.failed

kyc.initiated
kyc.approved
kyc.rejected

authorization.requested
authorization.approved
authorization.failed

document.submitted
document.attached
document.rejected

settlement.envelope.created
settlement.evidence.attached
settlement.readiness.checked
settlement.ready
settlement.failed
settlement.reconciled

disbursement.requested
disbursement.completed
disbursement.failed

collection.requested
collection.completed
collection.failed

statement.opened
statement.closed
```

---

## 12. Record Taxonomy

The package should allow execution records to be rendered under domain-specific labels.

Initial record taxonomy:

```text
Execution Record
    ├─ Voucher Issuance Record
    ├─ Claim Record
    ├─ KYC Record
    ├─ Authorization Record
    ├─ Document Submission Record
    ├─ Settlement Readiness Record
    ├─ Medical Assistance Record
    ├─ Collection Record
    ├─ Disbursement Record
    └─ Reconciliation Record
```

These should not require separate database tables at first.

They should be produced by renderers from the canonical journal entry.

---

## 13. Journal Capture

The package should support multiple capture mechanisms.

### Direct Recording

```php
ExecutionJournal::record(
    type: 'pay_code.issued',
    subject: $voucher,
    actor: $issuer,
    payload: [...]
);
```

---

### Event Projection

```php
class PayCodeIssuedProjector implements ExecutionJournalProjector
{
    public function project(object $event): ?ExecutionJournalEntryData
    {
        // return journal entry data or null
    }
}
```

---

### Listener-Based Capture

Host applications may register listeners:

```php
VoucherIssued::class => RecordVoucherIssuedJournalEntry::class
ClaimSubmitted::class => RecordClaimSubmittedJournalEntry::class
DisbursementCompleted::class => RecordDisbursementCompletedJournalEntry::class
```

---

### Manual Capture

Operators may create administrative journal entries for exceptional events:

```text
manual.adjustment.recorded
manual.reconciliation.note_added
manual.regulatory.annotation_added
```

Manual entries must include actor and reason.

---

## 14. Journal Sinks

The package should define a sink contract:

```php
interface ExecutionJournalSink
{
    public function write(ExecutionJournalEntryData $entry): void;
}
```

Initial sinks:

```text
DatabaseJournalSink
MonologJournalSink
ObjectStorageJournalSink
NullJournalSink
```

Future sinks:

```text
ElasticsearchJournalSink
MongoJournalSink
SiemWebhookJournalSink
HashChainJournalSink
```

---

## 15. Default Sink Behavior

Default enabled sinks:

```text
DatabaseJournalSink
MonologJournalSink
```

Optional enabled sinks:

```text
ObjectStorageJournalSink
ElasticsearchJournalSink
MongoJournalSink
SiemWebhookJournalSink
```

Config example:

```php
'sinks' => [
    'database' => [
        'enabled' => true,
    ],
    'monolog' => [
        'enabled' => true,
        'channel' => 'execution-journal',
    ],
    'object_storage' => [
        'enabled' => false,
        'disk' => 's3',
        'path' => 'journal',
    ],
    'search' => [
        'enabled' => false,
        'driver' => 'opensearch',
    ],
],
```

---

## 16. Monolog Integration

Monolog is not the canonical journal.

It is a sink.

The package should write structured log records suitable for downstream logging pipelines.

Example Monolog record:

```json
{
  "message": "execution.journal.recorded",
  "context": {
    "ern": "ERN-2026-000000123",
    "type": "pay_code.issued",
    "status": "committed",
    "subject_type": "voucher",
    "subject_id": "vch_456",
    "amount": "5000.00",
    "currency": "PHP",
    "correlation_id": "corr_..."
  }
}
```

---

## 17. Integrity and Immutability

Journal entries should be append-only.

Default behavior:

- no update after creation
- corrections are new journal entries
- reversals are new journal entries
- annotations are new journal entries
- failed attempts may be journaled if business-significant

Future integrity features:

- hash per entry
- previous hash
- chain verification
- archive manifest
- external notarization
- tamper detection command

---

## 18. Idempotency

Money-sensitive journal entries must support idempotency.

If the same idempotency key and payload are recorded again:

- return existing entry
- do not duplicate

If the same idempotency key but different payload is recorded:

- raise conflict
- optionally record conflict attempt

Applicable to:

- Pay Code issuance
- claim submit
- disbursement
- collection
- reconciliation
- statement close

---

## 19. Correlation and Causation

Each journal entry should support:

```text
correlation_id
causation_id
```

Usage:

- correlation ID groups entries in one business journey
- causation ID points to the entry or event that caused the current one

Example:

```text
Voucher Issuance ERN-001
    ↓
Claim Submitted ERN-002
    ↓
KYC Approved ERN-003
    ↓
Disbursement Completed ERN-004
```

---

## 20. Human Rendering

The package should provide rendering contracts, not necessarily full UI first.

### Renderer Contract

```php
interface ExecutionRecordRenderer
{
    public function render(ExecutionJournalEntryData $entry, string $format): mixed;
}
```

Supported formats:

```text
html
markdown
array
json
pdf
text
```

PDF may be deferred to a host app or optional package dependency.

---

## 21. Execution Receipt Rendering

Execution Receipt is used for one execution.

Example sections:

```text
Header
Execution Summary
Parties
Money Movement / Commitment
Evidence
References
Integrity
Verification QR
Footer
```

Example use cases:

- Pay Code Issuance Receipt
- Claim Submission Receipt
- Collection Receipt
- Disbursement Receipt
- Wallet Funding Receipt

---

## 22. Execution Certificate Rendering

Execution Certificate is used when the important result is verification, authorization, readiness, or compliance.

Example sections:

```text
Header
Certified Event
Subject
Result
Evidence
Authority
Timestamp
Integrity
Verification QR
Footer
```

Example use cases:

- KYC Certificate
- OTP Authorization Certificate
- Settlement Readiness Certificate
- Document Submission Certificate
- Medical Assistance Eligibility Certificate

---

## 23. Execution Statement Rendering

Execution Statement summarizes many entries for a period.

Statement types:

```text
Issuer Statement
Wallet Statement
Program Statement
Settlement Statement
Beneficiary Statement
Regulatory Statement
```

Example sections:

```text
Statement Header
Period
Opening Position
Activity Summary
Journal Entry Summary
Exceptions
Closing Position
Integrity
Generated At
```

Example activity summary:

```text
Opening Reserved Balance
Issued Pay Codes
Claimed Amount
Disbursed Amount
Expired / Released Amount
Failed / Pending Amount
Closing Reserved Balance
```

---

## 24. Execution Timeline Rendering

Execution Timeline shows related entries chronologically.

Timeline grouping keys:

```text
voucher_code
claim_id
settlement_envelope_id
wallet_id
issuer_id
correlation_id
beneficiary_id
```

Example:

```text
08:15 Pay Code Issued
08:17 Claim Started
08:19 KYC Approved
08:21 LOA Uploaded
08:25 Medical Certificate Attached
08:30 Settlement Ready
08:31 Disbursement Completed
```

---

## 25. Settlement Envelope Integration

Settlement envelopes should consume journal records.

A settlement envelope may include:

```text
Voucher Issuance Record
Claim Record
KYC Certificate
Document Submission Record
Medical Assistance Record
Authorization Record
Disbursement Receipt
Settlement Statement
```

The envelope should reference journal entries by ERN.

It should not duplicate the canonical journal.

---

## 26. Disaster Recovery Role

The journal should support recovery but should not be the only backup.

Its recovery role:

- reconstruct business timeline
- verify balances against statements
- identify missing or duplicate executions
- compare journal entries against wallet ledger
- compare statements against current balances
- provide human-readable recovery audit trail

The journal should become a recovery anchor, not a replacement for database backups.

---

## 27. Periodic Statements

The package should support statement snapshots.

Recommended table:

```text
execution_statement_snapshots
```

Recommended columns:

```text
id
statement_number
statement_type
period_start
period_end
subject_type
subject_id
opening_json
activity_json
closing_json
entries_count
entries_hash
generated_at
generated_by_type
generated_by_id
payload_json
hash
previous_hash
created_at
updated_at
```

Statement examples:

```text
Daily Wallet Statement
Monthly Issuer Statement
Program Settlement Statement
Regulatory Summary Statement
```

---

## 28. API Surface

Optional package routes may expose:

```text
GET /api/x-journal/entries
GET /api/x-journal/entries/{ern}
GET /api/x-journal/entries/{ern}/receipt
GET /api/x-journal/entries/{ern}/certificate
GET /api/x-journal/timelines/{correlation_id}
GET /api/x-journal/statements
GET /api/x-journal/statements/{statement_number}
```

Routes should be publishable/optional.

The package should primarily expose services and actions.

---

## 29. UI Surface

The package may later provide optional Inertia/Vue pages, but the first version should not depend on a UI.

Possible future pages:

```text
Journal Index
Journal Entry Detail
Execution Timeline
Statement Index
Statement Detail
Receipt Preview
Certificate Preview
```

In `x-change` cockpit, these would appear as:

```text
Execution Journal
Statements
Receipts
Certificates
Timelines
```

---

## 30. Configuration

Recommended config file:

```php
return [
    'enabled' => true,

    'ern' => [
        'prefix' => 'ERN',
        'yearly_sequence' => true,
        'padding' => 9,
    ],

    'database' => [
        'connection' => null,
        'table' => 'execution_journal_entries',
        'statements_table' => 'execution_statement_snapshots',
    ],

    'sinks' => [
        'database' => ['enabled' => true],
        'monolog' => [
            'enabled' => true,
            'channel' => 'execution-journal',
        ],
        'object_storage' => [
            'enabled' => false,
            'disk' => 's3',
            'path' => 'journal',
        ],
    ],

    'integrity' => [
        'hash_entries' => true,
        'chain_entries' => false,
    ],

    'rendering' => [
        'default_format' => 'html',
        'pdf_enabled' => false,
    ],
];
```

---

## 31. Required Actions

Initial package actions:

```text
RecordExecutionJournalEntry
GenerateExecutionReferenceNumber
WriteExecutionJournalEntryToSinks
RenderExecutionReceipt
RenderExecutionCertificate
RenderExecutionTimeline
GenerateExecutionStatementSnapshot
VerifyExecutionJournalIntegrity
```

---

## 32. Required DTOs

Initial DTOs:

```text
ExecutionJournalEntryData
ExecutionActorData
ExecutionSubjectData
ExecutionMoneyData
ExecutionEvidenceData
ExecutionReferenceData
ExecutionIntegrityData
ExecutionStatementSnapshotData
ExecutionArtifactData
```

---

## 33. Required Contracts

Initial contracts:

```text
ExecutionJournalRecorder
ExecutionJournalSink
ExecutionJournalProjector
ExecutionReferenceNumberGenerator
ExecutionRecordRenderer
ExecutionStatementGenerator
ExecutionIntegrityHasher
```

---

## 34. Required Models

Initial Eloquent models:

```text
ExecutionJournalEntry
ExecutionStatementSnapshot
ExecutionArtifact
```

`ExecutionArtifact` may be deferred if artifacts are rendered on demand.

---

## 35. Testing Strategy

The package should include tests for:

### Recording

- records a journal entry
- generates ERN
- persists payload JSON
- writes to enabled sinks
- skips disabled sinks

### Idempotency

- same key and same payload returns existing entry
- same key and different payload raises conflict

### Integrity

- hashes entry payload
- stores previous hash when chain enabled
- verifies chain integrity

### Rendering

- renders receipt from journal entry
- renders certificate from journal entry
- renders timeline from related entries
- renders statement from period entries

### Statements

- generates opening/activity/closing snapshot
- freezes period state
- includes hash of included entries

### Integration

- x-change Pay Code issuance can create journal entry
- claim submission can create journal entry
- disbursement completion can create journal entry
- settlement envelope can reference ERNs

---

## 36. Initial Development Slices

### Slice 1 — Package Skeleton and Core Model

Deliver:

- package skeleton
- config
- migration
- `ExecutionJournalEntry` model
- `ExecutionJournalEntryData`
- ERN generator
- database sink
- basic recorder
- tests

---

### Slice 2 — Sinks and Structured Logging

Deliver:

- Monolog sink
- object storage sink
- sink manager
- config-driven sink enablement
- tests

---

### Slice 3 — Idempotency and Integrity

Deliver:

- idempotency support
- entry hashing
- optional previous hash
- integrity verification command
- tests

---

### Slice 4 — Renderers

Deliver:

- receipt renderer
- certificate renderer
- timeline renderer
- markdown/html/array formats
- tests

---

### Slice 5 — Statements

Deliver:

- statement snapshot model
- statement generator
- wallet/issuer/program statement types
- tests

---

### Slice 6 — x-change Integration

Deliver:

- x-change listeners/projectors
- Pay Code issued journal entry
- claim submitted journal entry
- disbursement completed journal entry
- timeline grouping by voucher/correlation ID
- tests

---

### Slice 7 — Settlement Envelope Integration

Deliver:

- envelope references journal ERNs
- envelope can include rendered records
- readiness checks create certificates
- tests

---

## 37. Architecture Invariants

1. The journal is append-only.
2. Corrections are new entries, not updates.
3. Monolog is a sink, not the source of truth.
4. RDBMS is the default canonical store.
5. MongoDB/search/SIEM are optional projections.
6. Object storage archive is optional but recommended.
7. Every entry must have an ERN.
8. Money-sensitive entries must support idempotency.
9. Human artifacts are rendered from journal entries.
10. Settlement envelopes consume journal entries; they do not own the journal.
11. `x-change` emits into `x-journal`; it should not duplicate journal logic.
12. The journal supports recovery, but does not replace backups.
13. Statements are frozen snapshots, not live reports.
14. Execution records are business-level records, not noisy application logs.
15. The package must remain useful outside `x-change`.

---

## 38. One-Sentence Summary

`3neti/x-journal` is the official execution journal package for the 3neti ecosystem, turning business-significant system events into immutable, human-auditable, machine-verifiable records that can produce receipts, certificates, statements, timelines, and settlement-envelope evidence.
