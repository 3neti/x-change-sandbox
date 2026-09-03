# Architecture Disposition Request: Make the Funding merchant profile canonical for QR Ph

Date: 2026-08-28 (Asia/Manila)  
Status: **For Chief Architect decision**  
Decision owner: Chief Architect  
Affected packages: `3neti/merchant`, `3neti/x-change`, `3neti/emi-core`, `3neti/emi-netbank`  
Primary surfaces: Cockpit Funding, Cockpit POS, `/x/pay/{code}`, Partner API payable issuance

## Executive summary

The merchant name and city configured on the Cockpit Funding page should become the canonical merchant identity for QR Ph generated for that Account's payable vouchers.

Today, X-Change already stores and renders an owner-linked merchant profile through `3neti/merchant`, and uses that profile when provisioning a reusable Funding QR. The payer flow is inconsistent: `/x/pay/{code}` creates a NetBank QR without supplying a merchant profile, so `3neti/emi-netbank` falls back to deployment-wide environment values such as `NETBANK_FUNDING_QR_MERCHANT_NAME` and `NETBANK_FUNDING_QR_MERCHANT_CITY`.

This means an operator can configure a merchant identity on Funding, yet a Maya or other QR Ph scanner can display a different deployment-level identity when paying that operator's payable voucher.

The requested disposition is:

> The active merchant profile exposed on the Funding page is the canonical owner-specific payment-acceptance identity. X-Change selects it from the payable voucher's authoritative owner, snapshots it into each Payment Attempt, and supplies it explicitly to the provider adapter. Deployment environment values remain commissioning fallbacks only.

The merchant profile's domain semantics belong in `3neti/merchant`; X-Change owns the binding and orchestration; EMI packages own provider-neutral transport data and NetBank encoding.

## Decision requested

Please approve, amend, or reject the following decisions:

1. **Canonical source:** the active `3neti/merchant` profile associated with the payable voucher owner is the canonical QR merchant identity.
2. **Funding authority:** the Funding page edits that canonical profile; it does not maintain a second QR-only copy.
3. **Identity distinction:** an optional internal Funding-address nickname remains separate and must never be encoded as the QR merchant name.
4. **Selection time:** X-Change resolves the profile when creating a Payment Attempt and freezes a sanitized snapshot for that attempt.
5. **Fallback policy:** environment merchant name/city are used only for explicitly commissioned legacy/system flows; ordinary owner-specific payable QR generation fails closed if no valid profile can be resolved.
6. **Package ownership:** `3neti/merchant` owns profile identity, validation, lifecycle, and revision semantics; `3neti/x-change` owns owner resolution, authorization, attempt snapshots, Cockpit projections, and journal evidence; `3neti/emi-netbank` only maps the supplied identity into provider fields.
7. **Governance wave:** mutable merchant rows may support an initial controlled wiring wave, but production canonicality ultimately requires immutable revisions or equivalent versioned evidence in `3neti/merchant`.

## Current behavior

### Funding page

The Funding page reads and updates an owner-linked `Merchant` through `MerchantProfileRepositoryContract`:

- [`FundingQrMerchantProfileReadModel.php`](/Users/rli/PhpstormProjects/packages/x-change/src/Services/Cockpit/FundingQrMerchantProfileReadModel.php)
- [`CockpitFundingQrMerchantProfileController.php`](/Users/rli/PhpstormProjects/packages/x-change/src/Http/Controllers/Web/Cockpit/CockpitFundingQrMerchantProfileController.php)
- [`UpdateCockpitFundingQrMerchantProfileRequest.php`](/Users/rli/PhpstormProjects/packages/x-change/src/Http/Requests/Web/Cockpit/UpdateCockpitFundingQrMerchantProfileRequest.php)

The editable fields are:

- merchant name;
- merchant city;
- merchant category code; and
- merchant-name rendering template.

The request already enforces the current QR constraints: the rendered name must fit within 25 characters, city within 15 characters, and category code is four digits.

The same profile is already resolved into `FundingQrMerchantData` for reusable Funding QR provisioning:

- [`FundingQrMerchantProfileResolver.php`](/Users/rli/PhpstormProjects/packages/x-change/src/Services/Funding/FundingQrMerchantProfileResolver.php)
- [`GenerateNetbankReusableFundingAddress.php`](/Users/rli/PhpstormProjects/packages/x-change/src/Actions/Funding/GenerateNetbankReusableFundingAddress.php)

The Funding UI currently labels this profile `presentation_only=true` and `controls_settlement=false`. That description is no longer correct if the profile becomes the canonical payer-visible identity.

### Payable payment page

