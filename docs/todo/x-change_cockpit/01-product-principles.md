# 01-product-principles.md

# x-change Cockpit
## Product Principles

### Version
Draft v1

### Purpose

This document defines the foundational product principles of the x-change Cockpit.

These principles guide:

- UI design
- UX design
- Navigation
- Feature design
- Reporting
- Dashboard design
- AI integrations
- Mobile experiences
- Future enhancements

These principles are intended to be long-lived and should remain valid even as screens, technologies, and implementations evolve.

---

# Vision

The x-change Cockpit is not a voucher management system.

The x-change Cockpit is a:

- Financial Workflow Platform
- Pay Code Operations Platform
- Settlement Platform
- Distribution Platform
- Evidence Platform
- Financial Intelligence Platform

The primary purpose of the Cockpit is to enable institutions to:

- Create value
- Move value
- Validate value
- Distribute value
- Monitor value
- Reconcile value
- Audit value

through the Pay Code architecture.

---

# Product Philosophy

## Principle 1
### Money Is The Primary Subject

Most systems place transactions at the center.

The x-change Cockpit places money at the center.

Users care about:

- Available funds
- Reserved funds
- Expected redemptions
- Funding requirements
- Liquidity
- Settlement status

before they care about individual Pay Codes.

The system should always answer:

- How much money do I have?
- How much money is moving?
- How much money is at risk?

Balances are first-class citizens.

---

## Principle 2
### Templates Are First-Class Citizens

Templates represent institutional products.

Examples:

- Money Changer
- OFW Remittance
- Payroll
- Medical Assistance
- PhilHealth BST
- Settlement Envelope

Operators should normally issue from templates.

The system should encourage:

Template → Generate

rather than

Generate → Save Template

Templates should become reusable institutional knowledge.

---

## Principle 3
### Pay Codes Are Executable Contracts

Pay Codes are not vouchers.

Pay Codes are executable instruction contracts.

A Pay Code may represent:

- Cash disbursement
- Merchant payment
- Wallet deposit
- Settlement authorization
- Stored value
- Financial entitlement

The UI must never assume that all Pay Codes are cash-out instruments.

---

## Principle 4
### Distribution Is A First-Class Domain

Generation is not the end of the lifecycle.

Distribution is a core capability.

The system must support:

- Digital distribution
- Physical distribution
- Delivery monitoring
- Share analytics
- Print experiences

Distribution should be observable, measurable, and auditable.

---

## Principle 5
### Settlement Is A First-Class Domain

Settlement envelopes are not attachments.

Settlement envelopes are structured business objects.

The system must support:

- Parties
- Evidence
- Documents
- Approvals
- Readiness gates
- Institutional workflows

The system should treat settlement as a business process rather than a file upload.

---

## Principle 6
### Evidence Matters

Evidence is a first-class asset.

Examples:

- Signatures
- Selfies
- KYC results
- GPS locations
- Uploaded documents
- Receipts
- Medical certificates

Evidence should be:

- Viewable
- Searchable
- Auditable
- Exportable

The system should support forensic review.

---

## Principle 7
### Auditability Is Mandatory

Every important action must be explainable.

Users must always be able to answer:

- Who?
- What?
- When?
- Why?
- Before?
- After?

The platform should prefer immutable events over mutable records.

---

## Principle 8
### Search Must Be Powerful

Users should be able to find anything.

The system should support:

- Filters
- Saved views
- Advanced search
- Natural language search

The user should be able to ask:

"Show vouchers redeemed in Quezon City last year between ₱5,000 and ₱6,000."

and obtain results without building complex queries.

---

## Principle 9
### Operators Work In Flows

Users do not think in pages.

Users think in workflows.

Examples:

- Generate Pay Code
- Share Pay Code
- Deposit Pay Code
- Claim Pay Code
- Approve Batch
- Reconcile Batch

The system should optimize for complete workflows rather than isolated screens.

---

## Principle 10
### Speed Matters

Common actions should be executable quickly.

Examples:

- Money changer issuance
- Remittance issuance
- Cashier issuance
- Branch operations

The system should support issuance in under five seconds whenever possible.

Quick Generate is a first-class workflow.

---

## Principle 11
### Context Must Always Be Visible

Users should never lose operational awareness.

The system should continuously display:

- Operating identity
- Available balance
- Live balance
- Connectivity status
- Draft status
- Approval status

Operational context should not require navigation.

---

## Principle 12
### The System Should Feel Alive

The Cockpit should communicate activity.

Users should see:

- Money moving
- Claims progressing
- Batches advancing
- Distributions occurring
- Risks emerging

The system should feel operational rather than static.

---

## Principle 13
### Intelligence Beats Reporting

The goal is not merely reporting.

The goal is operational intelligence.

The platform should surface:

- Risks
- Trends
- Opportunities
- Anomalies
- Predictions

before the user asks.

---

## Principle 14
### AI Is An Assistant, Not An Operator

AI should:

- Search
- Explain
- Summarize
- Recommend
- Generate

AI should not silently move money.

Financial actions require human confirmation.

The model is:

AI proposes.
Human approves.

---

## Principle 15
### Desktop First

The Cockpit is primarily an operational workstation.

Primary target:

Desktop

Secondary target:

PWA

Future target:

Native mobile

Desktop experiences should never be compromised solely to satisfy mobile constraints.

---

## Principle 16
### Workspace Profiles Over Themes

The system should support different operational perspectives.

Examples:

- Treasury
- Operations
- Compliance
- Executive
- Branch

Profiles may alter:

- Layouts
- Density
- Widgets
- Priorities

This is a productivity feature, not a cosmetic feature.

---

## Principle 17
### Institutional Ownership Must Be Preserved

The institution owns the deployment.

Examples:

- DBP
- PhilHealth
- LGU
- Corporate Customer

Institution branding should be prominent.

The technology platform should remain visible but secondary.

---

## Principle 18
### Creator Provenance Should Be Preserved

The platform should maintain discoverable provenance.

Users should be able to learn:

- The technology story
- The platform history
- The inventor
- The architecture journey

without distracting from institutional ownership.

The goal is professional attribution, not product vanity.

---

# Dashboard Principles

The dashboard should answer five questions within five seconds:

1. Do I have money?
2. Is money moving?
3. Is anything stuck?
4. Is anything risky?
5. Do I need to act?

Any dashboard widget that does not contribute to one of these questions should be reconsidered.

---

# Cockpit Principles

The Cockpit should feel like:

- A treasury workstation
- A payment operations center
- A settlement command center
- A financial intelligence platform

The Cockpit should not feel like:

- A CRUD application
- An admin panel
- A voucher generator
- A reporting portal

---

# Success Criteria

A successful Cockpit experience allows users to:

- Generate Pay Codes quickly
- Understand money movement instantly
- Locate information effortlessly
- Resolve exceptions efficiently
- Monitor operations continuously
- Trust audit trails completely
- Operate at institutional scale

without needing to understand the underlying architecture.

---

# Guiding Statement

The x-change Cockpit exists to make value visible, actionable, traceable, and trustworthy throughout its entire lifecycle.

Money moves.

Context follows.
