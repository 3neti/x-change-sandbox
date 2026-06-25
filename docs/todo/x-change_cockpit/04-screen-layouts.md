# 04-screen-layouts.md

# x-change Cockpit
## Screen Layouts

### Version
Draft v1

### Purpose

This document defines the screen layout strategy for the x-change Cockpit.

This document is not an implementation plan.

It describes:

- Primary screens
- Screen regions
- Widget placement
- Action placement
- Layout priorities
- Desktop-first structure
- PWA adaptation rules

The layouts should guide Codex when scaffolding UI/UX screens while preserving product intent.

---

# Layout Philosophy

The x-change Cockpit should look and feel like:

- Treasury workstation
- Financial operations center
- Payment command center
- Settlement monitoring cockpit

The UI should not feel like:

- CRUD admin panel
- Generic dashboard template
- Voucher table application
- Consumer wallet app

The dominant design language is:

```text
Bank-grade.
Operational.
Dense but readable.
Actionable.
Forensic.
```

---

# Global Shell Layout

All authenticated screens should share a consistent shell.

```text
┌──────────────────────────────────────────────────────────────┐
│ Global Header                                                │
├──────────────┬───────────────────────────────────────────────┤
│ Side Nav     │ Workspace                                     │
│              │                                               │
│              │                                               │
└──────────────┴───────────────────────────────────────────────┘
```

---

## Global Header

The header must remain visible.

### Contents

```text
Institution / Product Name
Operating Identity
Internal Balance
Live Balance
Connectivity Status
Notifications
Ask x-change
Profile Menu
```

### Example

```text
DBP Pay Code

Operating As:
Treasury Operations

Internal Balance:
₱125,000,000

Live Balance:
₱123,500,000

Online
```

### Rules

- Institution name should be prominent.
- x-change version should not be shown in the header.
- Balance indicators must be visible.
- Operating identity must be visible for business, merchant, and institutional users.
- Personal users may hide operating identity if not relevant.

---

## Side Navigation

Persistent on desktop.

### Primary Items

```text
Dashboard
Quick Generate
Funding
Pay Codes
Templates
Contacts
Operations
Reports
```

### Secondary Items

```text
Approvals
Administration
```

### Rules

- Navigation items may show badges.
- Badges should represent actionable state.
- Avoid decorative badges.

Examples:

```text
Approvals 12
Operations 3
Funding Alert
```

---

# Dashboard Layout

## Purpose

The dashboard is the operational cockpit.

It should answer:

```text
Do I have money?
Is money moving?
Is anything stuck?
Is anything risky?
Do I need to act?
```

---

## Desktop Layout

```text
┌──────────────────────────────────────────────────────────────┐
│ Liquidity Hero                                               │
├──────────────────────────────┬───────────────────────────────┤
│ Balance Trend                │ Redemption Pipeline           │
├──────────────────────────────┼───────────────────────────────┤
│ Batch Command Center         │ Expiry Risk Center            │
├──────────────────────────────┼───────────────────────────────┤
│ Redemption Heat Map          │ AI Insight Panel              │
├──────────────────────────────┴───────────────────────────────┤
│ Recent Activity / Alerts                                      │
└──────────────────────────────────────────────────────────────┘
```

---

## Liquidity Hero

Most prominent dashboard region.

### Displays

```text
Available Internal Balance
Live Bank Balance
Reserved Funds
Pending Settlement
Available To Issue
Funding Runway
```

### Actions

```text
Top Up
Deposit Pay Code
Reconcile
View Funding
```

---

## Redemption Pipeline

Visual funnel.

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

## Batch Command Center

Displays active batches.

### Fields

```text
Batch Name
Total Count
Redeemed Count
Failed Count
Pending Count
Success Rate
```

### Actions

```text
View Batch
Resend
Export
```

---

## Expiry Risk Center

Displays Pay Codes at risk.

### Buckets

```text
Expiring Today
Expiring This Week
Expired Unopened
Opened But Unclaimed
```

### Actions

```text
Remind
Extend Expiry
View
```

---

## Redemption Heat Map

Displays geographic redemption activity.

### Metrics

```text
Count
Amount
Success Rate
Failure Rate
```

---

## AI Insight Panel

Displays operational insights.

Examples:

```text
53 Pay Codes expire in the next 48 hours.
PhilHealth BST batch has 14 claims awaiting evidence.
Quezon City redemptions increased 23% this week.
```

---

# Quick Generate Layout

## Purpose

Issue Pay Codes in under five seconds.

---

## Desktop Layout

```text
┌──────────────────────────────────────────────────────────────┐
│ Quick Generate Header                                        │
├──────────────────────────────────────────────────────────────┤
│ Template Selector                                            │
├──────────────────────────────────────────────────────────────┤
│ Runtime Inputs                                               │
├──────────────────────────────────────────────────────────────┤
│ Pricing + Funding Summary                                    │
├──────────────────────────────────────────────────────────────┤
│ Generate Action                                              │
├──────────────────────────────────────────────────────────────┤
│ Recent Generated Pay Codes                                   │
└──────────────────────────────────────────────────────────────┘
```

---

## Required Regions

