# x-talk Functional Specifications (Revised)
## Version 2.0
## Package Name: 3neti/x-talk
## Namespace: LBHurtado\XTalk

---

# 1. Executive Summary

x-talk is a channel-agnostic **Conversation-to-Contract Engine**.

Its purpose is to transform human conversations into durable, executable instruction contracts.

x-talk does not primarily execute workflows.

x-talk does not primarily execute financial transactions.

x-talk does not primarily function as a chatbot.

Instead, x-talk captures intent, compiles intent, completes missing requirements, and emits structured contracts that downstream systems may execute.

---

# 2. Core Philosophy

## Human Conversations are Temporary

Human conversations are:

- ambiguous
- incomplete
- contextual
- transient

Examples:

```text
Generate a Pay Code.
```

```text
Issue a subsidy voucher.
```

```text
Create a settlement code for Juan.
```

These messages represent intent but not execution-ready instructions.

---

## Instruction Contracts are Durable

Instruction contracts are:

- structured
- complete
- auditable
- executable
- replayable
- versionable

Instruction contracts become the canonical output of x-talk.

---

# 3. Architectural Principle

## Voucher Instructions are the Intent Contract

VoucherInstructionsData is the canonical representation of intent.

Voucher instructions already define:

- cash behavior
- settlement behavior
- required evidence
- required validations
- participant responsibilities
- authorization requirements
- redemption contracts
- lifecycle expectations

Therefore:

Voucher instructions are not merely configuration.

Voucher instructions are executable intent.

---

# 4. Architectural Positioning

Previous model:

```text
User
 ↓
Intent
 ↓
Workflow
 ↓
Execution
```

New model:

```text
User
 ↓
Conversation
 ↓
Intent Compilation
 ↓
Voucher Instructions
 ↓
x-action (optional)
 ↓
x-change
 ↓
Execution
```

---

# 5. Primary Responsibility

x-talk is an Intent Capture and Intent Compilation Layer.

Its responsibility is:

```text
Conversation
 ↓
Intent
 ↓
Instruction Contract
```

Its responsibility is NOT:

```text
Conversation
 ↓
Execution
```

Execution belongs elsewhere.

---

# 6. Package Scope

x-talk owns:

- conversations
- intent extraction
- slot collection
- instruction completion
- contract compilation
- contract validation
- conversation persistence

x-talk does not own:

- financial execution
- voucher redemption
- workflow orchestration
- wallet mutations
- disbursement

---

# 7. Core Architecture

```text
Channel Adapter
        ↓
Conversation Engine
        ↓
Intent Extraction
        ↓
Intent Compilation
        ↓
Instruction Completion
        ↓
Instruction Contract
        ↓
Consumer
```

Consumers may include:

- x-change
- x-action
- APIs
- CLI
- Agents
- Pipedream
- Zapier
- Batch Processing

---

# 8. Canonical Output

The canonical output of x-talk is:

```php
VoucherInstructionsData
```

or a directly compatible structure.

Everything else is an implementation detail.

---

# 9. Intent Compilation

## Definition

Intent Compilation is the process of transforming:

```text
Human Intent
```

into:

```php
VoucherInstructionsData
```

---

## Example

Input:

```text
Issue a ₱5,000 subsidy voucher requiring OTP.
```

Output:

```php
[
    'cash' => [
        'amount' => '5000.00',
    ],

    'inputs' => [
        'fields' => [
            'otp',
        ],
    ],

    'validation' => [
        'otp' => [
            'required' => true,
        ],
    ],
]
```

---

# 10. Intent Contract Model

Introduce:

```php
CompiledIntentData
```

Example:

```php
class CompiledIntentData
{
    public string $intent;

    public array $instructions;

    public array $missingRequirements;

    public float $confidence;

    public array $assumptions;

    public array $warnings;
}
```

---

# 11. Conversation Lifecycle

## Step 1

User expresses intent.

Example:

```text
Generate a Pay Code.
```

---

## Step 2

x-talk extracts:

- actors
- beneficiaries
- amount
- constraints
- evidence requirements
- validation requirements

---

## Step 3

x-talk builds draft instructions.

---

## Step 4

Missing requirements are detected.

Example:

```text
Amount is missing.
```

---

## Step 5

Conversation continues.

Example:

```text
What amount should be assigned?
```

---

## Step 6

Instructions become complete.

---

## Step 7

Normalized instructions are emitted.

---

