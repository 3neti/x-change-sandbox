# 3neti/x-action Functional Specifications

**Package:** `3neti/x-action`  
**Namespace:** `LBHurtado\XAction`  
**Status:** Draft for Architecture / Scaffolding  
**Primary Host:** `3neti/x-change`  
**Package Type:** Workflow Action / CTA Infrastructure Package

---

# 1. Purpose

`3neti/x-action` is a reusable Laravel package for modeling, resolving, rendering, routing, tracking, and extending workflow actions across the x-change ecosystem.

The package exists to make CTAs intentional, measurable, extensible, and workflow-aware.

This package should not be understood as a “button package.”

It is a workflow continuation package.

Its core question is:

> What can this actor do next, in this workflow state, on this surface, under this feature profile?

---

# 2. Core Philosophy

## 2.1 Notifications inform. Workflows continue.

Notifications, emails, SMS, in-app alerts, and campaign messages may render CTAs, but they should not decide which CTAs exist.

The action must be resolved by the workflow domain.

## 2.2 Workflow actions are not just URLs

A URL is only the final transport target.

A workflow action includes:

- action identity
- actor
- subject
- workflow state
- target
- eligibility
- feature profile
- campaign attribution
- surface
- channel
- analytics
- optional connector invocation
- optional asynchronous completion

## 2.3 Package owns the grammar, host apps own vocabulary

`x-action` owns:

- contracts
- DTOs
- registry
- resolver
- target routing
- action runs
- analytics
- connectors
- generic UI components

Host applications own:

- domain action classes
- workflow state
- claim/disbursement/campaign/settlement semantics
- actual business execution

Example:

```text
x-action owns:
    ActionData
    ActionTargetData
    WorkflowActionContract
    ActionRun
    ActionConnectorContract

x-change owns:
    claim.confirm
    claim.open_rider
    disbursement.retry
    settlement.upload_document

x-campaign owns:
    campaign.view
    campaign.claim_benefit
    campaign.complete_survey
```

---

# 3. Package Goals

`3neti/x-action` must provide:

1. A standard model for workflow actions.
2. A registry for resolving available actions.
3. DTOs for serializing actions to UI, feedback, campaign, cockpit, and future Copilot surfaces.
4. Optional route/token infrastructure for trackable redirects.
5. Optional action analytics.
6. Optional action playbooks for admin-configurable CTA behavior.
7. Connector abstraction for external automation such as webhooks, Pipedream, n8n, Zapier, MCP, or AI agents.
8. Generic reusable UI components for rendering actions.
9. Clean integration with x-change claim compiler and execution engine without disturbing existing clockwork flows.

---

# 4. Non-Goals

The package must not:

1. Own claim logic.
2. Own disbursement logic.
3. Own settlement execution.
4. Own campaign business rules.
5. Replace the claim compiler.
6. Replace form-flow.
7. Replace x-feedback.
8. Replace x-journal.
9. Decide financial authority.
10. Allow external connectors to directly mutate money/compliance state.

---

# 5. Canonical Concepts

## 5.1 Workflow Action

A workflow action is an intentional next step available to an actor.

Examples:

```text
claim.confirm
claim.open_rider
claim.upload_document
claim.approve_otp
disbursement.retry
beneficiary.contact
audit.view
campaign.view
campaign.claim_benefit
campaign.resend_delivery
settlement.upload_document
cockpit.open_dashboard
```

## 5.2 CTA

CTA is the user-facing rendering of a workflow action.

Examples:

- Button
- Link
- Email HTML button
- SMS short URL
- Slack link
- Cockpit menu item
- AI Copilot suggested action

## 5.3 Action Target

An action target tells the system where or how an action proceeds.

Target types:

```text
route
signed_route
external_url
api
mobile_deep_link
action_router
connector
```

## 5.4 Action Run

An action run is a runtime attempt to invoke or follow an action.

It is especially important for asynchronous connectors.

Action run statuses:

```text
created
rendered
clicked
pending
completed
failed
expired
cancelled
```

## 5.5 Connector

A connector is an external execution or automation adapter.

Examples:

```text
webhook
pipedream
n8n
zapier
slack
sms
email
mcp
ai_agent
human_review
```

---

# 6. Namespace and Package Structure

Namespace:

```php
LBHurtado\XAction
```

Recommended structure:

```text
src/
    Actions/
    Contracts/
    Data/
    Enums/
    Events/
    Exceptions/
    Facades/
    Http/
        Controllers/
        Middleware/
        Requests/
        Resources/
    Models/
    Registries/
    Resolvers/
    Connectors/
    Routing/
    Analytics/
    UI/
    Support/
    Traits/
    XActionServiceProvider.php

config/
    x-action.php

database/
    migrations/

resources/
    js/
        components/
            ActionButton.vue
            ActionList.vue
            ActionMenu.vue
            ActionRenderer.vue
            ActionCard.vue
    views/

routes/
    web.php
    api.php

tests/
```

---

# 7. Core DTOs

## 7.1 ActionData

```php
namespace LBHurtado\XAction\Data;

final class ActionData
{
    public function __construct(
        public string $key,
        public string $label,
        public ActionTargetData $target,
        public ?string $intent = null,
        public ?string $description = null,
        public ?string $icon = null,
        public ?string $style = null,
        public ?string $audience = null,
        public ?string $surface = null,
        public ?string $channel = null,
        public ?string $expires_at = null,
        public array $permissions = [],
        public array $conditions = [],
        public array $analytics = [],
        public array $meta = [],
    ) {}
}
```

## 7.2 ActionTargetData

```php
namespace LBHurtado\XAction\Data;

final class ActionTargetData
{
    public function __construct(
        public string $type,
        public ?string $route = null,
        public array $parameters = [],
        public ?string $url = null,
        public ?string $method = 'GET',
        public ?string $surface = null,
        public ?string $connector = null,
        public ?string $operation = null,
        public array $payload = [],
        public array $meta = [],
    ) {}
}
```

## 7.3 ActionSubjectData

```php
namespace LBHurtado\XAction\Data;

final class ActionSubjectData
{
    public function __construct(
        public string $type,
        public string|int|null $id = null,
        public array $attributes = [],
        public array $state = [],
        public array $meta = [],
    ) {}

    public function get(string $key, mixed $default = null): mixed;
}
```

## 7.4 ActionContextData

```php
namespace LBHurtado\XAction\Data;

final class ActionContextData
{
    public function __construct(
        public ?string $actor_type = null,
        public string|int|null $actor_id = null,
        public string $feature_profile = 'default',
        public ?string $surface = null,
        public ?string $channel = null,
        public ?string $campaign_id = null,
        public ?string $campaign_run_id = null,
        public array $capabilities = [],
        public array $meta = [],
    ) {}
}
```

## 7.5 ActionRunData

```php
namespace LBHurtado\XAction\Data;

final class ActionRunData
{
    public function __construct(
        public string $id,
        public string $action_key,
        public string $status,
        public ActionSubjectData $subject,
        public ActionContextData $context,
        public ActionTargetData $target,
        public ?string $connector = null,
        public ?string $correlation_id = null,
        public ?string $callback_url = null,
        public array $payload = [],
        public array $result = [],
        public array $meta = [],
    ) {}
}
```

## 7.6 ActionInvocationResultData

```php
namespace LBHurtado\XAction\Data;

final class ActionInvocationResultData
{
    public function __construct(
        public string $status,
        public ?string $external_reference = null,
        public ?string $message = null,
        public array $result = [],
        public array $meta = [],
    ) {}
}
```

## 7.7 ActionCallbackData

```php
namespace LBHurtado\XAction\Data;

final class ActionCallbackData
{
    public function __construct(
        public string $correlation_id,
        public string $status,
        public ?string $external_reference = null,
        public array $result = [],
        public array $meta = [],
    ) {}
}
```