The provisional NetBank payer issuer calls `generateQrCode()` without a `FundingQrMerchantData` argument:

- [`ProvisionalNetbankPayerInstructionIssuer.php`](/Users/rli/PhpstormProjects/packages/x-change/src/Services/Payment/ProvisionalNetbankPayerInstructionIssuer.php)

Because no profile is supplied, `NetbankFundingApiClient` uses provider configuration:

- [`NetbankFundingApiClient.php`](/Users/rli/PhpstormProjects/packages/x-change/vendor/3neti/emi-netbank/src/Funding/NetbankFundingApiClient.php)
- [`payment-gateway.php`](/Users/rli/PhpstormProjects/packages/x-change/vendor/3neti/emi-netbank/config/payment-gateway.php)

The effective fallback variables are:

- `NETBANK_FUNDING_QR_MERCHANT_NAME`;
- `NETBANK_FUNDING_QR_MERCHANT_CITY`.

In the current testing environment these resolve to `X Change Treasury` and `Makati`. They are deployment-wide, not voucher-owner-specific.

### Existing merchant package

`3neti/merchant` already owns the relevant record and repository:

- [`Merchant.php`](/Users/rli/PhpstormProjects/packages/merchant/src/Models/Merchant.php)
- [`MerchantProfileRepositoryContract.php`](/Users/rli/PhpstormProjects/packages/merchant/src/Contracts/MerchantProfileRepositoryContract.php)
- [`EloquentMerchantProfileRepository.php`](/Users/rli/PhpstormProjects/packages/merchant/src/Services/EloquentMerchantProfileRepository.php)
- [`MerchantDisplayNameRenderer.php`](/Users/rli/PhpstormProjects/packages/merchant/src/Services/MerchantDisplayNameRenderer.php)

The existing profile is owner-associated through `merchant_user`, includes an active flag, and contains the name, city, category code, and rendering template. It is currently mutable and does not provide an immutable revision history.

## Problem statement

There are presently two merchant-identity authorities:

| Context | Current authority | Scope |
| --- | --- | --- |
| Reusable Funding QR | Owner-linked `3neti/merchant` profile | Per Account owner |
| Payable-voucher QR Ph | NetBank environment variables | Entire deployment |

This produces four material problems:

1. **Payer-visible inconsistency:** the Funding page can show one merchant identity while the scanner displays another.
2. **Multi-merchant failure:** a deployment-wide value cannot truthfully represent several operators or merchants issuing payable vouchers.
3. **Audit ambiguity:** Payment Attempt evidence does not identify which merchant-profile version produced the provider QR.
4. **Operator surprise:** updating the Funding merchant profile does not affect the payable QR surface where the merchant expects it to apply.

## Proposed canonical model

### Product vocabulary

The Funding surface should distinguish two concepts:

| Field | Purpose | Provider-visible |
| --- | --- | --- |
| **Merchant name** | Canonical payment-acceptance identity | Yes |
| **Merchant city** | Canonical QR merchant city | Yes |
| **Merchant category** | Payment classification | Where supported |
| **Funding-address nickname** | Internal operator label such as “Main collections” | No |

The current Funding merchant name is promoted from presentation configuration to canonical payment identity. A casual nickname must be stored and displayed separately rather than overloaded into the merchant name.

### Source and flow

```text
3neti/merchant
active owner-linked merchant profile/revision
        |
        v
3neti/x-change
resolve from payable voucher owner
authorize + snapshot on Payment Attempt
        |
        v
3neti/emi-core
provider-neutral FundingQrMerchantData
        |
        v
3neti/emi-netbank
validate limits + encode NetBank QR Ph request
```

The public payer is not the merchant authority. The profile is resolved from the payable voucher's authoritative owner/issuer, never from request input, payer identity, browser session, purpose text, or sale reference.

### Snapshot semantics

Every successful Payment Attempt instruction issuance should retain a sanitized immutable snapshot containing at least:

- merchant profile reference;
- merchant profile revision or version;
- rendered merchant name;
- merchant city;
- merchant category code when used;
- profile fingerprint;
- metadata schema version; and
- resolution source (`merchant_profile` or explicitly permitted `commissioning_fallback`).

The QR request and the stored snapshot must be produced from the same resolved value object. The snapshot must exclude owner PII, provider credentials, raw provider responses, account numbers, mobile numbers, and secrets.

Once a Payment Attempt has instructions, editing the Funding merchant profile must not rewrite that attempt. A new attempt uses the then-current active profile revision. Exact idempotent replay returns the original snapshot and QR result.

## Package responsibilities

### `3neti/merchant`

Own:

