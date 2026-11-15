<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PipedreamConnectRequest;
use App\Services\Pipedream\PipedreamConnectClient;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PipedreamConnectController extends Controller
{
    /**
     * Show the user's Pipedream Connect settings page.
     */
    public function edit(Request $request, PipedreamConnectClient $pipedream): Response
    {
        return Inertia::render('settings/PipedreamConnect', [
            'projectId' => config('pipedream.project_id'),
            'environment' => config('pipedream.environment'),
            'configured' => $pipedream->isConfigured(),
            'connectionStatus' => $request->query('connected'),
            'defaultApp' => 'gmail',
            'suggestedApps' => [
                [
                    'slug' => 'gmail',
                    'label' => 'Gmail',
                    'description' => 'Connect a mailbox for future notification and workflow tests.',
                ],
                [
                    'slug' => 'slack',
                    'label' => 'Slack',
                    'description' => 'Connect a workspace for future operator updates.',
                ],
                [
                    'slug' => 'google_sheets',
                    'label' => 'Google Sheets',
                    'description' => 'Connect spreadsheets for future export and reconciliation experiments.',
                ],
            ],
        ]);
    }

    /**
     * Redirect the authenticated user to Pipedream's hosted Connect Link.
     */
    public function connectLink(
        PipedreamConnectRequest $request,
        PipedreamConnectClient $pipedream,
    ): SymfonyResponse {
        $token = $this->connectToken($request, $pipedream);
        $connectLink = $pipedream->trustedConnectLink($token['connect_link_url']);

        if ($request->inertia()) {
            return Inertia::location($connectLink);
        }

        return redirect()->away($connectLink);
    }

    /**
     * @return array{token: string, expires_at: string, connect_link_url: string}
     */
    private function connectToken(
        PipedreamConnectRequest $request,
        PipedreamConnectClient $pipedream,
    ): array {
        return $pipedream->createConnectToken($request->user(), [
            'app' => $request->appSlug(),
            'success_redirect_uri' => route('pipedream.connect.edit', ['connected' => '1']),
            'error_redirect_uri' => route('pipedream.connect.edit', ['connected' => '0']),
        ]);
    }
}
