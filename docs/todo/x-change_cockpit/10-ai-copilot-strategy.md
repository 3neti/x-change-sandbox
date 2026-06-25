# 10-ai-copilot-strategy.md

# x-change Cockpit
## AI Copilot Strategy

### Version
Draft v1

### Purpose

This document defines the AI Copilot strategy for the x-change Cockpit.

The purpose of the AI Copilot is to make the platform:

- Easier to operate
- Easier to search
- Easier to understand
- Easier to monitor
- Easier to learn

without reducing:

- Auditability
- Transparency
- Governance
- Financial controls

The AI Copilot exists to augment operators.

It does not exist to replace them.

---

# Vision

The AI Copilot should eventually become:

```text id="aic001"
Operations Assistant

Treasury Assistant

Compliance Assistant

Search Assistant

Knowledge Assistant
```

for every x-change deployment.

---

# Guiding Principle

## AI Proposes

## Humans Approve

The Copilot may:

```text id="aic002"
Search

Recommend

Summarize

Draft

Explain

Prepare
```

The Copilot must not:

```text id="aic003"
Move money autonomously

Approve autonomously

Override controls

Modify records silently
```

---

# Strategic Goals

The Copilot should help users answer:

```text id="aic004"
What happened?

Why did it happen?

What should I do next?

What am I missing?

What is at risk?
```

without requiring deep platform knowledge.

---

# Copilot Maturity Model

The Copilot evolves in phases.

---

# Phase 1
## Natural Language Search

Primary objective:

Replace complex search interfaces.

---

### Example Queries

```text id="aic005"
Show vouchers redeemed in Quezon City last year.

Show vouchers redeemed by Juan Dela Cruz above 2,000.

Show vouchers that will expire this week.

Show unshared vouchers.

Show pending evidence requests.
```

---

### Output

```text id="aic006"
Filtered Results

Explorer Views

Saved Views

Reports
```

---

# Phase 2
## Operational Intelligence

Primary objective:

Explain platform activity.

---

### Example Queries

```text id="aic007"
Why did this batch fail?

Why are redemptions lower this week?

Which vouchers are at risk?

Which claims are waiting for evidence?
```

---

### Output

```text id="aic008"
Analysis

Recommendations

Linked Records

Suggested Actions
```

---

# Phase 3
## Guided Operations

Primary objective:

Prepare actions.

---

### Example Commands

```text id="aic009"
Generate a ₱5,000 Pay Code.

Create a remittance batch.

Prepare a PhilHealth settlement envelope.

Generate vouchers from this contact group.
```

---

### Output

The Copilot prepares:

```text id="aic010"
Draft

Review Screen

Approval Request
```

The user confirms.

The system executes.

---

# Phase 4
## Institutional Intelligence

Primary objective:

Provide strategic insights.

---

### Example Questions

```text id="aic011"
What are our biggest operational risks?

Which programs are underperforming?

What is our expected redemption exposure next month?

Which beneficiaries are repeatedly failing KYC?
```

---

### Output

```text id="aic012"
Insights

Forecasts

Recommendations

Supporting Evidence
```

---

# Copilot Personas

The Copilot adapts to the user.

---

# Branch Copilot

Focus:

```text id="aic013"
Generation

Recipients

Balances

Recent Activity
```

---

### Example

```text id="aic014"
Generate a ₱5,600 Money Changer Pay Code.
```

---

# Operations Copilot

Focus:

```text id="aic015"
Batches

Distribution

Execution

Exceptions
```

---

### Example

```text id="aic016"
Show failed distributions from yesterday.
```

---

# Treasury Copilot

Focus:

```text id="aic017"
Liquidity

Funding

Settlement

Exposure
```

---

### Example

```text id="aic018"
How much liquidity is available today?
```

---

# Compliance Copilot

Focus:

```text id="aic019"
Evidence

Approvals

Audit

Risk
```

---

### Example

```text id="aic020"
Show claims missing medical certificates.
```

---

# Executive Copilot

Focus:

```text id="aic021"
Performance

Risk

Forecasts

Insights
```

---

### Example

```text id="aic022"
What should I be concerned about this week?
```

---

# Copilot Entry Points

The Copilot should be globally available.

---

## Header

Persistent entry point.

```text id="aic023"
Ask x-change
```

---

## Command Palette

Keyboard shortcut.

```text id="aic024"
⌘K

Ctrl+K
```

---

## Dashboard

Insight-focused entry point.

---

## Explorer

Search-focused entry point.

---

# Ask x-change

Ask x-change is not a chatbot page.

It is:

```text id="aic025"
Search Layer

Operations Layer

Intelligence Layer
```

---

# User Experience

The experience should feel like:

```text id="aic026"
Bloomberg Assistant

Treasury Analyst

Operations Consultant
```

not:

```text id="aic027"
General Chat Room

Social Messaging App

Consumer AI Bot
```

