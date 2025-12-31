<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StravaController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $clientId = config('services.strava.client_id');
        $redirectUrl = config('services.strava.redirect') ?: route('strava.callback');
        $scope = config('services.strava.scope', 'read');

        abort_unless($clientId, 500, 'Strava is not configured (missing STRAVA_CLIENT_ID).');

        $state = Str::random(40);
        $request->session()->put('strava_oauth_state', $state);

        $authorizationUrl = 'https://www.strava.com/oauth/authorize?'.http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUrl,
            'response_type' => 'code',
            'approval_prompt' => 'auto',
            'scope' => $scope,
            'state' => $state,
        ]);

        return redirect()->away($authorizationUrl);
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->string('error')->toString() === 'access_denied') {
            return to_route('profile.edit')->with('status', 'strava-access-denied');
        }

        $expectedState = $request->session()->pull('strava_oauth_state');
        if (! is_string($expectedState) || ! hash_equals($expectedState, (string) $request->input('state'))) {
            return to_route('profile.edit')->with('status', 'strava-invalid-state');
        }

        $clientId = config('services.strava.client_id');
        $clientSecret = config('services.strava.client_secret');

        abort_unless($clientId, 500, 'Strava is not configured (missing STRAVA_CLIENT_ID).');
        abort_unless($clientSecret, 500, 'Strava is not configured (missing STRAVA_CLIENT_SECRET).');

        $code = $request->string('code')->toString();
        if ($code === '') {
            return to_route('profile.edit')->with('status', 'strava-missing-code');
        }

        $tokenResponse = Http::asForm()->post('https://www.strava.com/oauth/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);

        if (! $tokenResponse->ok()) {
            return to_route('profile.edit')->with('status', 'strava-token-exchange-failed');
        }

        $payload = $tokenResponse->json();
        $athleteId = data_get($payload, 'athlete.id');
        $accessToken = data_get($payload, 'access_token');
        $avatarUrl = data_get($payload, 'athlete.profile');

        if (! $athleteId || ! $accessToken) {
            return to_route('profile.edit')->with('status', 'strava-token-response-invalid');
        }

        $expiresAt = data_get($payload, 'expires_at');
        $tokenExpiresAt = is_numeric($expiresAt) ? now()->setTimestamp((int) $expiresAt) : null;

        $request->user()->forceFill([
            'strava_id' => (int) $athleteId,
            'strava_access_token' => (string) $accessToken,
            'strava_refresh_token' => (string) data_get($payload, 'refresh_token', ''),
            'strava_token_expires_at' => $tokenExpiresAt,
            'strava_scope' => (string) $request->input('scope', ''),
            'strava_connected_at' => now(),
            'strava_avatar_url' => is_string($avatarUrl) && $avatarUrl !== '' ? $avatarUrl : null,
        ])->save();

        return to_route('profile.edit')->with('status', 'strava-connected');
    }

    public function avatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->strava_id || ! is_string($user->strava_avatar_url) || $user->strava_avatar_url === '') {
            return to_route('profile.edit')->with('status', 'strava-avatar-unavailable');
        }

        $user->forceFill([
            'avatar' => $user->strava_avatar_url,
        ])->save();

        return to_route('profile.edit')->with('status', 'strava-avatar-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->forceFill([
            'strava_id' => null,
            'strava_access_token' => null,
            'strava_refresh_token' => null,
            'strava_token_expires_at' => null,
            'strava_scope' => null,
            'strava_connected_at' => null,
            'strava_avatar_url' => null,
        ])->save();

        return to_route('profile.edit')->with('status', 'strava-disconnected');
    }
}