---

# 8. Core Contracts

## 8.1 WorkflowActionContract

```php
namespace LBHurtado\XAction\Contracts;

use LBHurtado\XAction\Data\ActionData;
use LBHurtado\XAction\Data\ActionSubjectData;
use LBHurtado\XAction\Data\ActionContextData;

interface WorkflowActionContract
{
    public function key(): string;

    public function supports(ActionSubjectData $subject, ActionContextData $context): bool;

    public function toActionData(ActionSubjectData $subject, ActionContextData $context): ActionData;
}
```

## 8.2 ActionRegistryContract

```php
namespace LBHurtado\XAction\Contracts;

interface ActionRegistryContract
{
    public function register(string $eventOrState, string|WorkflowActionContract $action): void;

    public function actionsFor(string $eventOrState): array;
}
```

## 8.3 ActionResolverContract

```php
namespace LBHurtado\XAction\Contracts;

use LBHurtado\XAction\Data\ActionSubjectData;
use LBHurtado\XAction\Data\ActionContextData;

interface ActionResolverContract
{
    public function resolve(
        string $eventOrState,
        ActionSubjectData $subject,
        ActionContextData $context
    ): array;
}
```

## 8.4 ActionRecorderContract

```php
namespace LBHurtado\XAction\Contracts;

use LBHurtado\XAction\Data\ActionData;
use LBHurtado\XAction\Data\ActionSubjectData;
use LBHurtado\XAction\Data\ActionContextData;

interface ActionRecorderContract
{
    public function rendered(ActionData $action, ActionSubjectData $subject, ActionContextData $context): void;

    public function clicked(string $actionKey, ActionSubjectData $subject, ActionContextData $context): void;

    public function completed(string $actionKey, ActionSubjectData $subject, ActionContextData $context, array $result = []): void;

    public function failed(string $actionKey, ActionSubjectData $subject, ActionContextData $context, array $error = []): void;
}
```

## 8.5 ActionConnectorContract

```php
namespace LBHurtado\XAction\Contracts;

use LBHurtado\XAction\Data\ActionRunData;
use LBHurtado\XAction\Data\ActionTargetData;
use LBHurtado\XAction\Data\ActionCallbackData;
use LBHurtado\XAction\Data\ActionInvocationResultData;

interface ActionConnectorContract
{
    public function key(): string;

    public function supports(ActionTargetData $target): bool;

    public function invoke(ActionRunData $run): ActionInvocationResultData;

    public function handleCallback(ActionCallbackData $callback): ActionInvocationResultData;
}
```

## 8.6 ActionTargetDriverContract

```php
namespace LBHurtado\XAction\Contracts;

use LBHurtado\XAction\Data\ActionData;
use LBHurtado\XAction\Data\ActionTargetData;

interface ActionTargetDriverContract
{
    public function supports(ActionTargetData $target): bool;

    public function resolve(ActionData $action): mixed;
}
```

---

# 9. Traits

## 9.1 HasWorkflowActionDefaults

Provides default icon, style, audience, expiration, and metadata behavior.

## 9.2 AsWorkflowAction

Allows concrete action classes to easily implement `WorkflowActionContract`.

## 9.3 ResolvesActionTargets

Helper methods for common targets:

```php
routeTarget()
signedRouteTarget()
externalUrlTarget()
connectorTarget()
actionRouterTarget()
```

## 9.4 RecordsActionEvents

Reusable helper for recording action lifecycle events.

---

# 10. Relationship With lorisleiva/laravel-actions

The package should be compatible with `lorisleiva/laravel-actions`, but must not require every workflow action to inherit from it.

Recommended rule:

```text
WorkflowActionContract describes what can be done next.
Laravel Action performs a specific task.
```

A concrete class may use both:

```php
use Lorisleiva\Actions\Concerns\AsAction;
use LBHurtado\XAction\Traits\AsWorkflowAction;

final class RetryDisbursement
{
    use AsAction;
    use AsWorkflowAction;
}
```

