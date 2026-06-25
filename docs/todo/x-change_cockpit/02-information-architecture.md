# 02-information-architecture.md

# x-change Cockpit
## Information Architecture

### Version
Draft v2

### Purpose

This document defines the logical information architecture of the x-change Cockpit.

The purpose of the architecture is to organize the application around the way institutions think about value movement rather than around database entities.

The architecture must support:

- Banks
- Government agencies
- LGUs
- Corporations
- Remittance centers
- Money changers
- Cooperatives
- NGOs

without requiring a different product for each.

---

# Architectural Mental Model

The Cockpit is organized around five domains:

```text
Money
People
Pay Codes
Operations
Intelligence
```

Everything in the application must belong to one or more of these domains.

---

# Primary Navigation

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

Secondary navigation:

```text
Approvals

Administration
```

Global capability:

```text
Ask x-change
```

---

# Information Hierarchy

The application is intentionally ordered by operational frequency.

Most frequent:

```text
Dashboard
Quick Generate
Pay Codes
```

Less frequent:

```text
Templates
Funding
Contacts
```

Administrative:

```text
Operations
Approvals
Reports
Administration
```

---

# Dashboard Domain

Purpose:

Provide immediate operational awareness.

Questions answered:

```text
Do I have money?

Is money moving?

Is anything stuck?

Is anything risky?

Do I need to act?
```

The dashboard is the operational cockpit.

It is not a reporting page.

---

## Dashboard Ownership

Owns:

```text
Liquidity

Redemption Activity

Funding Status

Batch Activity

Risk Indicators

Insights

Alerts
```

Does not own:

```text
Detailed searches

Voucher editing

Batch management

Template editing
```

---

# Quick Generate Domain

Purpose:

Enable issuance in under five seconds.

Quick Generate is a runtime.

Not a designer.

Not a composer.

---

## Quick Generate Ownership

Owns:

```text
Template selection

Runtime input collection

Immediate generation

Recent templates

Recent recipients
```

Does not own:

```text
Template authoring

Settlement design

Distribution design

Claim design
```

---

# Funding Domain

Purpose:

Manage value entering the system.

Funding is a first-class domain.

Funding is not merely a balance adjustment.

---

## Funding Ownership

Owns:

```text
Top Up

Deposit Pay Code

Funding Sources

Funding Ledger

Funding Reconciliation

Treasury Funding
```

---

## Funding Structure

```text
Funding

  Overview

  Top Up

  Deposit Pay Code

  Funding History

  Funding Reconciliation
```

---

# Pay Codes Domain

Purpose:

Manage value already created.

Pay Codes represent value in motion.

---

## Pay Codes Ownership

Owns:

```text
Explorer

Voucher Detail

Batches

Distribution

Archive

Exceptions
```

---

## Pay Codes Structure

```text
Pay Codes

  Explorer

  Batches

  Distribution

  Exceptions

  Archive
```

---

# Explorer

Purpose:

Search and operate on Pay Codes.

The Explorer is a financial search engine.

Not a table.

---

### Explorer Capabilities

```text
Search

Filter

Saved Views

Natural Language Queries

Bulk Operations
```

---

# Voucher Detail

Purpose:

Provide a complete forensic view of a Pay Code.

---

### Voucher Detail Sections

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

# Distribution Domain

Purpose:

Manage delivery and presentation of Pay Codes.

Distribution is a first-class lifecycle stage.

---

## Distribution Ownership

Owns:

```text
Links

QR

SMS

Email

Messenger

Physical Print

Certificates

Gift Cards

Bearer Instruments

Tracking

Analytics
```

---

# Templates Domain

Purpose:

Create reusable financial products.

Templates are institutional assets.

---

## Template Ownership

Owns:

```text
Composer

Claim Experience

Distribution Experience

Execution Configuration

Pricing Configuration

Settlement Configuration

Versioning
```

---

## Template Structure

```text
Templates

  Library

  Composer

  Versions

  Analytics
```

---

# Contacts Domain

Purpose:

Manage people and organizations participating in value movement.

---

## Contact Types

```text
Beneficiaries

Remitters

Approvers

Organizations

Groups
```

---

## Contact Ownership

Owns:

```text
Profiles

KYC

Relationships

Groups

Communication History
```

---

# Identity Domain

Purpose:

Manage operational identities.

Identity is separate from Contacts.

---

## Identity Ownership

Owns:

```text
Merchant Profiles

Vendor Aliases

Operating Identity

Identity Audit
```

---

## Identity Structure

```text
Identity

  Merchant Profiles

  Vendor Aliases

  Identity Audit
```

---

# Operations Domain

Purpose:

Operate the institution.

Operations owns workflow execution.

---

## Operations Structure

```text
Operations

  Execution Monitor

  Settlement Envelopes

  Evidence Center

  Reconciliation

  Audit Center
```

---

# Execution Monitor

Purpose:

Observe lifecycle execution.

Owns:

```text
Execution Drivers

Execution Pipelines

Execution Failures

Lifecycle Monitoring
```

---

# Settlement Envelope Workspace

Purpose:

Manage structured settlement processes.

Owns:

```text
Parties

Payload

Evidence

Approvals

Required Documents

Readiness Gates
```

---

# Evidence Center

Purpose:

Manage evidence as a first-class asset.

Owns:

```text
Signatures

Selfies

KYC

Receipts

Certificates

Uploaded Files
```

---

# Reconciliation

Purpose:

Manage operational reconciliation.

---

## Reconciliation Rules

Supports:

```text
Matching

Notes

Disputes

Exception Handling

Correction Entries
```

Must not support:

```text
Silent financial manipulation

Historical rewriting
```

---

# Audit Center

Purpose:

Provide complete forensic traceability.

Owns:

```text
System Events

User Events

Voucher Events

Administrative Events
```

---

# Approvals Domain

Purpose:

Support maker-checker workflows.

---

## Approvals Structure

```text
Approvals

  Pending

  Assigned

  History

  Rules
```

---

## Approval Scope

May govern:

```text
Funding

Voucher Actions

Batch Actions

Identity Changes

Administrative Actions
```

---

# Reports Domain

Purpose:

Provide operational and executive intelligence.

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

## Export Capabilities

```text
CSV

Excel

Data Warehouse

API
```

---

# Ask x-change Domain

Purpose:

Natural language interaction layer.

Accessible globally.

---

## Ask x-change Capabilities

```text
Search

Insights

Commands

Recommendations
```

Examples:

```text
Show vouchers redeemed in Quezon City.

Show expiring vouchers.

Generate a ₱5,000 remittance Pay Code.

Which batches are at risk?
```

---

# Administration Domain

Purpose:

Manage the platform.

---

## Administration Structure

```text
Administration

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

# Profile Domain

Accessible from the global header.

Purpose:

Manage user-specific preferences.

---

## Profile Structure

```text
Profile

  Personal Information

  Security

  API Access

  Workspace

  Appearance

  About
```

---

# About & Provenance

Purpose:

Provide platform history and attribution.

---

## About Structure

```text
Platform

Technology

Creator

Legal

Roadmap

Story of x-change
```

---

## Creator Attribution

Display:

```text
Technology Inventor

Lester B. Hurtado

Creator of x-change and the Pay Code architecture.
```

Institution ownership remains primary.

Technology provenance remains discoverable.

---

# Information Architecture Invariant

The Cockpit must always organize information according to:

```text
Money
  ↓

People
  ↓

Pay Codes
  ↓

Operations
  ↓

Intelligence
```

and never according to database tables, implementation details, or underlying packages.
