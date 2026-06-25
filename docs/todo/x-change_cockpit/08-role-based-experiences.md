# 08-role-based-experiences.md

# x-change Cockpit
## Role-Based Experiences

### Version
Draft v1

### Purpose

This document defines how the x-change Cockpit adapts to different user roles.

The objective is not to create separate applications.

The objective is to create different operational perspectives on the same platform.

Different users care about different things:

A branch operator wants speed.

A treasury officer wants liquidity.

A compliance officer wants evidence.

An executive wants visibility.

The system should surface the right information to the right user at the right time.

---

# Role Philosophy

## Principle 1
### Same Platform, Different Perspective

All users operate on the same underlying platform.

Role experiences affect:

```text id="hjlwm1"
Layout

Widget Priority

Navigation Priority

Default Views

Dashboard Composition
```

Role experiences should not unnecessarily fragment functionality.

---

## Principle 2
### Emphasize What Matters

Users should immediately see what they care about most.

Not everything the system can do.

---

## Principle 3
### Minimize Cognitive Load

Do not expose operational complexity unless the role requires it.

---

## Principle 4
### Roles Are Operational

Roles should reflect actual institutional responsibilities.

Not software permissions alone.

---

# Experience Categories

The Cockpit supports the following operational experiences:

```text id="jlwm2"
Branch

Operations

Treasury

Compliance

Executive

Administrator

Developer
```

These are experience profiles.

Not security roles.

A user may have:

```text id="jlwm3"
Permissions

+
Workspace Profile
```

independently.

---

# Branch Experience

## Purpose

Enable rapid issuance and servicing.

---

## Typical Users

```text id="jlwm4"
Remittance Staff

Money Changers

Branch Tellers

Cashiers

Frontline Operators
```

---

## Primary Goal

Issue value quickly.

---

## Dashboard Priority

```text id="jlwm5"
Quick Generate

Available Balance

Recent Transactions

Pending Customer Actions

Expiring Pay Codes
```

---

## Hidden By Default

```text id="jlwm6"
Reconciliation

Execution Monitor

Settlement Operations

Audit Center
```

---

## Navigation Priority

```text id="jlwm7"
Dashboard

Quick Generate

Pay Codes

Contacts

Funding
```

---

## Favorite Widgets

```text id="jlwm8"
Quick Generate Widget

Recent Activity Widget

Balance Widget

Recipient Widget

Expiry Reminder Widget
```

---

## Success Metric

```text id="jlwm9"
Time To Issue
```

---

# Operations Experience

## Purpose

Monitor and manage ongoing activity.

---

## Typical Users

```text id="jlwm10"
Operations Officers

Settlement Staff

Batch Managers

Program Coordinators
```

---

## Primary Goal

Keep value moving.

---

## Dashboard Priority

```text id="jlwm11"
Redemption Pipeline

Batch Command Center

Distribution Status

Risk Radar

Recent Activity
```

---

## Navigation Priority

```text id="jlwm12"
Dashboard

Pay Codes

Operations

Templates

Reports
```

---

## Favorite Widgets

```text id="jlwm13"
Batch Pipeline Widget

Distribution Analytics Widget

Execution Monitor Widget

Expiry Risk Widget
```

---

## Success Metric

```text id="jlwm14"
Operational Throughput
```

---

# Treasury Experience

## Purpose

Manage liquidity and financial movement.

---

## Typical Users

```text id="jlwm15"
Treasury Officers

Finance Managers

Liquidity Managers

Settlement Managers
```

---

## Primary Goal

Maintain funding and liquidity.

---

## Dashboard Priority

```text id="jlwm16"
Liquidity Hero

Funding Status

Funding Runway

Expected Redemptions

Settlement Exposure
```

---

## Navigation Priority

```text id="jlwm17"
Dashboard

Funding

Pay Codes

Operations

Reports
```

---

## Favorite Widgets

```text id="jlwm18"
Liquidity Hero Widget

Balance Trend Widget

Funding Source Widget

Reconciliation Widget
```

---

## Hidden By Default

```text id="jlwm19"
Claim Evidence

Distribution Branding

Template Design
```

---

## Success Metric

```text id="jlwm20"
Liquidity Availability
```

---

# Compliance Experience

## Purpose

Manage evidence, approvals, and auditability.

---

## Typical Users

```text id="jlwm21"
Compliance Officers

Risk Officers

Auditors

Reviewers
```

---

## Primary Goal

Ensure trust and accountability.

---

## Dashboard Priority

```text id="jlwm22"
Pending Evidence

Pending Approvals

Audit Events

Risk Radar

Exception Queue
```

---

## Navigation Priority

```text id="jlwm23"
Dashboard

Operations

Approvals

Pay Codes

Reports
```

---

## Favorite Widgets

```text id="jlwm24"
Evidence Status Widget

Audit Event Widget

Approval Queue Widget

Risk Radar Widget
```

---

## Preferred Voucher Detail Tabs

```text id="jlwm25"
Claim Evidence

Settlement Envelope

Audit Trail

Timeline
```

---

## Success Metric

```text id="jlwm26"
Compliance Readiness
```

---

# Executive Experience

## Purpose

Provide strategic visibility.

---

## Typical Users

```text id="jlwm27"
Executives

Directors

Department Heads

Program Owners
```

---