---

# Copilot Capabilities

---

# Capability 1
## Search

Convert natural language into filters.

---

### Example

Input:

```text id="aic028"
Redeemed in Quezon City last year between 5,000 and 6,000.
```

Output:

```text id="aic029"
Explorer Results

Applied Filters

Save Search Option
```

---

# Capability 2
## Explain

Explain system state.

---

### Example

```text id="aic030"
Why is this voucher still pending?
```

---

### Output

```text id="aic031"
Timeline

Evidence Status

Approvals

Blocking Conditions
```

---

# Capability 3
## Summarize

Condense large datasets.

---

### Example

```text id="aic032"
Summarize this batch.
```

---

### Output

```text id="aic033"
Totals

Failures

Risks

Recommendations
```

---

# Capability 4
## Recommend

Suggest actions.

---

### Example

```text id="aic034"
What should I do about these expiring vouchers?
```

---

### Output

```text id="aic035"
Recommended Actions

Affected Records

Expected Impact
```

---

# Capability 5
## Prepare

Create drafts.

---

### Examples

```text id="aic036"
Generate a voucher.

Create a batch.

Prepare a settlement envelope.

Draft a report.
```

---

### Rule

Preparation is allowed.

Execution requires approval.

---

# Capability 6
## Learn

Help users understand the platform.

---

### Example

```text id="aic037"
What is a settlement envelope?
```

---

### Output

```text id="aic038"
Documentation

Examples

Best Practices
```

---

# Voice Strategy

Voice is a future capability.

---

# Phase 1

Microphone support inside Ask x-change.

---

### Example

```text id="aic039"
Generate a ₱1,000 Pay Code for Juan Dela Cruz.
```

---

# Phase 2

Mobile voice workflows.

---

### Example

```text id="aic040"
Show my pending approvals.
```

---

# Phase 3

Platform integrations.

---

### Examples

```text id="aic041"
Siri

Google Assistant

Alexa
```

---

# Siri Strategy

Future capability.

---

### Examples

```text id="aic042"
Siri, generate a ₱1,000 remittance Pay Code.

Siri, check my available balance.

Siri, show pending approvals.
```

---

### Rule

Siri may prepare.

Siri may not execute.

---

# Copilot and Search

Search becomes AI-first.

Traditional filters remain available.

---

## User Preference

Users may choose:

```text id="aic043"
Search Builder

Natural Language

Both
```

---

# Copilot and Reports

The Copilot should eventually generate reports.

---

### Example

```text id="aic044"
Create an executive report for the last 30 days.
```

---

### Output

```text id="aic045"
Charts

Summary

Insights

Export
```

---

# Copilot and Settlement

The Copilot should understand:

```text id="aic046"
Settlement Envelopes

Evidence Requirements

Approvals

Readiness
```

---

### Example

```text id="aic047"
Why is this envelope not ready?
```

---

### Output

```text id="aic048"
Missing Evidence

Missing Approval

Blocking Rules
```

---

# Copilot and Distribution

The Copilot should understand:

```text id="aic049"
Delivery

Open Rates

Claim Rates

Expiry Risk
```

---

### Example

```text id="aic050"
Which vouchers should be resent?
```

---

# Copilot and Templates

The Copilot should eventually help author templates.

---

### Example

```text id="aic051"
Create a template for medical reimbursements.
```

---

### Output

```text id="aic052"
Draft Template

Suggested Inputs

Suggested Validation

Suggested Distribution
```

---

# AI Governance

All AI actions must be:

```text id="aic053"
Logged

Auditable

Traceable
```

---

## Record

```text id="aic054"
Prompt

Response

User

Timestamp

Result
```

where appropriate.

---

# AI Safety Rules

The Copilot must never:

```text id="aic055"
Issue money silently

Approve money silently

Modify balances

Change audit records

Delete evidence
```

---

# AI Transparency

The user should always know:

```text id="aic056"
What the AI did

Why it did it

What data it used
```

---

# Success Metrics

The Copilot is successful if it reduces:

```text id="aic057"
Time To Find

Time To Understand

Time To Generate

Time To Investigate
```

while preserving:

```text id="aic058"
Control

Trust

Auditability
```

---

# Long-Term Vision

The ultimate goal is not to build a chatbot.

The ultimate goal is to build:

```text id="aic059"
The Institutional Intelligence Layer
```

for x-change.

Users should eventually feel that they are interacting with:

```text id="aic060"
An Operations Analyst

A Treasury Assistant

A Compliance Reviewer

A Search Expert
```

that understands the institution's Pay Codes, balances, settlement envelopes, evidence, distribution activity, and operational state.

---

# Guiding Statement

The AI Copilot exists to reduce complexity, not authority.

It should make users faster.

It should make users smarter.

It should never take control away from them.

AI proposes.

Humans approve.