- canonical merchant identity fields;
- profile activation/suspension state;
- validation and normalized rendering policy;
- owner-to-profile authority relationship;
- immutable revision/version semantics;
- provider-neutral profile reference and snapshot DTO/contract.

Must not know:

- Pay Codes or Voucher models;
- Funding addresses;
- NetBank credentials or request shapes;
- Payment Attempt routes;
- Cockpit page structure.

### `3neti/x-change`

Own:

- resolving the voucher's authoritative owner;
- selecting the active merchant profile;
- authorization for Funding-page edits;
- binding merchant identity to Funding and Payment Attempt workflows;
- storing the immutable attempt snapshot;
- showing “Payer will see …” in Cockpit Funding/POS/payment previews;
- journal/outbox evidence;
- fallback and readiness policy.

X-Change must not accept a client-supplied merchant name or city as authoritative payment data on payable issuance or payment-attempt endpoints.

### `3neti/emi-core`

Own:

- provider-neutral merchant QR data such as `FundingQrMerchantData`;
- stable validation vocabulary required by EMI adapters.

It must not select the merchant profile.

### `3neti/emi-netbank`

Own:

- NetBank-specific length/character validation;
- translation to `merchant_name`, `merchant_city`, and supported category fields;
- QR generation/provider transport;
- use of fallback configuration only when the caller explicitly permits or omits identity under an approved legacy policy.

It must not query X-Change users, Merchants, Vouchers, or Funding addresses.

## Recommended implementation gates

### Gate 1 — canonical resolver adoption

1. Generalize or rename `FundingQrMerchantProfileResolver` so it represents payment-acceptance identity, not only reusable Funding QR.
2. Resolve the profile from the payable voucher owner in the Payment Attempt instruction path.
3. Pass the resulting `FundingQrMerchantData` explicitly to `NetbankFundingApiClient::generateQrCode()`.
4. Remove the Funding read-model flags that claim the profile is merely presentational.
5. Add a payer-visible preview in Cockpit: `Payer will see: {rendered name} · {city}`.

This gate may reuse the current mutable Merchant row, but it must snapshot the resolved facts onto the Payment Attempt.

### Gate 2 — fallback and readiness policy

Introduce an explicit policy with fail-closed defaults:

- owner-specific payable voucher + active profile: use profile;
- missing/inactive/ambiguous profile: reject instruction creation with a stable, safe domain error;
- commissioned system/legacy flow: environment fallback only when explicitly allowed;
- never silently fall back because a database query, binding, or profile resolver failed.

Doctor/commissioning should report whether canonical merchant identity is ready. It should disclose only sanitized profile readiness—not names, IDs, or owner data.

### Gate 3 — merchant-profile revisions

Add an append-only profile revision mechanism in `3neti/merchant` or an equivalent immutable version boundary:

- maker/checker requirements if the identity is regulated or production-facing;
- effective time and active head;
- normalized snapshot fingerprint;
- unique version/reference;
- no update/delete of activated revisions;
- deterministic idempotency for profile changes.

Existing mutable records should be imported as version 1 through a forward migration or controlled commissioning action. Do not mutate deployed migrations.

### Gate 4 — historical and in-flight behavior

- Existing generated Payment Attempts retain their current merchant snapshot/fallback facts.
- Existing payable vouchers without an attempt resolve the active profile only when their first new attempt is created, unless the architect requires issuance-time binding.
- Existing reusable Funding QR artifacts are not overwritten silently; regeneration follows their existing governed artifact lifecycle.
- Changing a profile does not reattribute settled transactions.
- In-flight attempts continue using their original identity through expiry, verification, settlement, and reconciliation.

### Gate 5 — Cockpit clarity

Funding should present:

```text
Merchant identity
X Change Treasury · Makati
Used on reusable Funding QR and payable-voucher QR Ph

Funding-address nickname
Main collections
Visible only to operators
```

The editor must explain that changes affect newly generated QR instructions, not already generated or settled attempts. POS should preview the identity before issuing a payable voucher. Pay Code Detail and Payment Attempt views should show only the sanitized frozen identity—not mutable live profile data.

## Authorization and governance

The current Funding request authorizes any authenticated user. Canonical payment identity is a stronger authority than presentation customization. Before production adoption, the architect should decide:

- whether an Account owner may edit their own merchant identity directly;
- whether verified business/KYC facts constrain the name and city;
- whether production changes require maker/checker approval;
- whether category-code changes require separate authority;
- whether Partner API clients may select among commissioned profiles or must use one bound profile;
- whether one user may own multiple merchant profiles and, if so, how the voucher selects one without accepting arbitrary client authority.

Recommended default: one active canonical profile per merchant authority and currency/provider acceptance context; profile selection is server-side. Any future multi-profile selector should accept only an opaque commissioned profile reference and repeat server authorization.