## Primary Goal

Understand performance.

---

## Dashboard Priority

```text id="jlwm28"
Liquidity

Value Moved

Risk Indicators

Insights

Forecasts
```

---

## Navigation Priority

```text id="jlwm29"
Dashboard

Reports

Pay Codes

Funding
```

---

## Favorite Widgets

```text id="jlwm30"
AI Insight Widget

Redemption Heat Map Widget

Balance Trend Widget

Executive Metrics Widget
```

---

## Hidden By Default

```text id="jlwm31"
Technical Operations

Execution Monitor

Provider Diagnostics
```

---

## Success Metric

```text id="jlwm32"
Institutional Performance
```

---

# Administrator Experience

## Purpose

Operate the platform.

---

## Typical Users

```text id="jlwm33"
Platform Administrators

Institution Administrators

Support Personnel
```

---

## Primary Goal

Maintain platform health.

---

## Dashboard Priority

```text id="jlwm34"
System Health

Connected Services

Pending Administration Tasks

Security Alerts
```

---

## Navigation Priority

```text id="jlwm35"
Administration

Operations

Approvals

Dashboard
```

---

## Favorite Widgets

```text id="jlwm36"
Provider Status Widget

Security Event Widget

System Activity Widget
```

---

## Success Metric

```text id="jlwm37"
System Availability
```

---

# Developer Experience

## Purpose

Support integrations and automation.

---

## Typical Users

```text id="jlwm38"
Developers

System Integrators

Partners

Technical Teams
```

---

## Primary Goal

Integrate with x-change.

---

## Dashboard Priority

```text id="jlwm39"
API Activity

Webhook Activity

Token Usage

Integration Status
```

---

## Navigation Priority

```text id="jlwm40"
API Access

Reports

Operations

Administration
```

---

## Favorite Widgets

```text id="jlwm41"
API Usage Widget

Webhook Status Widget

Developer Token Widget
```

---

## Success Metric

```text id="jlwm42"
Integration Reliability
```

---

# Institutional Experiences

In addition to role-based experiences, institutions may define operational profiles.

Examples:

```text id="jlwm43"
Bank

LGU

PhilHealth

Corporate

Cooperative

NGO
```

These affect:

```text id="jlwm44"
Terminology

Branding

Default Templates

Dashboard Defaults
```

but not core functionality.

---

# Individual Experience

## Purpose

Support personal users.

---

## Typical Users

```text id="jlwm45"
Individual Wallet Holders

Merchants

Freelancers

Consumers
```

---

## Dashboard Priority

```text id="jlwm46"
Balance

Recent Activity

Deposit Pay Code

Funding

Pay Codes
```

---

## Hidden By Default

```text id="jlwm47"
Treasury

Reconciliation

Batch Operations

Execution Monitor
```

---

# Workspace Switching

Users may switch workspace profiles.

---

## Example

A user with sufficient permissions may switch between:

```text id="jlwm48"
Operations

Treasury

Compliance
```

without changing accounts.

---

## Rules

Switching profiles changes:

```text id="jlwm49"
Dashboard Layout

Widget Priority

Navigation Emphasis

Default Landing Views
```

Switching profiles does not change:

```text id="jlwm50"
Permissions

Data Visibility

Security Controls
```

---

# Role-Specific Landing Pages

Default landing pages:

```text id="jlwm51"
Branch → Dashboard

Operations → Dashboard

Treasury → Dashboard

Compliance → Dashboard

Executive → Dashboard

Administrator → Dashboard

Developer → API Access Dashboard
```

The dashboard adapts to the role.

The landing page remains consistent.

---

# Role-Specific Alerts

Branch users receive:

```text id="jlwm52"
Customer Action Alerts

Balance Alerts

Expiry Alerts
```

Treasury users receive:

```text id="jlwm53"
Funding Alerts

Liquidity Alerts

Settlement Alerts
```

Compliance users receive:

```text id="jlwm54"
Evidence Alerts

Approval Alerts

Risk Alerts
```

Executives receive:

```text id="jlwm55"
Performance Alerts

Trend Alerts

Risk Alerts
```

---

# AI Behavior By Role

Ask x-change should adapt.

---

## Branch

Examples:

```text id="jlwm56"
Generate a ₱5,000 Pay Code.
```

---

## Treasury

Examples:

```text id="jlwm57"
How much liquidity do we have available?
```

---

## Compliance

Examples:

```text id="jlwm58"
Show claims missing medical certificates.
```

---

## Executive

Examples:

```text id="jlwm59"
What are the biggest operational risks this week?
```

---

# Role Experience Invariants

Every role experience must preserve:

```text id="jlwm60"
Money Visibility

Operational Awareness

Auditability

Searchability

Actionability
```

No role experience should hide:

```text id="jlwm61"
Financial Impact

Approval Requirements

Audit Trails

Critical Risks
```

when relevant to the user.

---

# Guiding Statement

The Cockpit should feel like it was designed specifically for the user sitting in front of it.

A teller should feel:

```text id="jlwm62"
Fast.
```

A treasury officer should feel:

```text id="jlwm63"
In control.
```

A compliance officer should feel:

```text id="jlwm64"
Confident.
```

An executive should feel:

```text id="jlwm65"
Informed.
```

The same platform should deliver all four experiences without becoming four different applications.