### Template Selector

```text
Template
Recent Templates
Favorite Templates
```

### Runtime Inputs

Rendered dynamically based on selected template.

Examples:

```text
Amount
Recipient Mobile
Recipient Name
Vendor Alias
Reference Number
```

### Pricing + Funding Summary

Always visible before generation.

Displays:

```text
Target Amount
Fees
Total Cost
Available Balance
Balance After Issue
```

### Generate Action

Primary button:

```text
Generate Pay Code
```

Secondary actions:

```text
Open Composer
Save Draft
```

---

# Funding Layout

## Purpose

Manage incoming value.

---

## Funding Overview Layout

```text
┌──────────────────────────────────────────────────────────────┐
│ Funding Balance Cards                                        │
├──────────────────────────────┬───────────────────────────────┤
│ Top Up Methods               │ Deposit Pay Code              │
├──────────────────────────────┴───────────────────────────────┤
│ Funding History                                               │
├──────────────────────────────────────────────────────────────┤
│ Funding Reconciliation                                        │
└──────────────────────────────────────────────────────────────┘
```

---

## Balance Cards

Display:

```text
Internal Wallet Balance
Live Bank Balance
Pending Funding
Reserved Funds
Variance
```

---

## Top Up Methods

Cards:

```text
QR Ph
InstaPay
Bank Transfer
Treasury Transfer
```

---

## Deposit Pay Code

Simple form:

```text
Enter Pay Code
Scan QR
Validate
Deposit To Wallet
```

---

# Pay Code Explorer Layout

## Purpose

Search, filter, manage, and operate Pay Codes.

---

## Desktop Layout

```text
┌──────────────────────────────────────────────────────────────┐
│ Search Bar                                                    │
├──────────────────────────────────────────────────────────────┤
│ Filter Builder                                                │
├──────────────────────────────────────────────────────────────┤
│ Saved Views + Bulk Actions                                    │
├──────────────────────────────────────────────────────────────┤
│ Results Table                                                 │
└──────────────────────────────────────────────────────────────┘
```

---

## Search Bar

Supports:

```text
Plain text
Code
Contact name
Mobile
Vendor alias
Natural language queries
```

Examples:

```text
redeemed in Quezon City last year between 5000 and 6000

redeemed by Juan Dela Cruz above 2000
```

---

## Filter Builder

Fields:

```text
Issued Date
Redeemed Date
Amount Range
Status
Template
Batch
Contact
Location
Vendor Alias
Execution Driver
Distribution Status
Settlement Envelope
```

---

## Results Table

Columns:

```text
Code
Status
Amount
Template
Beneficiary
Issued At
Redeemed At
Location
Batch
Execution
Distribution
Reconciliation
Actions
```

---

## Row Actions

```text
View
Share
Extend Expiry
Cancel
Reissue
Export Evidence
```

---

## Bulk Actions

```text
Extend Expiry
Cancel
Resend Distribution
Export Results
Export Evidence Pack
```

Bulk actions must require confirmation and may require approval.

---

# Pay Code Detail Layout

## Purpose

Provide complete forensic view of a Pay Code.

---

## Layout

```text
┌──────────────────────────────────────────────────────────────┐
│ Pay Code Header                                               │
├──────────────────────────────────────────────────────────────┤
│ Status + Amount + Key Summary                                 │
├──────────────────────────────────────────────────────────────┤
│ Action Bar                                                    │
├──────────────────────────────────────────────────────────────┤
│ Tabs                                                          │
└──────────────────────────────────────────────────────────────┘
```

---

## Header

Displays:

```text
Pay Code
Status
Amount
Template
Batch
Issued At
Expires At
Operating Identity
```

---

## Action Bar

Actions:

```text
Share
Extend Expiry
Cancel
Reissue
Export
View Audit
```

Sensitive actions require:

```text
Reason
Permission
Confirmation
Approval if configured
```

---

## Tabs

```text
Overview
Timeline
Distribution
Claim Evidence
Execution
Settlement Envelope
Pricing
Funding Impact
Reconciliation
Audit Trail
```

---

## Claim Evidence Tab

Displays:

```text
Signature
Selfie
Location
KYC Details
Uploaded Documents
Device
Session
IP Address
User Agent
```

Must look forensic and professional.

---

# Batch Detail Layout

## Purpose

Monitor and operate Pay Code batches.

---

## Layout

```text
┌──────────────────────────────────────────────────────────────┐
│ Batch Header                                                  │
├──────────────────────────────────────────────────────────────┤
│ Batch Summary Cards                                           │
├──────────────────────────────────────────────────────────────┤
│ Batch Pipeline                                                │
├──────────────────────────────────────────────────────────────┤
│ Tabs                                                          │
└──────────────────────────────────────────────────────────────┘
```

---

## Summary Cards

```text
Total Pay Codes
Total Amount
Redeemed
Pending
Failed
Expired
Distribution Status
```

---

## Tabs

```text
Pay Codes
Distribution
Failures
Approvals
Pricing
Reconciliation
Audit Trail
```

---

# Template Library Layout

## Purpose

Manage reusable Pay Code products.

---

## Layout