## Invariants

1. The payable voucher owner—not the payer—determines merchant identity.
2. Funding and payable QR generation consume the same active canonical profile.
3. An internal Funding nickname is never sent to NetBank.
4. Client payloads cannot override merchant name, city, category, fingerprint, or profile reference.
5. Provider adapters receive a complete, validated value object; they do not resolve domain ownership.
6. Every issued QR is traceable to a frozen identity snapshot and fingerprint.
7. Idempotent replay cannot change merchant identity or call the provider twice.
8. Updating a profile affects only new instructions after activation.
9. Missing/inactive/ambiguous profiles fail closed unless a specific commissioned fallback policy applies.
10. Environment values are installation bootstrap/fallback facts, not ordinary multi-tenant merchant truth.
11. Journal and read models expose no provider credentials, raw payloads, owner PII, or account identifiers.
12. Merchant-profile changes never mutate historical Payment Attempts or settled collection evidence.

## Required tests

### `3neti/merchant`

- profile rendering and QR constraints;
- active/inactive resolution;
- owner authority and cross-owner concealment;
- revision activation, immutability, and deterministic fingerprint;
- equivalent normalized inputs produce the same snapshot fingerprint;
- unauthorized updates fail without changing the active profile.

### `3neti/x-change`

- Funding read model and payable Payment Attempt resolve the same owner profile;
- `/x/pay/{code}` supplies that exact profile to the provider boundary;
- payer/authenticated browser input cannot override it;
- cross-owner voucher/profile substitution fails closed;
- missing/inactive/ambiguous profile behavior follows the approved fallback policy;
- existing attempt replay preserves the original merchant snapshot after profile changes;
- a new attempt after profile activation uses the new revision;
- Cockpit preview matches the QR request snapshot;
- internal Funding nickname never appears in provider input or public read models;
- journal evidence contains sanitized profile reference/version/fingerprint once.

### `3neti/emi-netbank`

- explicit merchant data overrides deployment fallback;
- name/city limits and provider-supported characters are enforced deterministically;
- omitted merchant data uses fallback only under the approved adapter contract;
- generated request contains the exact supplied name and city;
- provider logs and errors do not leak sensitive data.

### Acceptance

1. Configure a Funding merchant profile for Merchant A.
2. Issue a payable voucher owned by Merchant A.
3. Create one QR Ph Payment Attempt.
4. Decode/inspect the provider request and confirm the Funding merchant name/city.
5. Change Merchant A's active profile.
6. Replay the original attempt and confirm its identity is unchanged and no second provider call occurs.
7. Create a new attempt and confirm the new profile appears.
8. Issue for Merchant B and prove Merchant A's identity is never selected.

No real payment is required for this acceptance; provider calls should be faked locally. A later controlled Cloud check may create one test instruction only under explicit authorization.

## Options considered

### Option A — keep environment variables canonical

Rejected for multi-merchant operation. It is simple but contradicts the owner-specific Funding profile and cannot provide truthful per-merchant QR identity.

### Option B — copy Funding fields into X-Change configuration

Rejected. This creates another mutable source of truth and bypasses `3neti/merchant` ownership.

### Option C — use the Funding-address nickname as merchant name

Rejected. Nicknames are operator vocabulary and may be informal, duplicated, or unsuitable for payer display and QR constraints.

### Option D — use the owner-linked `3neti/merchant` profile, orchestrated by X-Change

**Recommended.** It reuses the existing domain seam, supports multiple merchants, and makes the Funding page and payable QR consistent while keeping NetBank provider-specific behavior out of the Merchant package.

## Proposed disposition

Approve Option D with a two-wave delivery:

1. **Controlled canonical-wiring wave:** use the existing active merchant profile for both Funding and payable QR generation, snapshot it on Payment Attempts, introduce explicit fallback policy, update Cockpit language, and add focused regression coverage.
2. **Governance wave:** introduce immutable merchant-profile revisions and the approved production authorization workflow in `3neti/merchant`, then migrate existing profiles through a forward-only controlled action.

Do not solve the inconsistency by changing only the deployment environment variables. That would make one installation label look correct while preserving the underlying split authority.

## Decision record

Chief Architect disposition:

- [ ] Approved as proposed
- [ ] Approved with amendments
- [ ] Rejected
- [ ] Deferred pending merchant/KYC governance design

Required amendments or rationale:

_To be completed by the decision owner._

## Scope boundary

This document is a disposition request only. It does not authorize code changes, database migrations, package releases, Cloud configuration changes, QR generation, payment attempts, provider calls, or financial mutations.
