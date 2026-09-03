# Architect bug report: Payable public entry, Stamp, and Cockpit projection disagree

Date: 2026-08-26 (Asia/Manila)  
Environment: Laravel Cloud testing  
Observed Pay Code: `4A4E`  
Installed package during investigation: `3neti/x-change v1.0.0-beta.267`  
Severity: **P1 — issued financial instrument is distributed with the wrong public authority and a broken finalized artifact**

## Executive summary

Payable issuance succeeds, but the resulting public presentation still follows the outward-claim contract:

- the issued link and share QR point to `/x/claim/{code}`;
- the durable finalized Stamp is materialized with that claim URL;
- the advertised claim share-card endpoint rejects Payable vouchers and returns `404`;
- the Cockpit Detail calls the Payable voucher `Claimable`, offers an `Open claim URL`, and shares a claim QR;
- a separate `/x/pay/{code}` URL exists and renders the correct collection landing page, but it is treated as a secondary action instead of the canonical public entry.

The public claim controller is behaving correctly when it refuses the Payable voucher. The defect is that issuance, artifact generation, post-issuance presentation, distribution, and the Cockpit read model do not consume the same capability-aware public-entry decision.

## Live browser evidence

All checks were read-only. No payment attempt was created, no payment was submitted, and no provider action was invoked by this investigation.

### 1. Claim route correctly refuses the Payable voucher

