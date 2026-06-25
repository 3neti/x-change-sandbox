# Codex Instruction: x-change Cockpit UI/UX Discovery, Porting, and Scaffold

## Mission

You are working on the `x-change` package UI/UX.

The objective is to begin the **x-change Cockpit** initiative.

The Cockpit is the future primary **operator UI** for x-change.

The Claim Journey remains separate.

```text
Cockpit manages.
Claim UI executes.
```

The Cockpit should become the primary interface for:

- dashboards
- quick generation
- funding
- Pay Code exploration
- voucher/batch monitoring
- template-driven issuance
- distribution
- evidence review
- execution monitoring
- operational reporting

Do not treat this as a generic admin panel.

The Cockpit should feel like a:

- treasury workstation
- financial operations center
- execution platform console
- bank-grade Pay Code cockpit

---

# Core Product Identity

x-change is an **Execution Platform**.

The Pay Code is the execution instrument.

The Cockpit is the operator command center.

The Claim Journey is the claimant/redeemer experience.

---

# Critical Repository Boundaries

## Target of Work

All productized updates must be made inside:

```text
/Users/rli/PhpstormProjects/x-change-sandbox/packages/x-change
```

Do not scaffold Cockpit work inside the host app.

Do not modify `redeem-x` except for inspection/reference.

Do not create a new package such as `packages/cockpit` at this stage.

Use a Cockpit namespace inside the existing x-change package.

Recommended structure:

```text
resources/js/cockpit/
  components/
  pages/
  layouts/
  widgets/
  composables/
  types/

tests/frontend/cockpit/
```

If the current project conventions strongly favor the existing `components` and `pages` folders, you may adapt, but preserve a clear `cockpit` domain identifier.

Acceptable alternative:

```text
resources/js/components/cockpit/
resources/js/pages/cockpit/
tests/frontend/cockpit/
```

Choose the structure that best fits the package conventions after inspection.

---

# Source Priority Order

Inspect sources in this order.

## 1. Current x-change package

```text
/Users/rli/PhpstormProjects/x-change-sandbox/packages/x-change
```

This is the current productization baseline.

The current UI in this package is already satisfactory and should be treated as the starting point, not something to discard casually.

## 2. Current x-change package resources

```text
/Users/rli/PhpstormProjects/x-change-sandbox/packages/x-change/resources
```

This contains current Claim UI/UX scaffolding.

The current Claim UI/UX flow is mature and frontend-tested.

Pay special attention to:

- Claim UI/UX
- Paynamics OTP approval UX
- rider message
- splash experience
- URL redirect behavior
- current Vue components
- current Inertia pages
- current frontend tests

## 3. Package ecosystem

```text
/Users/rli/PhpstormProjects/packages
```

Use this for domain contracts and package context.

Important packages may include:

- voucher
- settlement-envelope
- onboarding
- merchant
- wallet
- contact
- form-flow
- related x-change dependencies

## 4. redeem-x prior art

```text
/Users/rli/PhpstormProjects/redeem-x
```

Use `redeem-x` as historical/reference prior art.

It may contain useful ideas for:

- older claim/disburse flows
- voucher evidence display
- QR/copy/share behavior
- OG tag experiments
- PWA patterns
- social sharing
- voucher listing
- earlier UX experiments

Do not treat `redeem-x` as canonical over current `x-change`.

---

# Source-of-Truth Rules

## Current x-change package resources are stronger than redeem-x for Claim UX

The Claim UI/UX in x-change package resources is the current baseline.

Use redeem-x only as historical/prior-art reference when x-change lacks an equivalent capability.

## Protected UX Assets

Before redesigning or replacing anything, inspect and preserve the following protected assets:

- Claim Journey
- current frontend-tested claim flow
- Paynamics OTP approval UX
- rider message UX
- splash UX
- URL redirect UX
- existing Vitest frontend tests

These should be:

```text
Preserved → Promoted → Enhanced → Created
```

in that order.

Do not rewrite mature tested flows from scratch unless there is a documented reason.

