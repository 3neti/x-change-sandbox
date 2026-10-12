# IssuanceBatch implementation plan

## Objective

Allow a public user to upload a payroll or ayuda CSV/XLS/XLSX file to create many Pay Codes in one checkout. The application will validate and preview the rows, calculate one total, accept QR Ph or bank transfer funding, issue the rows after verified payment, and provide a durable status and download link.

## Scope

1. Add an `IssuanceBatch` domain in `x-change`, separate from Campaigns. A batch owns normalized items, totals, funding reference, lifecycle status, and generated Pay Code results.
2. Reuse the existing tabular safety rules and payment checkout lifecycle. Extract shared import behavior rather than making the public flow depend on Campaign models.
3. Support CSV, legacy XLS, and XLSX input with bounded file size, row count, column count, cell length, duplicate-header checks, and formula rejection.
4. Add a public `/x/auto-generate` “Create many now” flow: upload, review, fund, monitor, and download. The single-item flow remains unchanged.
5. Issue each item idempotently after funding. A retry resumes only unfinished items. The batch never issues more value than the verified funded total.
6. Persist the uploader mobile number as the checkout contact. Send one configurable SMS containing the batch status/download link after issuance; do not send one SMS per recipient in v1.
7. Provide signed, expiring download access and status information without exposing raw payment secrets or unclaimed Pay Code data to unrelated visitors.

## Delivery gates

- Domain models, migration, importer, and item-level state machine.
- Funding-order metadata and resumable batch issuance job.
- Public preview/create/status/download endpoints and UI wizard.
- Feature/unit tests for file safety, totals, idempotency, authorization, retry, and download expiry.
- Package tests and Pint; then synchronize the package into the host application and run the host test/build gates.

## Explicit v1 limits

- Payment methods: QR Ph and bank transfer.
- No maker-checker workflow; the uploader/order session controls the batch.
- SMS is only for the uploader’s completion/download link.
- A batch is bounded by configuration and cannot be silently accepted when any row is invalid.

## Distribution workspace extension

1. Reuse the package-owned `CockpitPayCodeResultsTable` for both the permanent Pay Codes cockpit and a temporary batch-scoped workspace at `/x/auto-generate/batches/{batch}/pay-codes`.
2. Add a sanitized, token-authorized batch read response containing only batch facts and item-level distribution fields: recipient, amount, status, Pay Code, claim URL, issue time, and failure message.
3. Keep the batch workspace isolated from the global Pay Code read model. Its source is `IssuanceBatchItem`, and its access token remains in browser session storage rather than the URL.
4. After successful batch issuance, offer `Open Pay Codes` and `Download CSV`; preserve the Claim URL column in the CSV.
5. Adapt batch rows into the shared table record contract so future table presentation changes propagate to both experiences without sharing global cockpit authorization or query logic.