# 12. Instruction-Centric Conversations

Conversations are no longer centered around command arguments.

Conversations are centered around instruction attributes.

---

## Traditional Model

```text
Missing command arguments
```

Example:

```text
GENERATE
```

Response:

```text
Enter amount.
```

---

## New Model

```text
Incomplete instruction contract
```

Example:

```text
Generate a Pay Code.
```

Response:

```text
What amount should be assigned?
```

After completion:

```text
Should OTP verification be required?
```

---

# 13. Instruction Completeness

Introduce:

```php
InstructionCompletenessAnalyzer
```

Responsibilities:

- identify missing instruction attributes
- identify incomplete validation rules
- identify unresolved assumptions
- generate completion prompts

---

# 14. Instruction Completion

Introduce:

```php
InstructionCompletionService
```

Responsibilities:

- collect missing information
- update draft instructions
- re-evaluate completeness

---

# 15. Intent Compiler

Introduce:

```php
IntentCompiler
```

Responsibilities:

- compile extracted intent
- produce instruction contracts
- normalize outputs

---

# 16. Instruction Builder

Introduce:

```php
InstructionBuilder
```

Responsibilities:

- create VoucherInstructionsData-compatible structures
- apply defaults
- apply profiles
- apply templates

---

# 17. AI Integration

AI is optional.

AI is not the source of truth.

AI may assist with:

- intent extraction
- entity extraction
- ambiguity resolution

AI never becomes the execution engine.

---

# 18. Deterministic Execution

Both paths must converge:

Without AI:

```text
Conversation
 ↓
Rules
 ↓
Instructions
```

With AI:

```text
Conversation
 ↓
LLM
 ↓
Instructions
```

Final output remains:

```php
VoucherInstructionsData
```

---

# 19. Channel Support

Supported channels:

- SMS
- Telegram
- WhatsApp
- Facebook Messenger
- Web Chat
- CLI
- HTTP
- API
- MCP
- AI Agents

All channels normalize into the same conversation model.

---

# 20. Conversation Persistence

Persist:

## Conversations

```text
xtalk_conversations
```

## Messages

```text
xtalk_messages
```

## Draft Contracts

```text
xtalk_draft_contracts
```

## Contract Versions

```text
xtalk_contract_versions
```

---

# 21. Contract Versioning

Contracts should be:

- saved
- resumed
- reviewed
- approved
- revised
- replayed

before execution.

This allows:

```text
Conversation
 ↓
Draft Contract
 ↓
Review
 ↓
Approval
 ↓
Execution
```

---

# 22. Relationship with x-action

x-action remains the orchestration layer.

x-talk does not orchestrate workflows.

Instead:

```text
x-talk
 ↓
Compiles Instructions
 ↓
x-action
 ↓
Executes Workflow
```

---

# 23. Relationship with x-change

x-change remains the execution layer.

Example:

```php
GeneratePayCode::run(
    instructions: $compiledIntent->instructions
);
```

x-talk never directly performs financial execution.

---

# 24. New Contracts

Introduce:

```php
IntentCompilerContract
```

```php
InstructionBuilderContract
```

```php
InstructionCompletionContract
```

```php
InstructionCompletenessAnalyzerContract
```

---

# 25. New Actions

Introduce:

```php
CompileIntent
```

```php
CompleteIntent
```

```php
BuildVoucherInstructions
```

```php
AnalyzeInstructionCompleteness
```

---

# 26. AI Provider Support

Supported providers:

- OpenAI
- Anthropic
- Gemini
- Ollama
- Custom Providers

All providers must return instruction-compatible outputs.

---

# 27. Auditing

Every contract compilation must be auditable.

Audit records include:

- original message
- extracted intent
- assumptions
- missing requirements
- generated instructions
- final instructions
- approval decisions

---

# 28. Design Principles

1. Conversations are temporary.
2. Contracts are durable.
3. Voucher Instructions are the Intent Contract.
4. Intent must compile into instructions.
5. AI is optional.
6. AI never executes.
7. Execution belongs outside x-talk.
8. Instruction contracts are the primary artifact.
9. Conversations exist to complete contracts.
10. Every compiled contract must be auditable.

---

# 29. One-Line Summary

x-talk is a Conversation-to-Contract Engine that transforms human intent into durable Voucher Instruction contracts that can be executed, orchestrated, audited, versioned, replayed, and consumed across x-change, x-action, APIs, mobile applications, AI agents, and automation platforms.
