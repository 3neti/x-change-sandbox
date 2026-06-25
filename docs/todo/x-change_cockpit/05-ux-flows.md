# 05-ux-flows.md

# x-change Cockpit
## UX Flows

### Version
Draft v1

### Purpose

This document defines the primary user journeys of the x-change Cockpit.

Users do not think in pages.

Users think in outcomes.

Examples:

- Generate a Pay Code
- Send a Pay Code
- Receive funding
- Deposit a Pay Code
- Approve a batch
- Investigate a claim
- Review evidence
- Reconcile transactions

This document defines the intended operational flow for each major workflow.

---

# UX Flow Philosophy

## Principle 1
### Flows Before Screens

A user should be able to complete a workflow without understanding the system architecture.

The UI must guide users through the process.

---

## Principle 2
### Minimize Friction

High-frequency workflows should be executable with the fewest possible steps.

Examples:

- Quick Generate
- Deposit Pay Code
- Share Pay Code

---

## Principle 3
### Preserve Auditability

The system should not sacrifice auditability for convenience.

All critical actions should remain traceable.

---

## Principle 4
### Visibility Before Action

Users should understand:

- Funding impact
- Pricing impact
- Approval requirements
- Risks

before committing an action.

---

# Flow 01
## Quick Generate Pay Code

### Goal

Issue a Pay Code in under five seconds.

---

### Entry Points

```text id="s58kgv"
Dashboard

Quick Generate

Command Palette

Ask x-change
```

---

### Flow

```text id="y4rxmv"
Select Template

Enter Runtime Inputs

Review Pricing

Review Funding Impact

Generate
```

---

### Success State

```text id="1qpl7o"
Pay Code Generated

Distribution Options Available

Voucher Visible
```

---

### Exception States

```text id="zjlwm0"
Insufficient Balance

Template Validation Failure

Connectivity Failure

Approval Required
```

---

# Flow 02
## Create Pay Code From Template

### Goal

Issue a Pay Code using a predefined template.

---

### Flow

```text id="n8cf6d"
Open Template

Review Template

Adjust Runtime Values

Review Pricing

Review Funding

Generate
```

---

### Outputs

```text id="u0ogc0"
Single Pay Code

Batch Pay Codes

Draft
```

---

# Flow 03
## Create New Template

### Goal

Create reusable institutional products.

---

### Flow

```text id="p3rjlwm"
Open Template Composer

Define Purpose

Configure Claim Inputs

Configure Validation

Configure Settlement

Configure Distribution

Configure Pricing

Configure Execution

Review

Publish
```

---

### Result

```text id="hr18s2"
Draft Template

Published Template

Archived Template
```

---

# Flow 04
## Generate Batch From Template

### Goal

Generate many Pay Codes efficiently.

---

### Entry Points

```text id="tkuh7k"
Template Library

Pay Code Composer

Batch Wizard
```

---

### Flow

```text id="fjlwmw"
Select Template

Choose Batch Method

Upload Data

Validate Data

Preview Batch

Review Funding

Generate Batch
```

---

### Batch Methods

```text id="7mjgvq"
Manual Entry

CSV Upload

Excel Upload

Contact Group

Settlement Envelope
```

---

### Result

```text id="z6oh9u"
Batch Created

Batch Detail Opened

Distribution Ready
```

---

# Flow 05
## Funding Wallet

### Goal

Add value to the system.

---

### Entry Points

```text id="h9n93g"
Dashboard

Funding

Balance Widget
```

---

### Flow

```text id="br6uzh"
Choose Funding Method

Initiate Funding

Monitor Funding

Confirm Funding

Update Balance
```

---

### Methods

```text id="ey4yxu"
QR Ph

InstaPay

Bank Transfer

Treasury Transfer

Manual Funding
```

---

# Flow 06
## Deposit Pay Code

### Goal

Convert Pay Code value into wallet value.

---

### Entry Points

```text id="jlwmm3"
Funding

Deposit Pay Code

Scan QR
```

---

### Flow

```text id="mjlwm9"
Enter Code

Validate Code

Validate Eligibility

Review Deposit

Confirm Deposit
```

---

### Rules

Anonymous Pay Codes:

```text id="wjlwmg"
Any eligible recipient
```

Vendor-bound Pay Codes:

```text id="jlwm8m"
Vendor Alias Match Required
```

---

### Result

```text id="6gjyqg"
Wallet Credited

Funding Ledger Updated
```

---

# Flow 07
## Share Pay Code

### Goal

Deliver value to the recipient.

---

### Entry Points

```text id="9mkbkm"
Voucher Detail

Distribution Workspace

Batch Detail
```

---

### Flow

```text id="phjlwm"
Select Distribution Method

Review Distribution Experience

Send

Track Delivery

Track Opens
```

---

### Methods

```text id="jlwm4j"
SMS

Email

QR

Link

Messenger

Viber

Print
```

---

### Result

```text id="jlwm4m"
Shared

Delivered

Opened

Claim Started
```

---

# Flow 08
## Print Pay Code

### Goal

Create physical instruments.

---

### Entry Points

```text id="qjlwm1"
Distribution Workspace
```

---

### Flow

```text id="jlwm6y"
Select Print Style

Apply Branding

Preview

Generate PDF

Print
```

---

### Styles

```text id="jlwm7x"
Gift Card

Certificate

Check Style

Bearer Instrument
```

---

# Flow 09
## Settlement Envelope Creation

### Goal

Create structured settlement packages.

