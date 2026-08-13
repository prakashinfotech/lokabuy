<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/api/v1/auth';
    private const STRONG_PASSWORD = 'Password1!abc';

    // =========================================================================
    // Register
    // =========================================================================

    public function test_register_creates_user_and_returns_token(): void
    {
        Notification::fake();

        $this->postJson(self::BASE . '/register', [
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])
            ->assertStatus(201)
            ->assertJson(['success' => true, 'error' => null])
            ->assertJsonStructure(['data' => ['user', 'token', 'email_verified', 'message']]);

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    public function test_register_sends_email_verification_notification(): void
    {
        Notification::fake();

        $this->postJson(self::BASE . '/register', [
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ]);

        Notification::assertSentTo(
            User::where('email', 'jane@example.com')->firstOrFail(),
            VerifyEmail::class
        );
    }

    public function test_register_returns_422_when_required_fields_missing(): void
    {
        $this->postJson(self::BASE . '/register', [])
            ->assertStatus(422)
            ->assertJson(['success' => false, 'error' => 'Validation failed']);
    }

    public function test_register_returns_422_for_duplicate_email(): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson(self::BASE . '/register', [
            'name'                  => 'Other User',
            'email'                 => 'taken@example.com',
            'password'              => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error', 'Validation failed');
    }

    public function test_register_returns_422_for_weak_password(): void
    {
        $this->postJson(self::BASE . '/register', [
            'name'                  => 'Test',
            'email'                 => 'test@example.com',
            'password'              => 'weakpass',
            'password_confirmation' => 'weakpass',
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_register_strips_html_tags_from_name(): void
    {
        Notification::fake();

        $this->postJson(self::BASE . '/register', [
            'name'                  => '<b>Alice</b><script>bad()</script>',
            'email'                 => 'alice@example.com',
            'password'              => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'alice@example.com',
            'name'  => 'Alicebad()',
        ]);
    }

    // =========================================================================
    // Login
    // =========================================================================

    public function test_login_returns_tokens_for_valid_credentials(): void
    {
        $user = User::factory()->create();

        $this->postJson(self::BASE . '/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJson(['success' => true, 'error' => null])
            ->assertJsonStructure(['data' => ['user', 'token', 'refresh_token']]);
    }

    public function test_login_returns_401_for_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->postJson(self::BASE . '/login', [
            'email'    => $user->email,
            'password' => 'wrongpassword',
        ])
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_login_returns_401_for_unknown_email(): void
    {
        $this->postJson(self::BASE . '/login', [
            'email'    => 'nobody@example.com',
            'password' => 'password',
        ])
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_login_returns_403_for_inactive_account(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->postJson(self::BASE . '/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])
            ->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_login_rate_limits_after_three_consecutive_failures(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 3; $i++) {
            $this->postJson(self::BASE . '/login', [
                'email'    => $user->email,
                'password' => 'wrongpassword',
            ]);
        }

        $this->postJson(self::BASE . '/login', [
            'email'    => $user->email,
            'password' => 'wrongpassword',
        ])
            ->assertStatus(429)
            ->assertJsonPath('success', false);

        RateLimiter::clear('login:' . strtolower($user->email) . '|127.0.0.1');
    }

    // =========================================================================
    // Logout
    // =========================================================================

    public function test_logout_invalidates_current_token(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('access-token')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_logout_returns_401_without_token(): void
    {
        $this->postJson('/api/v1/auth/logout')
            ->assertStatus(401);
    }

    // =========================================================================
    // Refresh
    // =========================================================================

    public function test_refresh_issues_new_access_token(): void
    {
        $user         = User::factory()->create();
        $refreshToken = $user->createToken('refresh-token', ['refresh'])->plainTextToken;

        $this->withToken($refreshToken)
            ->postJson(self::BASE . '/refresh')
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_refresh_returns_401_with_invalid_token(): void
    {
        $this->withToken('not-a-valid-token')
            ->postJson(self::BASE . '/refresh')
            ->assertStatus(401);
    }

    // =========================================================================
    // Forgot Password
    // =========================================================================

    public function test_forgot_password_returns_200_for_registered_email(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->postJson(self::BASE . '/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJson(['success' => true, 'error' => null]);
    }

    public function test_forgot_password_returns_422_for_unknown_email(): void
    {
        $this->postJson(self::BASE . '/forgot-password', ['email' => 'ghost@example.com'])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error', 'Validation failed');
    }

    // =========================================================================
    // Reset Password
    // =========================================================================

    public function test_reset_password_with_valid_token_updates_password(): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $this->postJson(self::BASE . '/reset-password', [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])
            ->assertOk()
            ->assertJson(['success' => true, 'error' => null]);

        $this->assertTrue(Hash::check(self::STRONG_PASSWORD, $user->fresh()->password));
    }

    public function test_reset_password_invalidates_existing_tokens_on_success(): void
    {
        $user = User::factory()->create();
        $user->createToken('access-token');
        $token = Password::createToken($user);

        $this->postJson(self::BASE . '/reset-password', [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])->assertOk();

        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_reset_password_returns_422_for_invalid_token(): void
    {
        $user = User::factory()->create();

        $this->postJson(self::BASE . '/reset-password', [
            'email'                 => $user->email,
            'token'                 => 'bad-token',
            'password'              => self::STRONG_PASSWORD,
            'password_confirmation' => self::STRONG_PASSWORD,
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_reset_password_returns_422_for_mismatched_confirmation(): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $this->postJson(self::BASE . '/reset-password', [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => self::STRONG_PASSWORD,
            'password_confirmation' => 'DifferentPassword1!',
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}
