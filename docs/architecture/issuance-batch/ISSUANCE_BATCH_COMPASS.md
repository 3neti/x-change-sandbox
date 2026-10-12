# IssuanceBatch compass

## North star

An uploaded list becomes a reviewable, paid, resumable issuance batch. Every amount is visible before payment, every Pay Code is traceable to an input row, and a transient request cannot create duplicate value.

## Invariants

- **Preview before charge:** no funding order is created until the normalized row summary and total are accepted.
- **One source of truth:** the batch owns row status and totals; the checkout owns payment state; the funding order links them.
- **Bounded value:** the verified payment must cover the exact batch total and issuance never exceeds it.
- **Idempotent progress:** each row has a stable ordinal/fingerprint and can be retried without a duplicate Pay Code.
- **Least exposure:** signed status/download access is scoped to the batch session and expires.
- **Recoverable failure:** payment remains protected while failed rows or provider errors are visible and retryable.
- **Package first:** domain and reusable orchestration live in `x-change`; the host supplies presentation, configuration, and deployment wiring.

## Flow

`upload → validate/normalize → preview → create batch → checkout/fund → verify payment → issue rows → notify uploader → download results`

Campaign imports may later create the same batch records. The public auto-generate flow is the first producer; Campaigns are not the owner of the batch domain.

## Decisions requiring evidence

Record provider reconciliation evidence, row-level failures, retries, and download access in the existing journal/feedback surfaces. Do not hide a partial batch behind a generic success message.
