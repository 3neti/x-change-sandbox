# 03-navigation-model.md

# x-change Cockpit
## Navigation Model

### Version
Draft v1

### Purpose

This document defines the navigation model of the x-change Cockpit.

Navigation should support:

- High-volume operators
- Branch users
- Treasury users
- Compliance officers
- Executives
- Administrators

The navigation model must optimize for:

- Speed
- Discoverability
- Operational awareness
- Context retention

The navigation model should feel closer to:

- Bloomberg Terminal
- Trading Workstation
- Treasury Operations Console

than a traditional admin panel.

---

# Navigation Philosophy

## Principle 1
### Navigation Must Reflect Work

Users think:

```text id="fr7x9m"
Generate

Monitor

Approve

Investigate

Reconcile
```

not:

```text id="z95lsh"
Create

Edit

Delete
```

Navigation should reflect workflows rather than CRUD operations.

---

## Principle 2
### Context Must Never Be Lost

Users must always know:

```text id="vt9r7l"
Who am I?

Where am I?

How much money do I have?

What needs attention?
```

without changing pages.

---

## Principle 3
### Money Is Always Visible

Balances should remain visible throughout the application.

Navigation must continuously expose:

```text id="9w4yq4"
Internal Balance

Live Balance

Funding Alerts
```

---

## Principle 4
### Search Is Navigation

Users should not be forced to navigate through menus.

Search should be a primary navigation mechanism.

Natural language search should eventually become a first-class navigation capability.

---

# Desktop Navigation Model

Desktop is the primary experience.

The desktop layout should contain:

```text id="6h5zrz"
Global Header

Primary Navigation

Workspace Area

Floating Utilities

Context Panels
```

---

# Global Header

Always visible.

Purpose:

Provide operational context.

---

## Header Layout

```text id="dcg7u6"
Institution

Operating Identity

Balances

Notifications

Ask x-change

Profile
```

Example:

```text id="t7ct3j"
DBP Pay Code

Operating As:
Treasury Operations

Internal:
₱125M

Live:
₱123M
```

---

# Primary Navigation

Persistent left navigation.

Always visible on desktop.

---

## Primary Navigation Structure

```text id="udn6r9"
Dashboard

Quick Generate

Funding

Pay Codes

Templates

Contacts

Operations

Reports
```

---

## Secondary Navigation

Displayed below primary navigation.

```text id="4w3jqq"
Approvals

Administration
```

---

# Navigation Ordering

The order reflects frequency of use.

Most frequent:

```text id="ljd58h"
Dashboard

Quick Generate

Pay Codes
```

Less frequent:

```text id="jlwmam"
Templates

Funding

Contacts
```

Administrative:

```text id="qbvdb4"
Operations

Approvals

Reports

Administration
```

---

# Navigation States

Each navigation item may display status indicators.

Examples:

```text id="g3g0mp"
Approvals (12)

Operations (3)

Funding (Alert)

Reports
```

The goal is to surface operational attention points.

---

# Dashboard Navigation

The Dashboard is the home page.

All users should land here after login.

Dashboard should function as:

```text id="zv8mg5"
Command Center

Operational Overview

Action Hub
```

---

# Quick Generate Navigation

Quick Generate is accessible through:

```text id="vzmkzq"
Dashboard

Navigation Menu

Global Action Button

Command Palette
```

Quick Generate should never be more than one click away.

---

# Command Palette

Purpose:

Universal navigation and command execution.

---

## Invocation

Desktop:

```text id="yd91pk"
⌘ + K

Ctrl + K
```

---

## Capabilities

Support:

```text id="nl8q5t"
Navigate

Search

Generate

Open

Approve

View
```

Examples:

```text id="m8p2av"
Go to Funding

Show expiring vouchers

Open batch 2026-001

Generate ₱5,000 Pay Code
```

---

# Ask x-change

Purpose:

Natural language interaction layer.

Accessible globally.

---

## Placement

Header.

Always visible.

---

## Scope

Supports:

```text id="5p8jzq"
Search

Insights

Commands

Recommendations
```

Ask x-change is not a chatbot page.

