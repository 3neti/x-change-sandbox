# 06-widget-catalog.md

# x-change Cockpit
## Widget Catalog

### Version
Draft v1

### Purpose

This document defines the reusable widget library for the x-change Cockpit.

The objective is to create a consistent operational experience across:

- Dashboard
- Funding
- Pay Codes
- Templates
- Settlement Envelopes
- Distribution
- Operations
- Reports

Widgets should be:

- Reusable
- Composable
- Responsive
- Operationally focused
- Audit-friendly

Widgets are the building blocks of the Cockpit.

---

# Widget Philosophy

## Principle 1
### Information Before Decoration

Widgets exist to communicate operational state.

Widgets are not visual ornaments.

---

## Principle 2
### Actions Near Information

Whenever possible:

```text
Information
+
Action
```

should coexist.

Avoid forcing users to navigate elsewhere to act.

---

## Principle 3
### Status Must Be Visible

Every widget should communicate:

```text
Healthy
Warning
Critical
```

when applicable.

---

## Principle 4
### Drill Down Everywhere

Every summary widget should support:

```text
View Details
```

or

```text
Open Explorer
```

---

# Category A
# Financial Widgets

These widgets represent value.

---

# Liquidity Hero Widget

## Purpose

Primary dashboard widget.

Displays available money.

---

## Contents

```text
Internal Balance

Live Balance

Reserved Funds

Pending Settlement

Available To Issue

Funding Runway
```

---

## Actions

```text
Top Up

Deposit Pay Code

View Funding

Reconcile
```

---

## Placement

Dashboard only.

Always above the fold.

---

# Balance Card Widget

## Purpose

Display a single financial metric.

---

## Examples

```text
Internal Balance

Live Balance

Pending Settlement

Reserved Funds

Projected Redemption
```

---

## Placement

Dashboard

Funding

Reports

---

# Balance Trend Widget

## Purpose

Display historical financial movement.

---

## Time Ranges

```text
7D

30D

90D

1Y
```

---

## Metrics

```text
Balance

Funding

Redemptions

Settlement
```

---

# Funding Source Widget

## Purpose

Display available funding methods.

---

## Examples

```text
QR Ph

InstaPay

Bank Transfer

Treasury Transfer
```

---

# Category B
# Pay Code Widgets

These widgets represent Pay Code activity.

---

# Redemption Pipeline Widget

## Purpose

Display lifecycle progression.

---

## Stages

```text
Issued

Shared

Opened

Claim Started

Claim Completed

Redeemed

Disbursed

Reconciled
```

---

## Placement

Dashboard

Batch Detail

Reports

---

# Pay Code Summary Widget

## Purpose

Display voucher summary.

---

## Contents

```text
Amount

Status

Recipient

Issued Date

Expiry Date

Template

Batch
```

---

# Pay Code Status Widget

## Purpose

Display current state.

---

## Examples

```text
Draft

Issued

Shared

Opened

Claim Started

Redeemed

Expired

Cancelled
```

---

# Pay Code Timeline Widget

## Purpose

Display lifecycle history.

---

## Events

```text
Created

Shared

Opened

Claimed

Redeemed

Approved

Cancelled
```

---

## Placement

Voucher Detail

Audit Center

---

# Pay Code Action Bar Widget

## Purpose

Provide operational actions.

---

## Actions

```text
Share

Extend Expiry

Cancel

Reissue

Export

View Audit
```

---

# Batch Summary Widget

## Purpose

Display batch overview.

---

## Contents

```text
Total Count

Redeemed

Pending

Failed

Expired

Total Amount
```

---

# Batch Pipeline Widget

## Purpose

Display batch progress.

---

## Placement

Batch Detail

Dashboard

Reports

---

# Category C
# Search & Explorer Widgets

---

# Universal Search Widget

## Purpose

Primary search entry point.

---

## Supports

```text
Code

Name

Mobile

Vendor Alias

Natural Language
```

---

## Example

```text
Show vouchers redeemed in Quezon City last year.
```

---

# Filter Builder Widget

## Purpose

Construct advanced queries.

---

## Supported Filters

```text
Amount

Date

Status

Location

Template

Batch

Distribution

Execution

Settlement
```

---

# Saved View Widget

## Purpose

Store reusable searches.

---

## Examples

```text
Expiring Soon

Pending Evidence

High Value Claims

Redeemed This Week
```

---

# Bulk Action Widget

## Purpose

Apply actions to search results.

---

## Actions

```text
Extend Expiry

Cancel

Export

Resend Distribution
```

---

# Category D
# Distribution Widgets

---

# Distribution Summary Widget

## Purpose

Display delivery status.

---

## Metrics

```text
Shared

Delivered

Opened

Claim Started

Redeemed
```

---

# Share Action Widget

## Purpose

Initiate delivery.

---

## Methods

```text
SMS

Email

QR

Link

Messenger

Viber
```

---

# QR Preview Widget

## Purpose

Display QR code.

---

## Actions

```text
Download

Print

Copy Link
```

---

# Print Template Widget

## Purpose

Render printable Pay Codes.

---

## Formats

```text
Gift Card

Certificate

Check Style

Bearer Instrument
```

---

# Branding Preview Widget

## Purpose

Preview institutional branding.

---

## Elements

```text
Logo

Seal

Stamp

Background

Watermark
```