But the package core should not depend on Laravel Actions as the primary abstraction.

---

# 11. Configuration

`config/x-action.php`

```php
return [
    'routes' => [
        'enabled' => true,
        'prefix' => 'actions',
        'middleware' => ['web'],
        'api_middleware' => ['api'],
    ],

    'analytics' => [
        'enabled' => true,
        'recorder' => LBHurtado\XAction\Analytics\DatabaseActionRecorder::class,
    ],

    'registry' => [
        'actions' => [
            // 'claim.succeeded' => [
            //     App\WorkflowActions\OpenRiderUrlAction::class,
            // ],
        ],
    ],

    'playbooks' => [
        'enabled' => true,
    ],

    'connectors' => [
        'default' => 'webhook',

        'drivers' => [
            'webhook' => LBHurtado\XAction\Connectors\WebhookConnector::class,
            'pipedream' => LBHurtado\XAction\Connectors\PipedreamConnector::class,
            'null' => LBHurtado\XAction\Connectors\NullConnector::class,
        ],
    ],

    'tokens' => [
        'enabled' => true,
        'ttl_minutes' => 60 * 24,
    ],

    'ui' => [
        'publish_components' => true,
    ],
];
```

---

# 12. Database Migrations

Migrations should be publishable and optional.

The package needs migrations only for runtime state, analytics, action runs, tokens, and optional playbooks.

## 12.1 `workflow_action_events`

Purpose: store action analytics/history.

Columns:

```text
id
action_key
event_type
subject_type nullable
subject_id nullable
actor_type nullable
actor_id nullable
campaign_id nullable
campaign_run_id nullable
channel nullable
surface nullable
target_type nullable
metadata json nullable
occurred_at
timestamps
```

Event types:

```text
rendered
viewed
clicked
invoked
pending
completed
failed
expired
cancelled
```

## 12.2 `workflow_action_tokens`

Purpose: support trackable redirect/action router links.

Columns:

```text
id
token unique
action_key
subject_type nullable
subject_id nullable
actor_type nullable
actor_id nullable
target json
context json nullable
expires_at nullable
consumed_at nullable
metadata json nullable
timestamps
```

## 12.3 `workflow_action_runs`

Purpose: track async connector execution.

Columns:

```text
id
action_key
status
subject_type nullable
subject_id nullable
actor_type nullable
actor_id nullable
connector nullable
correlation_id unique nullable
callback_url nullable
external_reference nullable
target json
payload json nullable
result json nullable
metadata json nullable
started_at nullable
completed_at nullable
failed_at nullable
expires_at nullable
timestamps
```

## 12.4 `workflow_action_playbooks`

Purpose: optional admin-configured action policies.

Columns:

```text
id
name
event_key
feature_profile default 'default'
audience nullable
surface nullable
channel nullable
enabled boolean
actions json
conditions json nullable
metadata json nullable
timestamps
```

---

# 13. Models

## 13.1 WorkflowActionEvent

Model for analytics records.

Namespace:

```php
LBHurtado\XAction\Models\WorkflowActionEvent
```

## 13.2 WorkflowActionToken

Model for trackable action router links.

Namespace:

```php
LBHurtado\XAction\Models\WorkflowActionToken
```

## 13.3 WorkflowActionRun

Model for connector and async execution.

Namespace:

```php
LBHurtado\XAction\Models\WorkflowActionRun
```

## 13.4 WorkflowActionPlaybook

Model for optional admin-configurable action availability.

Namespace:

```php
LBHurtado\XAction\Models\WorkflowActionPlaybook
```

---

# 14. Endpoints

The package may own infrastructure-level endpoints only.

## 14.1 Action Redirect

```text
GET /actions/{token}
```

Responsibilities:

1. Resolve token.
2. Record clicked event.
3. Validate expiration.
4. Resolve target.
5. Redirect or invoke connector.
6. Mark run as pending/completed/failed where applicable.