[Open `/x/claim/4A4E`](https://x-change-testing-testing-uw1gvj.laravel.cloud/x/claim/4A4E)

Observed:

- heading: `Unable to Claim`
- message: `This Pay Code accepts payment and cannot be claimed.`
- code: `4A4E`

This is the correct domain decision for a collection-only instrument.

### 2. Payment route resolves and exposes the correct amount due

[Open `/x/pay/4A4E`](https://x-change-testing-testing-uw1gvj.laravel.cloud/x/pay/4A4E)

Observed:

- heading: `Pay 4A4E`
- amount due: `₱1.00`
- action: `Create exact QR Ph payment`
- disclosure: creating instructions does not mark the Pay Code paid; NetBank history must confirm settlement.

The GET page is working. It intentionally does not create provider instructions until the payer presses the action. This investigation did not press it, so a reported failure after that action remains a separate provider/payment-attempt incident requiring its own evidence.

### 3. Advertised Stamp/share-card route rejects the same voucher

[Open `/x/claim/4A4E/share-card.png`](https://x-change-testing-testing-uw1gvj.laravel.cloud/x/claim/4A4E/share-card.png)

Observed: `404 Not Found`.

The immutable artifact route uses the same controller and therefore has the same capability rejection. This explains the blank/non-rendered finalized Stamp when Cockpit prefers the advertised artifact URL.

### 4. Cockpit Detail contradicts the authoritative claim route

[Open Cockpit Detail for 4A4E](https://x-change-testing-testing-uw1gvj.laravel.cloud/x/cockpit/pay-codes/4A4E)

Observed:

- workspace classification: `Collection`
- lifecycle status: `Active`
- availability: `Claimable`
- primary action: `Open claim URL` → `/x/claim/4A4E`
- share text: `Claim Pay Code`
- share QR points to `/x/claim/4A4E`
- `Target Value`: `₱1.00`
- `Pay Code Value`: `₱10.00`
- `Reserved Principal`: `₱10.00`
- recipient: `Open claim`
- terminal action copy says it can return the reserved principal.

At minimum, the claimability, public link, share language, and share QR are wrong for Payable. The coexistence of a `₱1.00` collection target and `₱10.00` reserved outward principal should be reviewed as a related economic-projection concern rather than silently normalized in the UI.

## Confirmed code causes

### A. Issuance unconditionally creates an outward claim URL

[`PayCodeIssuanceService.php:56`](/Users/rli/PhpstormProjects/packages/x-change/src/Services/PayCodeIssuanceService.php:56) always calls `redeemPath()`. [`redeemPath()` at line 130`](/Users/rli/PhpstormProjects/packages/x-change/src/Services/PayCodeIssuanceService.php:130) selects `x-change.claim.show` whenever that route exists; it never examines voucher capabilities.

The service then:

1. materializes the durable Stamp with that claim URL at [`PayCodeIssuanceService.php:59`](/Users/rli/PhpstormProjects/packages/x-change/src/Services/PayCodeIssuanceService.php:59); and
2. returns it as `links.redeem` / `links.redeem_path` at [`PayCodeIssuanceService.php:67`](/Users/rli/PhpstormProjects/packages/x-change/src/Services/PayCodeIssuanceService.php:67).

Therefore the wrong URL is durable before Cockpit builds its response.

### B. Cockpit emits two contradictory public destinations

[`CockpitQuickGenerateMutationRouteShellController.php:364`](/Users/rli/PhpstormProjects/packages/x-change/src/Http/Controllers/Web/Cockpit/CockpitQuickGenerateMutationRouteShellController.php:364) returns:

- `redeem` and `redeem_path` from the unconditional claim result;
- `claim_qr` rendered from that claim URL;
- a claim share-card URL; and
- a separate `payment` URL for `/x/pay/{code}`.

The finalized dialog continues to use the claim URL/QR for the Stamp and share actions. The payment URL appears only as a secondary link.

### C. Claim share-card controller is correctly disbursement-only

[`ClaimShareCardController.php:27`](/Users/rli/PhpstormProjects/packages/x-change/src/Http/Controllers/Web/Claim/ClaimShareCardController.php:27) requires `can_disburse`. A Payable voucher resolves to `can_collect`, so both mutable and immutable claim-card routes return `404`.

This guard should not simply be weakened. A collection Stamp needs the correct public-entry URL, terminology, and QR semantics.

### D. Finalized dialog has no failed-artifact fallback

[`CockpitIssuedPayCodeDialog.vue:385`](/Users/rli/PhpstormProjects/packages/x-change/resources/js/cockpit/components/CockpitIssuedPayCodeDialog.vue:385) prefers `shareCardUrl`. Its image has no error handler that falls back to the client-rendered canvas. A `404` therefore produces a blank Stamp while still showing the enlarge affordance.

### E. Capability-aware resolvers use a third URL dialect

The codebase already has capability-aware services, but both concatenate raw paths rather than named package routes:

- [`DefaultPayCodePresentationResolver.php:24`](/Users/rli/PhpstormProjects/packages/x-change/src/Services/DefaultPayCodePresentationResolver.php:24)
- [`DefaultVoucherPaymentQrGenerator.php:27`](/Users/rli/PhpstormProjects/packages/x-change/src/Services/DefaultVoucherPaymentQrGenerator.php:27)

For a collection flow they can produce `/pay/{code}`, while the actual package route is `/x/pay/{code}`. These services are not the authority currently used by issuance and should not be adopted unchanged.

## redeem-x comparison

redeem-x is useful as a UX reference, not as production code to transplant.

- [`redeem-x/routes/pay.php:19`](/Users/rli/PhpstormProjects/redeem-x/routes/pay.php:19) exposes `/pay?code={code}` with separate POST quote and QR endpoints.
- [`PayVoucherController.php:45`](/Users/rli/PhpstormProjects/redeem-x/app/Http/Controllers/Pay/PayVoucherController.php:45) validates the voucher through `canAcceptPayment()` and returns remaining-payment facts.
- [`PayVoucherController.php:176`](/Users/rli/PhpstormProjects/redeem-x/app/Http/Controllers/Pay/PayVoucherController.php:176) labels QR creation as TODO and currently returns a mock QR response.

x-change's current `/x/pay/{code}` design is safer and more mature: it capability-checks the voucher, exposes a read-only landing page, creates a session-bound durable Payment Attempt only after an explicit payer action, and requires provider history to confirm settlement. Keep that architecture.

## Recommended architectural disposition

### Gate 1 — one canonical public-entry decision

Introduce one capability-aware resolver/data contract used by issuance and every projection. Suggested vocabulary:

- `public_entry_url`
- `public_entry_path`
- `public_entry_kind` = `claim | payment | settlement`
- `public_entry_qr_kind` = `claim_url | payment_url | hybrid`

Named-route mapping:

- disbursable → `x-change.claim.show`
- collectible → `x-change.pay.show`
- settlement → explicitly approved settlement entry, not an inferred alias

Do not force inward collection through fields named `redeem`.

### Gate 2 — make Stamp artifacts capability-aware

- Resolve the canonical public entry before artifact materialization.
- Persist the public-entry kind and URL hash in the artifact descriptor/evidence.
- Add a generic or payment-specific artifact route instead of routing collection artifacts through `ClaimShareCardController`.
- Render payment language and a static URL QR for `/x/pay/{code}`.
- Keep the provider-generated QR Ph payload separate; it is short-lived Payment Attempt evidence, not the permanent Stamp QR.

For already-issued vouchers such as `4A4E`, do not silently overwrite immutable artifact evidence. Define a governed, idempotent forward repair or versioned artifact replacement after the canonical contract is approved.

### Gate 3 — repair Cockpit projections and sharing

For `public_entry_kind=payment`:

- availability should communicate `Awaiting payment`, `Partially paid`, or `Paid`, not `Claimable`;
- primary action should be `Open payment page`;
- share text should say `Pay Pay Code`, not `Claim Pay Code`;
- share QR and copied link should use `/x/pay/{code}`;
- Claim & Evidence presentation should not imply an outward beneficiary.

Add an image-load fallback so a missing server artifact renders the safe client canvas and visibly reports degraded artifact readiness.

### Gate 4 — review economic projection separately

The live read model shows target `₱1.00` alongside Pay Code value/reserved principal `₱10.00`. The architect should determine whether this is:

- legitimate compatibility evidence;
- a stale pre-zero-funding voucher shape; or
- an active reservation defect.

Do not repair the display without tracing the underlying Treasury reservation and issuance instruction authority.

### Gate 5 — diagnose provider instructions without conflating it with routing

The GET `/x/pay/4A4E` works. If `Create exact QR Ph payment` fails, capture the existing Payment Attempt/provider failure evidence and diagnose `IssuePaymentInstructions` independently. Do not create repeated attempts speculatively and do not make provider success a prerequisite for fixing the permanent public URL and Stamp.

## Required regression gates

1. Payable Quick Generate returns `/x/pay/{code}` as the canonical public entry and never presents `/x/claim/{code}` as the payer destination.
2. Redeemable Quick Generate remains on `/x/claim/{code}`.
3. The persisted Payable Stamp QR decodes to the exact `/x/pay/{code}` URL.
4. The Payable artifact endpoint returns a valid PNG and is capability-authorized.
5. Missing/failed artifact loading falls back visibly without a blank Stamp.
6. Payable Cockpit Detail says `Awaiting payment`/collection status and exposes only the payment entry.
7. Share/SMS/email/WhatsApp payloads use payment language and the payment URL.
8. The public claim route continues to refuse Payable vouchers.
9. The public payment GET remains read-only; QR Ph creation remains an explicit, session-bound action.
10. Tests cover exact/partial/fully-paid projections and no leakage of provider instruction payloads.

## Investigation boundaries

- No voucher, payment attempt, Treasury row, provider state, or Cloud configuration was mutated.
- The provider QR action was deliberately not pressed.
- This report recommends disposition only; it does not authorize implementation, repair, release, or deployment.
