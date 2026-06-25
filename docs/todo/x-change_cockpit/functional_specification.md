# x-change Cockpit
## Comprehensive UI/UX Functional Specification Inventory

### Purpose

This document enumerates all functional capabilities, screens, user experiences, workflows, and operational concepts discussed for the future x-change Cockpit.

This is NOT an implementation plan.

This is NOT a technical architecture document.

This is a functional specification inventory intended to guide future UI/UX design, product design, and implementation.

---

# Design Philosophy

The x-change Cockpit is not a voucher management system.

The x-change Cockpit is a:

- Financial Workflow Platform
- Pay Code Operations Platform
- Settlement Platform
- Distribution Platform
- Evidence Platform
- Financial Intelligence Platform

The UI should feel closer to:

- Bloomberg Terminal
- Treasury Operations Console
- Financial Operations Center
- Payment Network Operations Center

than a traditional CRUD application.

The design should be:

- Professional
- Institutional
- Banking-grade
- Audit-friendly
- Highly visual
- Operationally efficient

---

# Global UX Principles

## Template First

Templates are first-class citizens.

Operators should normally issue from templates.

The system should encourage:

Template → Generate

rather than

Generate → Save Template

---

## Quick Generate

The most common workflow must be executable in under 5 seconds.

Examples:

- Money changer
- Remittance center
- Pawnshop
- Micro-lender
- Branch teller

Workflow:

Select Template
Enter Amount
Enter Recipient
Generate

---

## Composer Mode

Advanced voucher construction is performed through a Pay Code Composer.

The Composer is used for:

- Template creation
- Complex issuance
- Settlement envelopes
- Claim experience
- Distribution design
- Execution engine configuration

---

## Floating Issuance Palette

The Composer must include a floating palette.

Always visible.

Contains:

- Pricing
- Funding impact
- Connectivity status
- Draft status
- Template
- Execution summary
- Generate actions

No scrolling should be required to issue a Pay Code.

---

## Dashboard Philosophy

The dashboard should answer:

Do I have money?

Is money moving?

Is anything stuck?

Is anything risky?

Do I need to act?

---

# Dashboard

## Liquidity Center

Display prominently:

- Internal balance
- Live balance
- Reserved funds
- Pending settlement
- Available to issue

Balances are the most important information in the system.

---

## Balance Trends

Historical balance graphs:

- 7 days
- 30 days
- 90 days
- 1 year

---

## Redemption Velocity

Display:

- Issued
- Shared
- Opened
- Claim Started
- Redeemed
- Disbursed
- Reconciled

Trend analysis included.

---

## Expected Redemptions

Forecast:

- Today
- This Week
- This Month

---

## Expiry Risk Center

Display:

- Expiring today
- Expiring this week
- Expiring this month

Actions:

- Remind recipients
- Extend expiry

---

## Low Balance Warnings

Display:

- Funding runway
- Estimated depletion date
- Projected redemptions

---

## Batch Command Center

Display:

- Batch success rates
- Batch redemption rates
- Batch failures
- Batch exceptions

---

## Redemption Heat Map

Display geographic redemption activity.

Examples:

- Quezon City
- NCR
- Cebu
- Davao

Metrics:

- Count
- Value
- Success rate
- Failure rate

---

## Risk Radar

Display:

- Expiring soon
- Not shared
- Abandoned claims
- Pending KYC
- Failed disbursements
- Pending evidence

---

## AI Insight Panel

Display generated operational insights.

Examples:

- Unusual redemption activity
- Expiring batches
- Outstanding evidence requests
- Balance risks

---

# Contacts

## Contact Management

First-class module.

Types:

- Beneficiaries
- Approvers
- Remitters
- Organizations

---

## Contact Profiles

Store:

- Name
- Mobile
- Email
- Address
- Bank accounts
- KYC
- Redemption history
- Notes

---

## Contact Groups

Examples:

- OFW Families
- Scholarship Beneficiaries
- Payroll Group
- LGU Program

Used for batch issuance.

---

## Contact Enrichment

Successful redemptions should enrich contact profiles.

---

# Identity

## Operating Identity

The active identity under which actions are performed.

Examples:

- Personal
- Meralco
- Starbucks
- DBP Treasury

Visible throughout the application.

---

## Merchant Profiles

Store:

- Business information
- Address
- Branding
- Contacts

---

## Vendor Aliases

Approved aliases.

Examples:

- MERALCO
- STARBUCKS

Used for:

- Vendor-payable vouchers
- Deposit restrictions
- Authorization

---

## Vendor Alias Approval

Centralized approval workflow.

---

# Funding

## Funding Dashboard

Display:

- Internal wallet
- Live balance
- Pending funding
- Settlement status

---

## Funding Sources

Support:

- QR Ph
- InstaPay
- Bank transfer
- Treasury transfer
- Manual funding

---

## Deposit Pay Code

Allow a Pay Code to fund a wallet.

Separate from Claim.

Workflow:

Pay Code → Deposit → Wallet Credit

---

## Funding Ledger

Track:

- Funding history
- Sources
- Reconciliation status

---

# Templates

## Template Management

Support:

- Create
- Edit
- Clone
- Publish
- Archive

---

## Template Categories

Examples:

- Money Changer
- OFW Remittance
- Medical Assistance
- Payroll
- Settlement Envelope

---

## Template Versioning

Templates must be versioned.

Issued Pay Codes use snapshots.

---