Controller:

```php
LBHurtado\XAction\Http\Controllers\ActionRedirectController
```

## 14.2 Action Event

```text
POST /actions/events
```

Purpose: frontend/client records rendered/viewed/clicked events.

Controller:

```php
LBHurtado\XAction\Http\Controllers\ActionEventController
```

## 14.3 Action Resolve

```text
GET /actions/resolve
POST /actions/resolve
```

Purpose: return available actions for a subject/context.

This endpoint is optional and should be protected by middleware.

Controller:

```php
LBHurtado\XAction\Http\Controllers\ActionResolveController
```

## 14.4 Connector Callback

```text
POST /actions/connectors/{connector}/callbacks/{correlation_id}
```

Purpose: receive async completion/failure from external connectors.

Controller:

```php
LBHurtado\XAction\Http\Controllers\ActionConnectorCallbackController
```

---

# 15. Resources and Responses

## 15.1 ActionResource

Serializes `ActionData`.

```php
LBHurtado\XAction\Http\Resources\ActionResource
```

Output shape:

```json
{
  "key": "claim.open_rider",
  "label": "Continue",
  "intent": "continue",
  "description": null,
  "icon": null,
  "style": "primary",
  "audience": "claimant",
  "surface": "claim_success",
  "channel": "in_app",
  "target": {
    "type": "external_url",
    "url": "https://example.com",
    "method": "GET"
  },
  "analytics": {},
  "meta": {}
}
```

## 15.2 ActionRunResource

Serializes action run state.

## 15.3 ActionEventResource

Serializes action event state.

## 15.4 Standard Response

```json
{
  "success": true,
  "data": {},
  "meta": {}
}
```

Errors:

```json
{
  "success": false,
  "message": "Action token has expired.",
  "code": "ACTION_TOKEN_EXPIRED",
  "errors": {}
}
```

---

# 16. UI Components

The package may ship generic reusable Vue components.

It must not ship domain-specific pages for claims, campaigns, disbursements, or settlements.

## 16.1 Components

```text
ActionButton.vue
ActionList.vue
ActionMenu.vue
ActionRenderer.vue
ActionCard.vue
ActionTimeline.vue
```

## 16.2 Component Rules

Components should accept serialized `ActionData`.

Example:

```vue
<ActionButton :action="action" />
<ActionList :actions="actions" />
```

They should not know domain semantics like:

```text
RetryDisbursementButton
ClaimSuccessRiderButton
DBPReviewBeneficiaryButton
```

Those belong to host apps.

## 16.3 Optional Admin UI

The package may later provide optional generic admin pages:

```text
Actions/Index.vue
Actions/Playbooks.vue
Actions/Analytics.vue
```

These pages should be optional/publishable and not required for package usage.

---

# 17. Action Playbooks

Action Playbooks make CTAs intentional and administrable.

They answer:

> For this workflow event, feature profile, audience, channel, and surface, which approved actions should appear?

## 17.1 Developer vs Admin Responsibility

Developers define safe action classes.

Admins configure:

- enabled actions
- labels
- priority
- audience
- channel
- surface
- feature profile overrides

Admins must not define arbitrary executable code.

## 17.2 Example

```text
Event: disbursement.failed
Profile: lgu
Audience: operator
Surface: cockpit

Enabled Actions:
    - disbursement.retry
    - beneficiary.contact
    - audit.view
```

## 17.3 Fallback Rules

1. Use exact feature profile.
2. Fall back to default.
3. Filter by actor permissions/capabilities.
4. Filter by workflow state.
5. Return no action if unsupported.
6. Never invent actions in notification rendering.

---

# 18. Connector Architecture

Connectors provide the extension seam for external automation and future technologies.

## 18.1 Connector Use Cases

Connectors may:

- call a webhook
- trigger Pipedream
- trigger n8n
- trigger Zapier
- post to Slack
- send a support request
- call an MCP tool
- delegate to an AI agent
- initiate human review

## 18.2 Connector Flow