---

# Distribution Analytics Widget

## Purpose

Track effectiveness.

---

## Metrics

```text
Delivered

Opened

Claimed

Expired
```

---

# Category E
# Settlement Envelope Widgets

---

# Envelope Summary Widget

## Purpose

Display settlement readiness.

---

## Contents

```text
Status

Evidence Count

Approvals

Required Documents

Parties
```

---

# Party Summary Widget

## Purpose

Display participating entities.

---

## Examples

```text
Issuer

Beneficiary

Institution

Provider
```

---

# Required Documents Widget

## Purpose

Manage evidence requirements.

---

## Types

```text
Standard

Ad Hoc
```

---

## States

```text
Pending

Submitted

Reviewed

Rejected
```

---

# Evidence Status Widget

## Purpose

Track document completion.

---

## Examples

```text
Medical Certificate

Receipt

Employee ID

Proof of Service
```

---

# Envelope Readiness Widget

## Purpose

Determine readiness.

---

## States

```text
Draft

Incomplete

Ready

Awaiting Approval
```

---

# Category F
# Evidence & Forensics Widgets

---

# Signature Viewer Widget

## Purpose

Display captured signatures.

---

## Metadata

```text
Captured At

Source

Hash

File
```

---

# Selfie Viewer Widget

## Purpose

Display claimant selfies.

---

## Metadata

```text
Timestamp

Validation Result

Provider
```

---

# Location Viewer Widget

## Purpose

Display claim location.

---

## Metadata

```text
Latitude

Longitude

Accuracy

Captured At
```

---

# KYC Summary Widget

## Purpose

Display identity verification.

---

## Contents

```text
Provider

Status

Document Type

Reference
```

---

# Evidence Gallery Widget

## Purpose

Display uploaded documents.

---

## Supported Files

```text
PDF

Image

Video

Document
```

---

# Device Fingerprint Widget

## Purpose

Display claim environment.

---

## Contents

```text
IP Address

User Agent

Device

Channel
```

---

# Category G
# Operations Widgets

---

# Execution Monitor Widget

## Purpose

Display execution engine activity.

---

## Contents

```text
Driver

Pipeline

Current Stage

Result

Failure
```

---

# Failure Queue Widget

## Purpose

Display operational failures.

---

## Examples

```text
Distribution Failure

Execution Failure

Settlement Failure
```

---

# Reconciliation Summary Widget

## Purpose

Display reconciliation state.

---

## Metrics

```text
Matched

Unmatched

Variance

Resolved
```

---

# Audit Event Widget

## Purpose

Display immutable events.

---

## Contents

```text
Who

What

When

Why

Before

After
```

---

# Approval Queue Widget

## Purpose

Display pending approvals.

---

## Actions

```text
Approve

Reject

Escalate
```

---

# Category H
# Dashboard Intelligence Widgets

---

# Expiry Risk Widget

## Purpose

Identify expiring Pay Codes.

---

## Buckets

```text
Today

This Week

This Month
```

---

# Risk Radar Widget

## Purpose

Surface operational concerns.

---

## Examples

```text
Unshared

Abandoned Claims

Low Balance

Pending Evidence
```

---

# Redemption Heat Map Widget

## Purpose

Display geographic activity.

---

## Metrics

```text
Count

Amount

Success Rate
```

---

# AI Insight Widget

## Purpose

Surface operational intelligence.

---

## Examples

```text
53 vouchers expire in 48 hours.

14 claims await evidence.

Funding runway is 3 days.
```

---

# Recent Activity Widget

## Purpose

Display system activity stream.

---

## Examples

```text
Voucher Redeemed

Batch Completed

Approval Granted

Funding Received
```

---

# Category I
# Composer Widgets

---

# Floating Issuance Palette Widget

## Purpose

Persistent issuance control center.

---

## Contents

```text
Template

Amount

Pricing

Funding Impact

Connectivity

Draft Status
```

---

## Actions

```text
Save Draft

Save Template

Generate

Generate Batch
```

---

# Pricing Summary Widget

## Purpose

Display cost impact.

---

## Contents

```text
Target Amount

Fees

Total Cost

Balance After Issue
```

---

# Connectivity Widget

## Purpose

Display operational connectivity.

---

## States

```text
Online

Offline

Degraded
```

---

# Draft Status Widget

## Purpose

Display save status.

---

## States

```text
Saved

Saving

Unsaved Changes

Conflict
```

---

# Category J
# AI Widgets

---

# Ask x-change Widget

## Purpose

Natural language interaction.

---

## Capabilities

```text
Search

Insights

Commands

Recommendations
```

---

# AI Action Review Widget

## Purpose

Require confirmation before execution.

---

## Flow

```text
AI Proposal

Review

Confirm

Execute
```

---

## Rule

```text
AI Proposes

Human Approves
```

---

# Widget Invariants

Every widget should:

```text
Support drill-down

Expose operational state

Support accessibility

Support auditability

Support responsive layouts
```

Widgets must not:

```text
Duplicate functionality

Hide critical status

Require excessive navigation

Obscure financial impact
```

---

# Guiding Statement

Widgets are not decorative UI components.

Widgets are operational instruments.

Every widget should help the user:

```text
Observe

Understand

Decide

Act
```

with the minimum possible friction.
