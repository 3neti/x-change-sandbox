# 09-mobile-and-pwa-strategy.md

# x-change Cockpit
## Mobile and PWA Strategy

### Version
Draft v1

### Purpose

This document defines the mobile and Progressive Web App (PWA) strategy for the x-change Cockpit.

The objective is to provide:

- Excellent desktop experience
- Strong operational PWA experience
- Future native mobile readiness

without compromising the primary mission of the Cockpit.

---

# Strategic Position

## Principle 1
### Desktop Is The Primary Platform

The Cockpit is fundamentally:

```text id="m1a1x2"
Treasury Workstation

Operations Console

Settlement Center

Financial Intelligence Platform
```

These workflows are naturally desktop-oriented.

Desktop is the primary design target.

---

## Principle 2
### PWA Is The Secondary Platform

PWA exists to support:

```text id="b1a2x3"
Field Operations

Approvals

Monitoring

Quick Actions

Management Visibility
```

PWA is not a replacement for desktop.

---

## Principle 3
### Native Mobile Is A Future Option

Native mobile applications may eventually support:

```text id="c1a3x4"
Field Collection

Branch Operations

Executive Dashboards

Merchant Operations
```

but are outside the scope of the current Cockpit initiative.

---

# Platform Hierarchy

```text id="d1a4x5"
Desktop
  ↓

PWA
  ↓

Native Mobile
```

Features should be designed in this order.

---

# User Categories

## Desktop Users

Examples:

```text id="e1a5x6"
Treasury

Operations

Compliance

Administrators

Settlement Teams
```

---

## PWA Users

Examples:

```text id="f1a6x7"
Branch Personnel

Approvers

Managers

Executives

Field Personnel
```

---

## Future Native Users

Examples:

```text id="g1a7x8"
Sales Teams

Collectors

Inspectors

Auditors
```

---

# Mobile Philosophy

## Mobile Is For Action

Mobile users typically need to:

```text id="h1a8x9"
Approve

Monitor

Generate Quickly

Search

Share

Respond
```

---

## Mobile Is Not For Construction

Complex authoring belongs on desktop.

Examples:

```text id="i1a9x0"
Template Composer

Settlement Envelope Design

Claim Experience Design

Distribution Design

Large Batch Creation
```

---

# Desktop-Only Experiences

The following workflows should remain desktop-first.

---

## Template Composer

Reason:

```text id="j2a1x1"
High complexity

Many sections

Heavy configuration
```

---

## Settlement Envelope Workspace

Reason:

```text id="k2a2x2"
Evidence management

Complex visibility rules

Approval structures
```

---

## Distribution Workspace

Reason:

```text id="l2a3x3"
Print design

Branding configuration

Analytics
```

---

## Reconciliation

Reason:

```text id="m2a4x4"
Table-heavy

Comparison-heavy

Operational complexity
```

---

## Administration

Reason:

```text id="n2a5x5"
System-level configuration
```

---

# Mobile-Optimized Experiences

These workflows should be excellent on PWA.

---

## Quick Generate

Target:

```text id="o2a6x6"
Under 5 seconds
```

---

## Approvals

Target:

```text id="p2a7x7"
Review and approve in under 30 seconds
```

---

## Voucher Search

Target:

```text id="q2a8x8"
Locate information immediately
```

---

## Voucher Sharing

Target:

```text id="r2a9x9"
One-handed operation
```

---

## Dashboard Monitoring

Target:

```text id="s2a0x0"
Glanceable awareness
```

---

# Responsive Strategy

## Rule

Do not remove meaning.

Adapt presentation.

---

## Example

Desktop:

```text id="t3a1x1"
Table
```

PWA:

```text id="u3a2x2"
Card List
```

---

## Example

Desktop:

```text id="v3a3x3"
Floating Issuance Palette
```

PWA:

```text id="w3a4x4"
Sticky Bottom Sheet
```

---

## Example

Desktop:

```text id="x3a5x5"
Side Navigation
```

PWA:

```text id="y3a6x6"
Bottom Navigation
```

---

# PWA Navigation Model

## Bottom Navigation

Always visible.

```text id="z3a7x7"
Dashboard

Generate

Pay Codes

Funding

Profile
```

---

## Overflow Menu

Contains:

```text id="a4b1c1"
Templates

Contacts

Operations

Reports

Administration
```

---

# PWA Dashboard

## Purpose

Provide operational awareness.

---

## Priority Order

```text id="b4b2c2"
Balance

Risk

Actions

Activity
```

---

## Layout

```text id="c4b3c3"
Balance Hero

Quick Actions

Risk Alerts

Recent Activity

Insights
```

---

# PWA Quick Generate