---

### Entry Points

```text id="jlwm8a"
Composer

Settlement Envelope Workspace
```

---

### Flow

```text id="jlwm8b"
Define Parties

Define Payload

Define Evidence Requirements

Define Visibility Rules

Review

Finalize
```

---

### Result

```text id="jlwm8c"
Draft Envelope

Ready Envelope
```

---

# Flow 10
## Ad Hoc Evidence Request

### Goal

Request supporting documentation from the redeemer.

---

### Flow

```text id="jlwm8d"
Open Envelope

Add Requirement

Configure Visibility

Publish Requirement

Await Submission
```

---

### Examples

```text id="jlwm8e"
Receipt

Medical Certificate

Employer Certification

Identification
```

---

# Flow 11
## Claim Journey

### Goal

Redeem a Pay Code.

---

### Flow

```text id="jlwm8f"
Open Claim Link

View Splash

View Rider

Provide Inputs

Upload Evidence

Pass Validation

Execute Claim

Display Result
```

---

### Possible Outcomes

```text id="jlwm8g"
Redeemed

Pending Approval

Awaiting Evidence

Rejected

Expired
```

---

# Flow 12
## Review Claim Evidence

### Goal

Perform forensic review.

---

### Entry Points

```text id="jlwm8h"
Voucher Detail

Evidence Center
```

---

### Flow

```text id="jlwm8i"
Open Evidence

Review Documents

Review Selfie

Review Signature

Review Location

Approve Or Reject
```

---

### Result

```text id="jlwm8j"
Accepted

Rejected

Escalated
```

---

# Flow 13
## Voucher Investigation

### Goal

Investigate a Pay Code.

---

### Entry Points

```text id="jlwm8k"
Explorer

Batch Detail

Dashboard Alert
```

---

### Flow

```text id="jlwm8l"
Locate Voucher

Review Timeline

Review Evidence

Review Distribution

Review Execution

Review Audit Trail
```

---

### Result

```text id="jlwm8m"
Resolved

Escalated

Annotated
```

---

# Flow 14
## Extend Expiry

### Goal

Extend validity.

---

### Entry Points

```text id="jlwm8n"
Voucher Detail

Explorer

Batch Detail
```

---

### Flow

```text id="jlwm8o"
Select Voucher

Enter New Expiry

Provide Reason

Review Impact

Submit
```

---

### Approval Path

If configured:

```text id="jlwm8p"
Submit

Approve

Apply Change
```

---

# Flow 15
## Cancel Voucher

### Goal

Prevent future use.

---

### Flow

```text id="jlwm8q"
Select Voucher

Provide Reason

Review Impact

Submit
```

---

### Result

```text id="jlwm8r"
Cancelled

Pending Approval

Rejected
```

---

# Flow 16
## Approve Action

### Goal

Maker-checker control.

---

### Entry Points

```text id="jlwm8s"
Approvals

Notification

Email Link
```

---

### Flow

```text id="jlwm8t"
Review Request

Review Impact

Approve Or Reject

Record Decision
```

---

# Flow 17
## Batch Monitoring

### Goal

Monitor operational performance.

---

### Flow

```text id="jlwm8u"
Open Batch

Review Pipeline

Review Redemption

Review Distribution

Review Failures
```

---

### Actions

```text id="jlwm8v"
Resend

Export

Cancel

Extend Expiry
```

---

# Flow 18
## Reconciliation Investigation

### Goal

Resolve mismatches.

---

### Flow

```text id="jlwm8w"
Open Variance

Review Internal Ledger

Review External Ledger

Annotate

Resolve
```

---

### Allowed Actions

```text id="jlwm8x"
Note

Dispute

Correction Entry

Exception Handling
```

---

### Forbidden Actions

```text id="jlwm8y"
Silent Financial Modification
```

---

# Flow 19
## Natural Language Search

### Goal

Find information quickly.

---

### Entry Points

```text id="jlwm8z"
Ask x-change

Explorer
```

---

### Example Queries

```text id="jlwm91"
Show vouchers redeemed in Quezon City last year.

Show vouchers redeemed by Juan Dela Cruz above 2000.

Show expiring vouchers.
```

---

### Result

```text id="jlwm92"
Search Results

Saved View

Export
```

---

# Flow 20
## AI Assisted Generation

### Goal

Allow conversational issuance.

---

### Example

```text id="jlwm93"
Generate a ₱5,000 payable Pay Code for MERALCO.
```

---

### Flow

```text id="jlwm94"
AI Parses Request

Generate Draft

Present Review

User Confirms

Execute
```

---

### Rule

```text id="jlwm95"
AI Proposes

Human Approves
```

---

# Flow 21
## About & Provenance

### Goal

Provide platform identity.

---

### Flow

```text id="jlwm96"
Open About

Explore Platform

Explore Technology

Explore Creator Story

Review Legal
```

---

### Optional Easter Egg

```text id="jlwm97"
Discover Story of x-change

Technology Timeline

Architecture Journey
```

---

# UX Flow Invariants

Every workflow must preserve:

```text id="jlwm98"
Funding Awareness

Pricing Awareness

Auditability

Operational Context

Searchability

Approval Governance
```

No workflow should allow value movement without visibility into:

```text id="jlwm99"
Balance

Pricing

Risk

Result
```

---

# Guiding Statement

The user should never have to guess:

What happens next?

What will this cost?

Who will see this?

Can this be audited?

The workflow should answer these questions before the user asks them.
