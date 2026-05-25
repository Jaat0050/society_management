<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use App\Models\SocietyNotification;
use App\Models\User;
use App\Support\MobileFeatureRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'device_token' => 'nullable|string',
            'platform' => 'nullable|string',
        ]);

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($field, $request->login)->where('is_active', true)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['login' => ['Invalid credentials.']]);
        }

        if (! MobileFeatureRegistry::isMobileRole($user->role)) {
            return $this->fail('This account uses the web panel. Mobile app access is not enabled for this role.', 403);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        if ($request->device_token) {
            $user->tokens()->latest()->first()?->update(['name' => 'mobile-'.$request->platform]);
        }

        return $this->ok([
            'token' => $token,
            'user' => $this->userPayload($user),
        ], 'Login successful');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $user = User::where('phone', $request->phone)->where('is_active', true)->first();

        if (! $user) {
            return $this->fail('Phone not registered.', 404);
        }

        return $this->ok([
            'otp_sent' => true,
            'dev_otp' => '123456',
            'message' => 'OTP simulation — use 123456 in development',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        if ($request->otp !== '123456') {
            return $this->fail('Invalid OTP.', 422);
        }

        $user = User::where('phone', $request->phone)->where('is_active', true)->firstOrFail();

        if (! MobileFeatureRegistry::isMobileRole($user->role)) {
            return $this->fail('Mobile access not allowed for this role.', 403);
        }

        $token = $user->createToken('mobile-otp')->plainTextToken;

        return $this->ok([
            'token' => $token,
            'user' => $this->userPayload($user),
        ], 'OTP verified');
    }

    public function me(Request $request)
    {
        return $this->ok($this->userPayload($request->user()->load(['society', 'member.flat', 'member.family', 'member.vehicles'])));
    }

    public function features(Request $request)
    {
        $user = $request->user();

        return $this->ok([
            'role' => $user->role,
            'features' => MobileFeatureRegistry::featuresFor($user->role),
            'payment_gateway_enabled' => config('mobile_features.payment_gateway_enabled', false),
            'chat_poll_seconds' => (int) config('mobile_features.chat_poll_seconds', 3),
            'menus' => MobileFeatureRegistry::menusFor($user->role),
            'broadcast' => $this->broadcastConfig(),
        ]);
    }

    public function appConfig(Request $request)
    {
        return $this->ok([
            'payment_gateway_enabled' => config('mobile_features.payment_gateway_enabled', false),
            'chat_poll_seconds' => (int) config('mobile_features.chat_poll_seconds', 3),
            'broadcast' => $this->broadcastConfig(),
            'society_name' => $request->user()->society?->name,
        ]);
    }

    protected function broadcastConfig(): array
    {
        return [
            'driver' => config('broadcasting.default'),
            'pusher_key' => config('broadcasting.connections.pusher.key'),
            'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster'),
            'channel_prefix' => 'society.chat',
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->ok(null, 'Logged out');
    }

    public function notifications(Request $request)
    {
        $list = SocietyNotification::where('society_id', $request->user()->society_id)
            ->latest()
            ->take(40)
            ->get(['id', 'title', 'body', 'channel', 'status', 'created_at']);

        return $this->ok($list);
    }

    public function updatePreferences(Request $request)
    {
        $data = $request->validate([
            'locale' => 'nullable|string|max:10',
            'dark_mode' => 'nullable|boolean',
        ]);

        \DB::table('app_preferences')->updateOrInsert(
            ['user_id' => $request->user()->id],
            array_merge($data, ['updated_at' => now(), 'created_at' => now()])
        );

        return $this->ok($data, 'Preferences saved');
    }

    protected function userPayload(User $user): array
    {
        $user->loadMissing(['society', 'member.flat', 'member.family', 'member.vehicles']);

        $prefs = \DB::table('app_preferences')->where('user_id', $user->id)->first();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'role_label' => \App\Enums\UserRole::tryFrom($user->role)?->label() ?? $user->role,
            'society' => $user->society ? [
                'id' => $user->society->id,
                'name' => $user->society->name,
                'city' => $user->society->city,
            ] : null,
            'member' => $user->member ? [
                'id' => $user->member->id,
                'member_type' => $user->member->member_type,
                'flat' => $user->member->flat?->label,
                'family' => $user->member->family,
                'vehicles' => $user->member->vehicles,
            ] : null,
            'preferences' => [
                'locale' => $prefs->locale ?? 'en',
                'dark_mode' => (bool) ($prefs->dark_mode ?? false),
            ],
        ];
    }
}