It is a global operational assistant.

---

# Breadcrumb Model

Purpose:

Preserve context.

---

## Example

```text id="hwwt3o"
Pay Codes

  Explorer

    Voucher ABC123
```

Displayed as:

```text id="lqf4gu"
Pay Codes
>
Explorer
>
Voucher ABC123
```

---

## Rules

Breadcrumbs should reflect:

- Navigation hierarchy
- User path

not database relationships.

---

# Context Bar

Purpose:

Display operational context.

---

## Placement

Immediately below header.

---

## Contents

May display:

```text id="gx7xfr"
Current Batch

Current Template

Current Merchant Profile

Current Voucher

Current Envelope
```

Example:

```text id="gbf7n4"
Template:
OFW Remittance

Merchant:
DBP Treasury
```

---

# Floating Issuance Palette

Purpose:

Support voucher generation workflows.

---

## Behavior

Always visible while composing.

Supports:

```text id="ncn57e"
Pricing

Funding Impact

Connectivity

Draft Status

Actions
```

---

## Actions

```text id="eahjlwm"
Save Draft

Save Template

Generate Pay Code

Generate Batch
```

---

# Notification Center

Purpose:

Surface actionable events.

---

## Categories

```text id="7l74r8"
Approvals

Funding

Redemptions

Failures

Alerts
```

---

## Rules

Notifications must be actionable.

Avoid informational noise.

---

# Workspace Panels

Purpose:

Provide contextual information.

---

## Examples

```text id="wbefuj"
Voucher Summary

Funding Summary

Evidence Summary

Settlement Summary
```

Panels should collapse when not needed.

---

# Voucher Explorer Navigation

The Explorer should support:

```text id="a8r39u"
Search

Filters

Saved Views

Bulk Actions
```

without requiring navigation away from the page.

---

# Voucher Detail Navigation

Voucher Detail should use tab navigation.

---

## Tabs

```text id="d5x73h"
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

Tabs preserve user context while reducing navigation complexity.

---

# Settlement Envelope Workspace

Purpose:

Complex workflows.

---

## Navigation Style

Use:

```text id="l1whkg"
Workspace

Wizard

Sections
```

not simple forms.

---

## Sections

```text id="ztdvyl"
Parties

Case Details

Evidence

Required Documents

Approvals

Review
```

---

# Distribution Workspace

Purpose:

Manage delivery.

---

## Sections

```text id="yj7f3u"
Delivery

Branding

Print

Analytics

History
```

---

# Reports Navigation

Reports should be organized by intent.

Not by source table.

---

## Categories

```text id="c8hjlwm"
Executive

Operational

Compliance

Geographic

Financial
```

---

# Profile Navigation

Profile menu accessible from header.

---

## Structure

```text id="d3ew7o"
Profile

Security

API Access

Workspace

Appearance

About
```

---

# Workspace Profiles

Navigation may adapt based on role.

---

## Treasury

Prioritize:

```text id="5z5ujm"
Balances

Funding

Liquidity

Reconciliation
```

---

## Branch

Prioritize:

```text id="shsgu0"
Quick Generate

Recent Activity

Recipients
```

---

## Compliance

Prioritize:

```text id="fcjhrd"
Evidence

Audit

Approvals
```

---

## Executive

Prioritize:

```text id="6lw3yn"
Insights

Reports

Risk Indicators
```

---

# PWA Navigation Model

PWA is a secondary experience.

---

## Bottom Navigation

Recommended:

```text id="utrmfe"
Dashboard

Generate

Pay Codes

Funding

Profile
```

---

## Overflow Menu

Contains:

```text id="cfzfrk"
Templates

Contacts

Operations

Reports

Administration
```

---

# Navigation Invariants

The navigation model must always ensure:

```text id="rcw5h0"
Money is visible.

Context is visible.

Search is immediate.

Generation is immediate.

Approvals are discoverable.

Operations are observable.
```

---

# Guiding Statement

The navigation system exists to minimize the distance between intention and action.

Users should never wonder:

Where do I go?

What do I click?

What do I do next?

The navigation should make the answer obvious.