# Pay Code Composer

## Purpose

Select:

- Cash Disbursement
- Merchant Payable
- Deposit
- Settlement Envelope
- Stored Value
- Authority Voucher

---

## Amount

Support:

- Amount
- Currency
- Funding source
- Pricing impact

---

## Payable Target

Support:

- Anonymous
- Specific recipient
- Contact
- Vendor alias
- Merchant profile

---

## Claim Inputs

Support:

- Name
- Mobile
- Email
- Address
- Birth Date
- Signature
- Selfie
- KYC
- Bank Account
- Location

---

## Validation Rules

Support:

- OTP
- Location
- Face Match
- Time Window
- Vendor Alias
- KYC

---

## Execution Engine

Allow selection of execution behavior.

Display:

- Driver
- Pipeline
- Execution summary

---

# Settlement Envelope Workspace

## Purpose

Manage:

- Payload
- Evidence
- Settlement metadata

---

## Parties

Support:

- Beneficiary
- Issuer
- Institution
- Counterparty

---

## Case Details

Support domain-specific metadata.

Examples:

- PhilHealth BST
- Medical claims
- Reimbursements

---

## Evidence

Support:

- PDF
- Images
- Documents

---

## Required Documents

### Standard

Template-driven.

Examples:

- Medical certificate
- Employer contribution records
- Employee ID

### Ad Hoc

Issuer-defined.

Examples:

- Receipt
- Affidavit
- Certification

---

## Visibility Rules

Control visibility for:

- Redeemer
- Issuer
- Approver
- Institution

---

## Envelope Status

Examples:

- Draft
- Ready
- Awaiting Evidence
- Awaiting Approval

---

# Claim Experience

## Splash Page

Configurable.

---

## Rider Message

Rich content.

---

## CTA Buttons

Configurable.

---

## Rider URL

Configurable.

---

## Touch Analytics

Track:

- Viewed
- Clicked
- Redirected

---

# Share Experience

## OG Metadata

Support:

- Title
- Description
- Image

---

## Share Preview

Messenger
Viber
iMessage
Email

---

## Share Stamp

Visual identity asset.

---

# Distribution Workspace

## Digital Distribution

Support:

- Link
- QR
- SMS
- Email
- Messenger
- Viber

---

## Physical Distribution

Support:

- PDF
- Print
- Gift Card
- Certificate
- Check Style
- Bearer Instrument

---

## Distribution Tracking

Track:

- Shared
- Delivered
- Opened
- Claimed

---

## Distribution Analytics

Track effectiveness.

---

## Distribution Status

Examples:

- Not Shared
- Shared
- Opened
- Claim Started
- Redeemed

---

# Voucher Explorer

## Search

Natural language search.

Examples:

- Redeemed in Quezon City last year
- Redeemed by Juan Dela Cruz above 2,000

---

## Advanced Filters

Support:

- Date ranges
- Amount ranges
- Status
- Contact
- Location
- Batch
- Template
- Execution Driver
- Vendor Alias
- Settlement Envelope

---

## Saved Views

Support reusable searches.

---

## Bulk Actions

Support:

- Extend expiry
- Cancel
- Resend
- Export

---

# Voucher Detail

## Overview

Summary information.

---

## Timeline

Lifecycle events.

---

## Claim Evidence

Display:

- Signature
- Selfie
- Location
- KYC
- Uploaded documents

---

## Execution

Display execution details.

---

## Settlement Envelope

Display associated envelope.

---

## Distribution

Display sharing history.

---

## Pricing

Display costs and balance impacts.

---

## Audit Trail

Display:

- Who
- What
- When
- Why
- Before
- After

---

# Operational Actions

## Voucher Actions

Support:

- Share
- Extend Expiry
- Cancel
- Reissue
- Export Evidence

---

## Batch Actions

Support:

- Cancel Batch
- Extend Expiry
- Resend Distribution

---

## Approval Support

Sensitive actions may require:

- Reason
- Approval
- Audit logging

---

# Settings

## Personal

Profile management.

---

## Security

Support:

- MFA
- Devices
- Sessions
- Logout all sessions

---

## API Access

Support:

- Token creation
- Scope management
- Rotation
- Usage monitoring

---

## Connected Services

Support:

- SMS
- OTP
- Webhooks
- Providers

---

# AI Copilot

## Natural Search

Search operational data.

---

## Command Execution

Support:

- Generate Pay Code
- Create Batch
- Create Template

Requires confirmation.

---

## Voice Commands

Future support.

Examples:

- Generate Pay Code
- Check Balance
- Pending Approvals

---

## Siri / Voice Assistant Integration

Future capability.

---

# Workspace Profiles

Support:

- Operations
- Treasury
- Compliance
- Executive
- Branch

---

# Branding Profiles

Support:

- DBP
- PhilHealth
- LGU
- Corporate

---

# Appearance

Support:

- Light
- Dark
- Auto

---

# About & Provenance

## Platform Information

Display:

- Product
- Version
- Environment

---

## Technology Information

Display:

- Powered by x-change

---

## Creator Information

Display:

Technology Inventor

Lester B. Hurtado

Creator of x-change and the Pay Code architecture.

---

## Legal

Display:

- EULA
- Licensing
- Patent information

---

## Easter Egg

Hidden discoverable experience.

Examples:

- Story of x-change
- Platform timeline
- Technology journey

Purpose:

Leave a tasteful creator signature without disrupting institutional ownership.
