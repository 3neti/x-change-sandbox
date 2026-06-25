# x-change Cockpit
## Information Architecture (IA)

### Version
Draft v1

### Purpose

This document defines the logical information architecture of the x-change Cockpit.

This is not a screen design.

This is not a technical architecture.

This document defines:

- Navigation hierarchy
- Primary modules
- User mental models
- Relationships between modules
- Screen ownership

---

# Core Navigation Philosophy

The application should be organized around:

1. Money
2. Pay Codes
3. People
4. Operations
5. Intelligence

Not around database tables.

The user should intuitively understand:

```text
Where is my money?

What Pay Codes exist?

Who are the people involved?

What needs attention?

What is happening?
```

---

# Global Navigation

```text
Dashboard

Quick Generate

Funding

Pay Codes

Templates

Contacts

Approvals

Operations

Reports

Administration
```

---

# Global Header

Always visible.

```text
Institution Name

Operating Identity

Internal Balance

Live Balance

Notifications

Ask x-change

Profile
```

Examples:

```text
DBP Pay Code

Operating As:
Treasury Operations
```

---

# Dashboard

Purpose:

Operational awareness.

Answers:

```text
Do I have money?

Is money moving?

Do I need to act?
```

Subsections:

```text
Liquidity

Redemption Activity

Batch Activity

Risk Center

Insights

Recent Activity
```

---

# Quick Generate

Purpose:

Fast issuance.

Subsections:

```text
Template Selector

Runtime Inputs

Generate

Recent Templates
```

---

# Funding

Purpose:

Manage incoming value.

---

## Funding Dashboard

Displays:

```text
Internal Balance

Live Balance

Reserved Funds

Pending Settlement
```

---

## Top Up

Funding methods:

```text
QR Ph

InstaPay

Bank Transfer

Treasury Funding
```

---

## Deposit Pay Code

Purpose:

Fund wallet using Pay Code.

---

## Funding History

Displays:

```text
Funding Events

Deposits

Top Ups

Funding Reconciliation
```

---

# Pay Codes

Purpose:

Explore, manage, audit, and operate Pay Codes.

---

## Explorer

Primary search experience.

Supports:

```text
Search

Filters

Saved Views

Bulk Actions
```

---

## Batches

Batch management.

Displays:

```text
Batch Status

Batch Redemption

Batch Distribution

Batch Exceptions
```

---

## Distribution

Manage delivery of Pay Codes.

Displays:

```text
Digital Distribution

Physical Distribution

Delivery Tracking

Campaign Analytics
```

---

## Exceptions

Displays:

```text
Failed Distribution

Failed Claims

Failed Disbursements

Pending Review
```

---

## Archive

Historical records.

---

# Pay Code Detail

Structure:

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

# Templates

Purpose:

Design reusable financial products.

---

## Template Library

Displays:

```text
Draft

Published

Archived
```

---

## Composer

The primary authoring experience.

Sections:

```text
Purpose

Amount

Payable Target

Claim Inputs

Validation

Settlement

Execution

Claim Experience

Distribution Experience

Pricing

Review
```

---

## Versions

Displays:

```text
Version History

Differences

Publishing Status
```

---

## Analytics

Displays:

```text
Usage

Success Rates

Redemption Rates

Template Performance
```

---

# Contacts

Purpose:

Manage people and organizations.

---

## Beneficiaries

Displays:

```text
Contacts

History

KYC

Redemption Activity
```

---

## Remitters

Displays:

```text
Organizations

Individuals

Funding Sources
```

---

## Approvers

Displays:

```text
Approvers

Approval Limits

Approval Activity
```

---

## Organizations

Displays:

```text
Institutions

Businesses

Government Agencies
```

---

## Groups

Displays:

```text
OFW Families

Scholarships

Payroll Groups

Program Groups
```

---

# Identity

Purpose:

Manage operational identities.

---

## Merchant Profiles

Displays:

```text
Business Information

Addresses

Branding
```

---

## Vendor Aliases

Displays:

```text
Approved Aliases

Pending Approval

Rejected Aliases
```

---

## Identity Audit

Displays:

```text
Alias Changes

Ownership History

Approval History
```

---

# Approvals

Purpose:

Maker-checker workflows.

---

## Pending

Displays:

```text
Voucher Actions

Batch Actions

Funding Actions

Administrative Actions
```

---

## My Approvals

Displays:

```text
Assigned Items

History
```

---

## Approval Rules

Displays:

```text
Approval Limits

Approval Chains

Escalation Rules
```

---

# Operations

Purpose:

Run the institution.

---

## Execution Monitor

Displays:

```text
Voucher Lifecycle

Execution Pipelines

Execution Drivers

Failures
```

---

## Settlement Envelopes

Displays:

```text
Draft Envelopes

Ready Envelopes

Pending Evidence

Pending Approval
```

---

## Evidence Center

Displays:

```text
Documents

Receipts

Medical Certificates

Uploaded Evidence
```

---

## Reconciliation

Displays:

```text
Matched

Unmatched

Exceptions

Variance
```

This module must support:

```text
Notes

Disputes

Exception Handling

Correction Entries
```

Not silent modification of financial records.

---

## Audit Center

Displays:

```text
System Activity

Voucher Activity

User Activity

Administrative Activity
```

---

# Reports

Purpose:

Operational and executive reporting.

---

## Executive Reports

Displays:

```text
Value Moved

Redemption Trends

Liquidity Trends

Institution Metrics
```

---

## Operational Reports

Displays:

```text
Voucher Reports

Batch Reports

Distribution Reports

Exception Reports
```

---

## Compliance Reports

Displays:

```text
Audit Reports

Evidence Reports

KYC Reports
```

---

## Geographic Reports

Displays:

```text
Heat Maps

Regional Activity

Location Intelligence
```

---

## Data Export

Supports:

```text
CSV

Excel

Warehouse Export

API Export
```

---

# Administration

Purpose:

Manage the platform.

---

## Users

Displays:

```text
Users

Roles

Permissions
```

---

## Feature Profiles

Displays:

```text
Workspace Profiles

Institution Profiles

Feature Flags
```

---

## Connected Services

Displays:

```text
SMS

OTP

KYC

Providers

Webhooks
```

---

## API Access

Displays:

```text
Tokens

Scopes

Usage
```

---

## Branding

Displays:

```text
Institution Branding

Logos

Colors

Distribution Branding

Stamp Assets
```

---

## Security

Displays:

```text
MFA

Sessions

Devices

Security Events
```

---

# Ask x-change

Purpose:

Natural-language interaction layer.

Accessible globally.

Supports:

```text
Search

Analytics

Commands

Insights
```

Examples:

```text
Show vouchers redeemed in Quezon City.

Generate a ₱5,000 Pay Code.

Show expiring vouchers.
```

---

# Profile Menu

```text
Profile

Security

API Access

Workspace

Appearance

About
```

---

# About & Provenance

Purpose:

Explain the platform.

---

## Platform

Displays:

```text
Product

Version

Build

Environment
```

---

## Technology

Displays:

```text
x-change

Execution Engine

Settlement Envelope

Claim Compiler
```

---

## Creator

Displays:

```text
Technology Inventor

Lester B. Hurtado

Creator of x-change and the Pay Code architecture.
```

---

## Legal

Displays:

```text
EULA

Licensing

Patent Information
```

---

## Easter Egg

Hidden discoverable experience.

Examples:

```text
The Story of x-change

Technology Timeline

Platform Journey
```

Purpose:

Create a memorable signature while preserving institutional ownership.