## Design Goal

Fastest workflow in the system.

---

## Layout

```text id="d4b4c4"
Template

Amount

Recipient

Generate
```

---

## Optional

```text id="e4b5c5"
Scan Contact

Recent Recipient

Voice Input
```

---

# PWA Voucher Detail

## Structure

Use stacked sections.

---

## Tabs

```text id="f4b6c6"
Overview

Timeline

Evidence

Distribution

Audit
```

---

## Rule

Avoid excessive nested navigation.

---

# PWA Funding

## Primary Functions

```text id="g4b7c7"
Top Up

Deposit Pay Code

Balance

History
```

---

## Optimizations

Support:

```text id="h4b8c8"
Camera QR Scan

Paste Code

Deep Links
```

---

# PWA Approvals

## Purpose

Enable rapid maker-checker actions.

---

## Layout

```text id="i4b9c9"
Request Summary

Impact Summary

Approve

Reject
```

---

## Rule

No scrolling through massive forms.

Approvers need concise information.

---

# PWA Notifications

## Purpose

Bring users back into workflows.

---

## Notification Types

```text id="j4c1d1"
Approval Required

Funding Alert

Expiry Alert

Batch Failure

Evidence Submitted
```

---

## Actions

Notifications should deep-link directly into the task.

---

# Offline Strategy

## Principle

The PWA should degrade gracefully.

---

## Offline Supported

```text id="k4c2d2"
View Cached Dashboard

View Recent Pay Codes

Prepare Draft Issuance

Review Drafts
```

---

## Online Required

```text id="l4c3d3"
Generate Pay Code

Approve Financial Action

Funding

Settlement Execution
```

---

# Connectivity Awareness

## Requirement

Connectivity status should always be visible.

---

## States

```text id="m4c4d4"
Online

Offline

Degraded
```

---

## Actions

The system should explain:

```text id="n4c5d5"
What works

What does not work
```

when connectivity changes.

---

# Camera Strategy

The camera becomes a first-class input.

---

## Supported Use Cases

```text id="o4c6d6"
QR Scan

Pay Code Scan

Evidence Capture

Document Upload

Selfie Verification
```

---

# Sharing Strategy

## Mobile Strength

PWA should excel at sharing.

---

## Supported Targets

```text id="p4c7d7"
SMS

Messenger

Viber

Email

iMessage

Native Share Sheet
```

---

## Goal

Sharing should feel native.

---

# Home Screen Installation

PWA should support installation.

---

## Benefits

```text id="q4c8d8"
Full Screen

Offline Cache

Push Notifications

App-like Experience
```

---

# Push Notification Strategy

Push notifications should be operational.

Not marketing.

---

## Examples

```text id="r4c9d9"
Voucher Expiring

Approval Required

Funding Low

Batch Failed

Evidence Submitted
```

---

## Avoid

```text id="s4d1e1"
Promotional Messages

Engagement Campaigns

Unnecessary Reminders
```

---

# Future Native Mobile Strategy

Native mobile may eventually focus on:

```text id="t4d2e2"
Branch Operations

Merchant Operations

Field Collection

Executive Monitoring
```

---

## Native Advantages

```text id="u4d3e3"
Biometrics

Background Sync

Native Wallet Integration

Siri Shortcuts

Voice Commands
```

---

# Redeem-X Alignment

The Cockpit should leverage proven concepts from redeem-x.

Examples:

```text id="v4d4e4"
PWA Installability

Offline Awareness

QR Flows

Share Experience

OG Preview Strategy

Claim-Centric Mobile UX
```

However:

```text id="w4d5e5"
Cockpit ≠ Redeem-X
```

The Cockpit serves operators.

Redeem-X serves claimants.

---

# Mobile Experience Invariants

Every mobile experience must preserve:

```text id="x4d6e6"
Balance Awareness

Operational Context

Auditability

Actionability

Connectivity Awareness
```

---

# Forbidden Mobile Compromises

Do not:

```text id="y4d7e7"
Simplify away financial visibility

Hide audit information

Remove operational status

Replace workflows with opaque automation
```

---

# Success Criteria

A user on mobile should be able to:

```text id="z4d8e8"
Generate a Pay Code

Approve a request

Monitor a batch

Check balances

Share a Pay Code

Review evidence
```

without needing a desktop.

A user should not be expected to:

```text id="a5d9e9"
Design templates

Configure settlement workflows

Perform reconciliation

Administer the platform
```

from a phone.

---

# Guiding Statement

The PWA exists to keep operators connected to value in motion.

Desktop is where value is designed.

PWA is where value is monitored, approved, generated, and acted upon.

The goal is not feature parity.

The goal is operational continuity.