```text
┌──────────────────────────────────────────────────────────────┐
│ Template Header                                               │
├──────────────────────────────────────────────────────────────┤
│ Search + Filters                                              │
├──────────────────────────────────────────────────────────────┤
│ Template Cards / Table                                        │
└──────────────────────────────────────────────────────────────┘
```

---

## Template Card

Displays:

```text
Template Name
Category
Status
Version
Last Edited
Used By
Success Rate
Average Issue Time
```

Actions:

```text
Issue One
Issue Batch
Edit
Clone
Archive
```

---

# Pay Code Composer Layout

## Purpose

Design or issue complex Pay Codes.

---

## Layout

```text
┌──────────────────────────────────────────────────────────────┐
│ Composer Header                                               │
├──────────────────────────────────────────────┬───────────────┤
│ Composer Sections                            │ Floating      │
│                                              │ Issuance      │
│                                              │ Palette       │
└──────────────────────────────────────────────┴───────────────┘
```

---

## Composer Sections

```text
Purpose
Amount
Payable Target
Claim Inputs
Validation
Settlement / Envelope
Execution Engine
Claim Experience
Distribution Experience
Pricing
Review
```

---

## Floating Issuance Palette

Always visible.

Displays:

```text
Template
Purpose
Amount
Payable Target
Execution
Inputs
Validation
Pricing
Funding Impact
Connectivity
Draft Status
```

Actions:

```text
Save Draft
Save Template
Generate Pay Code
Generate Batch
```

---

# Settlement Envelope Workspace Layout

## Purpose

Manage structured evidence and settlement data.

---

## Layout

```text
┌──────────────────────────────────────────────────────────────┐
│ Envelope Header                                               │
├──────────────┬───────────────────────────────────────────────┤
│ Section Nav  │ Workspace                                     │
├──────────────┴───────────────────────────────────────────────┤
│ Review / Completion Bar                                       │
└──────────────────────────────────────────────────────────────┘
```

---

## Section Nav

```text
Parties
Case Details
Line Items
Required Documents
Evidence
Approvals
Visibility
Review
```

---

## Required Documents

Supports:

```text
Standard checklist
Ad hoc document requests
Required / Optional
Visibility rules
Review requirements
```

---

# Distribution Workspace Layout

## Purpose

Manage sharing, printing, and delivery.

---

## Layout

```text
┌──────────────────────────────────────────────────────────────┐
│ Distribution Header                                           │
├──────────────────────────────────────────────────────────────┤
│ Distribution Summary                                          │
├──────────────────────────────────────────────────────────────┤
│ Tabs                                                          │
└──────────────────────────────────────────────────────────────┘
```

---

## Tabs

```text
Digital
Print
Branding
Analytics
History
```

---

## Digital

Supports:

```text
Link
QR
SMS
Email
Messenger
Viber
iMessage
```

---

## Print

Supports:

```text
PDF
Gift Card
Certificate
Check Style
Bearer Instrument
```

---

# Operations Layout

## Purpose

Run and monitor execution.

---

## Operations Screens

```text
Execution Monitor
Settlement Envelopes
Evidence Center
Reconciliation
Audit Center
```

---

## Execution Monitor

Displays:

```text
Execution Queue
Driver
Pipeline
Status
Failures
Retries
```

---

## Evidence Center

Displays:

```text
Evidence Type
Voucher
Contact
Envelope
Submitted At
Review Status
```

---

## Reconciliation

Displays:

```text
Internal Ledger
Live Bank Transaction
Amount
Reference
Variance
Status
Notes
```

---

# Reports Layout

## Purpose

Provide intelligence and export.

---

## Report Categories

```text
Executive
Operational
Compliance
Geographic
Financial
```

---

## Report Layout

```text
Report Header
Filters
Visualizations
Table
Export Actions
```

---

# Administration Layout

## Purpose

Manage the platform.

---

## Sections

```text
Users
Roles
Permissions
Connected Services
API Access
Branding
Security
Feature Profiles
```

---

# About & Provenance Layout

## Purpose

Provide product identity and creator provenance.

---

## Sections

```text
Platform
Technology
Creator
Legal
Story
```

---

## Creator Section

Displays:

```text
Technology Inventor

Lester B. Hurtado

Creator of x-change and the Pay Code architecture.
```

---

## Easter Egg

Hidden interaction may reveal:

```text
Story of x-change
Timeline
Technology Journey
```

---

# PWA Layout Rules

PWA should simplify, not remove meaning.

---

## PWA Bottom Navigation

```text
Dashboard
Generate
Pay Codes
Funding
Profile
```

---

## PWA Adaptation

Desktop side nav becomes:

```text
Bottom nav
Overflow menu
```

Floating Issuance Palette becomes:

```text
Sticky bottom sheet
```

Tables become:

```text
Cards
Swipe actions
```

Evidence views become:

```text
Stacked sections
```

---

# Layout Invariants

Every layout must preserve:

```text
Money awareness
Operating identity
Action visibility
Auditability
Searchability
Professional density
```

---

# Guiding Statement

Every screen should answer:

```text
What is happening?

What matters?

What can I do?

What will happen if I act?
```