---

# Tooling Expectations

Laravel Boost is available and has already been used successfully in this project.

Use Laravel Boost as the primary Laravel inspection mechanism.

Use it to inspect:

- routes
- controllers
- requests
- service providers
- bindings
- models
- actions
- Inertia pages
- Vue resources
- tests
- package structure

Use normal Git commands when helpful:

```bash
git status
git diff
git log
git branch
```

Git history may help, but the current source code and tests are more important than historical commits.

Use repository search and filesystem inspection as needed.

---

# Required Discovery Before Implementation

Do not begin broad UI scaffolding immediately.

First produce discovery documentation under:

```text
docs/ui-cockpit/
```

Create or update the following:

```text
docs/ui-cockpit/COMPASS.md
docs/ui-cockpit/reports/000-source-discovery.md
docs/ui-cockpit/reports/001-source-of-truth-matrix.md
docs/ui-cockpit/reports/002-porting-map.md
docs/ui-cockpit/reports/003-scaffold-plan.md
```

---

# COMPASS.md Requirement

Maintain a living project compass.

At any point, a human should be able to open:

```text
docs/ui-cockpit/COMPASS.md
```

and understand:

- current objective
- completed slices
- current work
- next work
- risks
- decisions
- open questions
- test status

Suggested structure:

```markdown
# x-change Cockpit Compass

## Current Objective

## Completed

## In Progress

## Next

## Risks

## Decisions

## Open Questions

## Test Status

## Last Updated
```

Update the compass after every meaningful slice.

---

# Discovery Report Requirements

## 000-source-discovery.md

Document what currently exists in:

- x-change package resources
- x-change package tests
- redeem-x prior art
- package ecosystem

For each relevant feature, include:

- file path
- purpose
- current maturity
- test coverage
- recommendation

## 001-source-of-truth-matrix.md

Create a matrix like:

```markdown
| Capability | Current Source | Maturity | Recommendation |
|---|---|---|---|
| Claim UI flow | x-change resources | tested | preserve/promote |
| Paynamics OTP approval UX | x-change resources | tested | preserve/promote |
| Rider message | x-change resources | mature | enhance |
| Splash | x-change resources | mature | enhance |
| URL redirect | x-change resources | mature | enhance |
| OG/share prior art | redeem-x | prior art | adapt |
| Voucher evidence display | redeem-x / x-change if present | inspect | enhance |
| PWA sharing | redeem-x | prior art | adapt |
```

## 002-porting-map.md

Classify each capability as:

```text
Preserve
Promote
Enhance
Create
Defer
```

## 003-scaffold-plan.md

Propose the first coding slices.

Each slice must be:

- coherent
- small enough to review
- testable
- reversible
- commit-worthy

---

# UI/UX Design Documents

The following design documents are the guiding references for this initiative:

```text
docs/ui-cockpit/01-product-principles.md
docs/ui-cockpit/02-information-architecture.md
docs/ui-cockpit/03-navigation-model.md
docs/ui-cockpit/04-screen-layouts.md
docs/ui-cockpit/05-ux-flows.md
docs/ui-cockpit/06-widget-catalog.md
docs/ui-cockpit/07-design-language.md
docs/ui-cockpit/08-role-based-experiences.md
docs/ui-cockpit/09-mobile-and-pwa-strategy.md
docs/ui-cockpit/10-ai-copilot-strategy.md
```

If these documents are not yet present in the repository, create them using the provided project discussion/specification.

Do not treat these documents as implementation checklists.

Treat them as product compass documents.

---

# Major Product Decisions Already Made

The following decisions are locked unless the human changes them.

## Cockpit role

```text
Cockpit becomes the primary operator UI.

Claim journey remains separate.

Cockpit manages.
Claim UI executes.
```

## Migration strategy

```text
New cockpit pages.
Old pages remain.
Gradual migration.
```

Do not replace all existing pages in one sweep.

## Balance truth

```text
Internal Wallet = operational truth.
Live Balance = external confirmation.
```

Show variance when both are present.

