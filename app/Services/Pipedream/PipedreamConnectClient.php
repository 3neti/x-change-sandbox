<?php

namespace App\Services\Pipedream;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;

class PipedreamConnectClient
{
    public function __construct(private readonly HttpFactory $http) {}

    /**
     * @param  array<string, mixed>  $options
     * @return array{token: string, expires_at: string, connect_link_url: string}
     */
    public function createConnectToken(Authenticatable $user, array $options = []): array
    {
        $payload = array_filter([
            'external_user_id' => $this->externalUserId($user),
            'allowed_origins' => $this->allowedOrigins(),
            'success_redirect_uri' => $options['success_redirect_uri'] ?? null,
            'error_redirect_uri' => $options['error_redirect_uri'] ?? null,
            'webhook_uri' => $options['webhook_uri'] ?? null,
            'expires_in' => $this->tokenExpiresIn(),
            'scope' => config('pipedream.connect_token_scope'),
        ], fn (mixed $value): bool => $value !== null && $value !== []);

        $response = $this->http
            ->baseUrl($this->baseUrl())
            ->connectTimeout($this->connectTimeout())
            ->timeout($this->timeout())
            ->withToken($this->accessToken())
            ->withHeaders(['x-pd-environment' => $this->environment()])
            ->acceptJson()
            ->asJson()
            ->post("/v1/connect/{$this->projectId()}/tokens", $payload);

        if ($response->failed()) {
            throw new RuntimeException('Pipedream Connect token creation failed.');
        }

        $token = $response->json();
        $connectLinkUrl = $token['connect_link_url'] ?? null;

        if (! is_string($connectLinkUrl) || $connectLinkUrl === '') {
            throw new RuntimeException('Pipedream did not return a Connect Link URL.');
        }

        $token['connect_link_url'] = $this->withApp($connectLinkUrl, $options['app'] ?? null);

        return [
            'token' => (string) ($token['token'] ?? ''),
            'expires_at' => (string) ($token['expires_at'] ?? ''),
            'connect_link_url' => $token['connect_link_url'],
        ];
    }

    public function isConfigured(): bool
    {
        return filled(config('pipedream.project_id'))
            && filled(config('pipedream.client_id'))
            && filled(config('pipedream.client_secret'));
    }

    public function trustedConnectLink(string $connectLinkUrl): string
    {
        $parts = parse_url($connectLinkUrl);

        if (! is_array($parts)
            || ($parts['scheme'] ?? null) !== 'https'
            || ($parts['host'] ?? null) !== 'pipedream.com') {
            throw new RuntimeException('Pipedream returned an untrusted Connect Link URL.');
        }

        return $connectLinkUrl;
    }

    private function accessToken(): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Pipedream Connect is missing client credentials.');
        }

        $cacheKey = 'pipedream:oauth-token:'.sha1(implode('|', [
            $this->baseUrl(),
            $this->projectId(),
            $this->environment(),
            (string) config('pipedream.oauth_scope'),
            (string) config('pipedream.client_id'),
        ]));

        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $response = $this->http
            ->baseUrl($this->baseUrl())
            ->connectTimeout($this->connectTimeout())
            ->timeout($this->timeout())
            ->acceptJson()
            ->asJson()
            ->post('/v1/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('pipedream.client_id'),
                'client_secret' => config('pipedream.client_secret'),
                'scope' => config('pipedream.oauth_scope'),
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Pipedream OAuth token request failed.');
        }

        $accessToken = $response->json('access_token');

        if (! is_string($accessToken) || $accessToken === '') {
            throw new RuntimeException('Pipedream did not return an OAuth access token.');
        }

        $expiresIn = max((int) $response->json('expires_in', 3600) - 60, 60);
        Cache::put($cacheKey, $accessToken, now()->addSeconds($expiresIn));

        return $accessToken;
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('pipedream.base_url', 'https://api.pipedream.com'), '/');
    }

    private function projectId(): string
    {
        $projectId = (string) config('pipedream.project_id');

        if (! Str::startsWith($projectId, 'proj_')) {
            throw new RuntimeException('Pipedream project ID must start with proj_.');
        }

        return $projectId;
    }

    private function environment(): string
    {
        $environment = (string) config('pipedream.environment', 'production');

        if (! in_array($environment, ['development', 'production'], true)) {
            throw new RuntimeException('Pipedream environment must be development or production.');
        }

        return $environment;
    }

    private function tokenExpiresIn(): int
    {
        return min(max((int) config('pipedream.token_expires_in', 14400), 60), 14400);
    }

    private function connectTimeout(): int
    {
        return min(max((int) config('pipedream.connect_timeout', 5), 1), 30);
    }

    private function timeout(): int
    {
        return min(max((int) config('pipedream.timeout', 15), 1), 60);
    }

    /**
     * @return array<int, string>
     */
    private function allowedOrigins(): array
    {
        $configured = config('pipedream.allowed_origins', []);

        if (is_array($configured) && $configured !== []) {
            return array_values(array_filter(array_map('strval', $configured)));
        }

        $appUrl = (string) config('app.url');
        $parts = parse_url($appUrl);

        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return [];
        }

        $origin = "{$parts['scheme']}://{$parts['host']}";

        if (! empty($parts['port'])) {
            $origin .= ":{$parts['port']}";
        }

        return [$origin];
    }

    private function externalUserId(Authenticatable $user): string
    {
        $identifier = implode('|', [
            (string) config('pipedream.external_user_namespace'),
            $user::class,
            (string) $user->getAuthIdentifier(),
        ]);

        return 'xchange_'.hash_hmac('sha256', $identifier, (string) config('app.key'));
    }

    private function withApp(string $connectLinkUrl, mixed $app): string
    {
        if (! is_string($app) || trim($app) === '') {
            return $connectLinkUrl;
        }

        $separator = str_contains($connectLinkUrl, '?') ? '&' : '?';

        return $connectLinkUrl.$separator.http_build_query(['app' => trim($app)]);
    }
}