```text
User clicks CTA
    ↓
x-action records clicked
    ↓
x-action creates WorkflowActionRun
    ↓
connector invoked
    ↓
run status = pending
    ↓
connector performs external automation
    ↓
connector calls callback URL
    ↓
x-action verifies callback
    ↓
run status = completed / failed
    ↓
event emitted to host app
```

## 18.3 Pipedream Example

Payload to Pipedream:

```json
{
  "correlation_id": "actrun_123",
  "action_key": "beneficiary.contact",
  "subject": {
    "type": "voucher",
    "id": "123"
  },
  "callback_url": "https://x-change.test/actions/connectors/pipedream/callbacks/actrun_123",
  "callback_token": "signed-secret",
  "data": {}
}
```

Callback from Pipedream:

```json
{
  "correlation_id": "actrun_123",
  "status": "completed",
  "external_reference": "pd_abc",
  "result": {
    "message": "Beneficiary notified"
  }
}
```

## 18.4 Agentic AI Connector

The same connector model can support agentic AI.

Allowed:

```text
suggest
draft
classify
summarize
prepare
notify
recommend
invoke approved endpoint
```

Not allowed:

```text
directly approve claim
directly retry disbursement
directly move money
directly change voucher state
directly bypass authorization
```

Rule:

> Agent may suggest or prepare. x-change remains the authority for money and compliance state.

---

# 19. Integration With x-change

`x-action` should integrate with x-change as a sidecar layer.

It should not be inserted inside critical claim compiler logic at first.

## 19.1 Claim Compiler Integration

Initial strategy:

```text
Claim Compiler
    ↓ existing compiled payload
CTA Decorator
    ↓ same payload + actions[]
UI renders actions if present
```

Example:

```php
$experience = $claimCompiler->compile($voucher, $context);

$experience = $ctaDecorator->decorate($experience, $voucher, $context);

return $experience;
```

## 19.2 Surgical Guardrails

For claim compiler integration:

1. No CTA logic inside form-flow step resolution.
2. No CTA changes to validation.
3. No CTA changes to redemption execution.
4. No CTA changes to YAML driver.
5. No CTA required for existing flow to work.
6. CTA failure must not break claim flow.
7. CTA integration must be append-only at first.

## 19.3 Claim Actions

Initial x-change action providers:

```text
claim.confirm
claim.open_rider
claim.upload_document
claim.view_status
claim.approve_otp
```

## 19.4 Rider URL

Current:

```text
success.rider.url
```

Future normalized action:

```php
ActionData(
    key: 'claim.open_rider',
    label: 'Continue',
    target: ActionTargetData(
        type: 'external_url',
        url: $riderUrl,
        surface: 'claim_success'
    )
)
```

The claim compiler still owns the existence of the rider URL.

`x-action` only wraps it as a workflow action for rendering, routing, and analytics.

## 19.5 Confirm Redemption

Current button remains as-is.

First integration:

```text
Add action_key="claim.confirm"
Record rendered/clicked/completed
Do not change submit route
Do not change form-flow behavior
```

---

# 20. Integration With Execution Engine

The execution engine should not render CTAs.

It should return workflow state and execution results.

`x-action` resolves CTAs from those results.

Example:

```text
SubmitPayCodeClaim result
    status: approval_required
    requirements: ['otp']
```

Resolved actions:

```text
claim.approve_otp
claim.view_status
```

Clean rule:

```text
Execution engine performs the business action.
x-action describes and tracks the continuation action.
```

---

# 21. Integration With x-feedback

`x-feedback` should receive resolved `ActionData[]`.

It should render channel-specific CTAs.

Examples:

```text
Email → HTML button
SMS → short URL
In-app → button
Slack → interactive link
```

`x-feedback` must not decide which actions exist.

---

# 22. Integration With x-campaign

`x-campaign` should use action analytics for attribution.

Campaign context should be carried in `ActionContextData`.

Fields:

```text
campaign_id
campaign_run_id
segment_id
batch_id
```

This allows campaign analytics such as:

```text
action rendered
action clicked
action completed
action failed
conversion by CTA
drop-off by CTA
campaign performance by action
```

---

# 23. Integration With x-journal

`x-journal` may listen to action events and record them as historical entries.

Examples:

```text
workflow_action.rendered
workflow_action.clicked
workflow_action.completed
workflow_action.failed
connector.callback.received
```

`x-action` records structured action telemetry.

`x-journal` records narrative/audit history.

---

# 24. Integration With Cockpit

Cockpit should use actions as operator workflow affordances.

Examples:

```text
Approve Request
Retry Disbursement
Review Beneficiary
View Audit Trail
Export Batch
Contact Beneficiary
```

Cockpit owns page placement and layout.

`x-action` owns action shape and tracking.

---

# 25. Lifecycle Scenario Runner Integration

The lifecycle scenario runner should treat CTAs as observable workflow artifacts.

## 25.1 Modes

```text
assert_only
follow_action
analytics_probe
```

## 25.2 Example

```text
Scenario: claim succeeds with rider URL

Given voucher has rider.url
When claimant submits claim
Then action claim.open_rider is available
And action target is external_url
And action surface is claim_success
And action is rendered
When runner follows claim.open_rider
Then action clicked is recorded
And redirect target is rider.url
```

## 25.3 Test Helpers

Potential helper methods:

```php
expectAction('claim.open_rider');
expectActionTarget('claim.open_rider', 'external_url');
followAction('claim.open_rider');
expectActionRendered('claim.open_rider');
expectActionClicked('claim.open_rider');
expectActionCompleted('claim.open_rider');
```

---

# 26. Analytics

## 26.1 Analytics Events

The package should track:

```text
rendered
viewed
clicked
invoked
pending
completed
failed
expired
cancelled
```

## 26.2 Analytics Dimensions

Each event may include:

```text
action_key
subject_type
subject_id
actor_type
actor_id
campaign_id
campaign_run_id
channel
surface
feature_profile
target_type
connector
metadata
occurred_at
```

## 26.3 Core Metrics

The package should support:

```text
render count
click count
completion count
failure count
click-through rate
completion rate
drop-off rate
average completion time
connector failure rate
stale action rate
```

---

# 27. Security

## 27.1 Tokenized Links

Action router links should use opaque tokens.

Never expose sensitive payloads in URLs.

## 27.2 Signed Routes

Internal routes may use Laravel signed URLs.

## 27.3 Expiration

Action tokens may expire.

Expired links should record `expired`.

## 27.4 Authorization

Before redirecting or invoking an action, the package must allow host apps to verify:

```text
actor
subject
permission
current workflow state
feature profile
```

## 27.5 Connector Security

Connector callbacks must be verified by:

```text
callback token
signature
shared secret
correlation ID
idempotency key
```

## 27.6 Financial Authority Rule

External connectors may not directly authorize money movement.

They may only call controlled host-app endpoints.

---

# 28. Events

Package events:

```php
WorkflowActionRendered
WorkflowActionViewed
WorkflowActionClicked
WorkflowActionInvoked
WorkflowActionPending
WorkflowActionCompleted
WorkflowActionFailed
WorkflowActionExpired
WorkflowActionCancelled
WorkflowActionConnectorCallbackReceived
```

Namespace:

```php
LBHurtado\XAction\Events
```

---

# 29. Exceptions

```php
ActionNotFoundException
ActionUnsupportedException
ActionUnauthorizedException
ActionTokenExpiredException
ActionTokenInvalidException
ActionConnectorNotFoundException
ActionConnectorInvocationFailedException
ActionCallbackVerificationFailedException
```

Namespace:

```php
LBHurtado\XAction\Exceptions
```

---

# 30. Testing Strategy

Use Pest.

## 30.1 Unit Tests

Test:

```text
ActionData serialization
ActionTargetData serialization
ActionSubjectData getter behavior
ActionContextData defaults
WorkflowActionContract implementations
ActionRegistry registration
ActionResolver filtering
ActionPlaybook fallback
Connector support detection
```

## 30.2 Feature Tests

Test:

```text
GET /actions/{token}
POST /actions/events
POST /actions/resolve
POST /actions/connectors/{connector}/callbacks/{correlation_id}
```

## 30.3 Integration Tests

Test with fake x-change subjects:

```text
claim.open_rider is resolved when rider.url exists
claim.confirm is rendered on completion state
disbursement.retry is rendered for failed disbursement
connector action creates pending run
connector callback completes run
```

## 30.4 Scenario Runner Tests

Test:

```text
expectAction
followAction
expectActionRendered
expectActionClicked
expectActionCompleted
```

## 30.5 Safety Tests

Test:

```text
expired tokens do not redirect
invalid callbacks fail
unauthorized actors cannot invoke protected actions
connector failure records failed status
x-action failure does not break claim compiler output
```

---

# 31. Recommended Initial Scaffold

## Phase 1 — Core Grammar

Deliver:

```text
ActionData
ActionTargetData
ActionSubjectData
ActionContextData
WorkflowActionContract
ActionRegistryContract
ActionResolverContract
basic resolver
config/x-action.php
```

## Phase 2 — Analytics

Deliver:

```text
workflow_action_events migration
WorkflowActionEvent model
ActionRecorderContract
DatabaseActionRecorder
events
test helpers
```

## Phase 3 — Router Tokens

Deliver:

```text
workflow_action_tokens migration
WorkflowActionToken model
ActionRedirectController
token generation service
target drivers
```

## Phase 4 — x-change Claim Integration

Deliver:

```text
CTA decorator pattern
claim.open_rider action
claim.confirm action
append-only actions[] to compiled payload
no claim compiler internal refactor
```

## Phase 5 — Connectors

Deliver:

```text
workflow_action_runs migration
WorkflowActionRun model
ActionConnectorContract
WebhookConnector
PipedreamConnector
callback controller
status lifecycle
```

## Phase 6 — Generic Vue Components

Deliver:

```text
ActionButton.vue
ActionList.vue
ActionRenderer.vue
ActionMenu.vue
```

## Phase 7 — Playbooks

Deliver:

```text
workflow_action_playbooks migration
WorkflowActionPlaybook model
playbook resolver
feature profile fallback
optional admin UI
```

---

# 32. Package Boundary Summary

## x-action owns

```text
workflow action grammar
action DTOs
action registry
action resolver
action targets
action tokens
action analytics
action runs
connectors
generic UI components
optional playbooks
```

## x-change owns

```text
claim actions
disbursement actions
settlement actions
execution engine
claim compiler
money movement
compliance decisions
```

## x-campaign owns

```text
campaign actions
campaign attribution
campaign performance dashboards
```

## x-feedback owns

```text
message delivery
channel rendering
email/SMS/webhook delivery
```

## x-journal owns

```text
history
audit narrative
event timeline
```

## Cockpit owns

```text
operator placement
dashboard layout
operational screens
```

---

# 33. Final Recommendation

`3neti/x-action` should be built as a reusable workflow action infrastructure package.

It should be:

```text
backend-first
DTO-driven
registry-resolved
analytics-aware
connector-extensible
UI-renderer-friendly
claim-compiler-safe
execution-engine-adjacent
```

It should not take over claim, campaign, settlement, or disbursement logic.

The first implementation target should be x-change claim success and claim completion actions:

```text
claim.open_rider
claim.confirm
```

These are safe because they can be added as append-only CTA decorations without disturbing the existing claim compiler and form-flow clockwork.

---

# 34. One-Line Summary

`3neti/x-action` is the reusable workflow-action layer that turns “what can the actor do next?” into a resolved, rendered, routed, tracked, and extensible action across claim, campaign, feedback, cockpit, settlement, and future AI-agent workflows.