## Reconciliation scope

Initial scope:

```text
Reconciliation dashboard only.
```

Do not build a full reconciliation engine/workspace in the first Cockpit slice.

## Beneficiary/contact model

For batch workflows:

```text
Beneficiary Registry first.
Promote to Contact later.
```

Do not force every claimant into full contact management immediately.

## Distribution analytics scope

Distribution analytics are operational reporting.

Do not build marketing-grade analytics.

## AI scope

Initial AI target:

```text
Natural-language search first.
```

Do not implement autonomous AI money movement.

## Product identity

x-change is an:

```text
Execution Platform
```

The Cockpit should reflect execution, funding, settlement, evidence, distribution, and audit.

## Feature Profiles

Feature Profiles are first-class.

They are not tenants.

They are experience dictionaries similar to localization.

They may control:

- terminology
- navigation labels
- dashboard composition
- visible features
- default templates
- branding
- workspace defaults

They must not control:

- data isolation
- domain behavior
- voucher lifecycle
- ledger semantics
- execution engine behavior
- authorization rules

Feature Profiles influence presentation.

Feature Profiles do not create multi-tenancy.

---

# Feature Profile Direction

Treat Feature Profiles like localization plus product experience configuration.

Potential structure:

```text
resources/profiles/
  default/
    terminology.php
    navigation.php
    dashboard.php
    features.php
  bank/
    terminology.php
    navigation.php
    dashboard.php
    features.php
  philhealth/
    terminology.php
    navigation.php
    dashboard.php
    features.php
  lgu/
    terminology.php
    navigation.php
    dashboard.php
    features.php
  money_changer/
    terminology.php
    navigation.php
    dashboard.php
    features.php
```

You may propose a better structure if the existing package conventions suggest one.

Profiles should be composable or inheritable if practical.

Example:

```text
default
  ↓
government
  ↓
philhealth
```

Do not hardcode profile-specific behavior throughout Vue pages.

Use profile-aware helpers/composables/config where possible.

---

# Cockpit Initial Scope

The first scaffold should focus on foundation, not full feature completion.

Recommended first slices:

## Slice 0: Discovery and Compass

- Create/update `docs/ui-cockpit/COMPASS.md`
- Create discovery reports
- Produce source-of-truth matrix
- Produce porting map
- Produce scaffold plan

## Slice 1: Cockpit namespace and shell

- Create cockpit namespace
- Create Cockpit layout
- Create global header
- Create sidebar navigation
- Create placeholder dashboard
- Create balance HUD placeholder
- Ensure no existing Claim UI regression

## Slice 2: Dashboard foundation

- Liquidity hero placeholder
- balance cards
- redemption pipeline placeholder
- risk/expiry placeholder
- recent activity placeholder

## Slice 3: Quick Generate foundation

- template selector placeholder
- runtime input area placeholder
- pricing/funding summary placeholder
- generate action placeholder
- no real money movement unless existing services are safely wired

## Slice 4: Pay Code Explorer foundation

- search bar
- filter builder placeholder
- results table placeholder
- row action placeholders

## Slice 5: Voucher Detail foundation

- overview
- timeline
- evidence tab placeholder
- distribution tab placeholder
- audit tab placeholder

## Slice 6: Distribution Workspace foundation

- digital distribution placeholder
- print template placeholder
- share/QR placeholder
- operational analytics placeholder

Only proceed beyond this after tests pass and the compass is updated.

---

# Testing Expectations

All frontend tests must pass.

The package uses Vitest for frontend tests.

Before every commit, run the appropriate frontend test command.

Likely command:

```bash
npm run test:frontend
```

If the package has a more specific command, discover and use it.

If PHP/backend is touched, also run the appropriate backend tests.

Likely command may be:

```bash
composer test
```

or the package-specific Pest/PHPUnit command.

Discover before assuming.

Do not commit failing tests.

If a test currently fails for an unrelated pre-existing reason, document it clearly in the compass and report.

---

# Frontend Testing Rules

For new Cockpit components:

- add tests under `tests/frontend/cockpit/`
- prefer component-level Vitest tests
- test critical rendering and action availability
- test feature-profile-driven visibility where introduced
- test layout components before deep functionality

Existing frontend tests must not be deleted or weakened.

The current Claim UI/UX tests are protected.

---

# Commit Discipline

Commit per coherent slice.

Rule:

```text
1 slice = 1 passing commit
```

Do not make one giant commit.

Do not commit per tiny file.

Before commit:

```bash
git status
npm run test:frontend
```

If backend touched:

```bash
composer test
```

Suggested commit messages:

```text
docs(cockpit): add source discovery reports
feat(cockpit): add cockpit layout shell
feat(cockpit): scaffold dashboard foundation
feat(cockpit): scaffold quick generate foundation
test(cockpit): cover balance hud rendering
```

At the end of each slice, update `COMPASS.md`.

---

# Architecture Guardrails

## Do not put domain logic in Vue

The Cockpit UI should consume domain/application services.

Do not move execution rules into frontend conditionals.

## Do not make Cockpit multi-tenant

Feature Profiles are not tenants.

Do not introduce tenant IDs, tenant routing, tenant database isolation, or tenant ownership concepts.

## Do not break Claim UI

Claim UI executes the redeemer journey.

Cockpit manages operator workflows.

## Do not merge issuer OTP into redeemer UX

Paynamics OTP approval is issuer-side authorization.

The redeemer should not enter the issuer OTP.

Preserve this separation.

## Do not implement silent reconciliation manipulation

Reconciliation may support:

- dashboard summary
- notes
- disputes
- exception markers
- correction entries in the future

Do not support silent historical rewriting or hidden financial edits.

## Do not build marketing analytics

Distribution analytics are operational:

- shared
- delivered
- opened
- claim started
- redeemed
- expired

Do not build campaign/marketing automation unless explicitly instructed later.

## Do not implement autonomous AI money movement

AI may search and prepare.

Human must confirm financial actions.

---

# Desired Cockpit Experience

The Cockpit should feel:

```text
professional
bank-grade
dense but readable
financial
forensic
actionable
alive
```

Avoid:

```text
generic admin panel
consumer wallet UI
toy-like dashboard
overly decorative theme
uncontrolled CRUD sprawl
```

---

# Important UX Concepts To Preserve

## Floating Issuance Palette

On long issuance/composer screens, provide an always-visible palette that shows:

- pricing
- funding impact
- connectivity
- draft status
- selected template
- execution summary
- issue actions

## Quick Generate

Common issuance should be possible in under five seconds.

## Balance HUD

Balances should be visible globally.

Internal wallet is operational truth.

Live bank balance is external confirmation.

## Distribution Workspace

Sharing/printing is not a popup.

Distribution deserves a workspace.

## Settlement Envelope Workspace

Simple payloads may remain inline.

Complex settlement/evidence workflows should open a dedicated workspace.

## Voucher Explorer

Search should feel like a financial transaction explorer.

Support natural-language search as a future/phase feature.

## Voucher Detail

Must be forensic.

Must show:

- signature
- location
- selfie
- KYC
- evidence
- timeline
- audit
- distribution
- execution

## About & Provenance

Add a memorable but tasteful About & Provenance experience.

Display:

```text
Technology Inventor

Lester B. Hurtado

Creator of x-change and the Pay Code architecture.
```

Institution ownership remains primary.

Creator provenance is discoverable, not intrusive.

Optional Easter Egg is acceptable.

---

# Expected First Response From Codex

Do not start coding immediately.

First produce:

1. confirmation of repository inspected
2. tooling detected
3. discovery plan
4. proposed first reports to create
5. risk notes

Then proceed with source discovery.

---

# Final Guiding Rule

Preserve what works.

Promote what is mature.

Enhance what aligns with the Cockpit.

Create only what is missing.

At every step:

```text
keep tests passing
keep the compass updated
commit coherent slices
protect the existing Claim UI
keep all productized work inside x-change
```
