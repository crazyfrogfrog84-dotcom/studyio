<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetFlowTest extends TestCase
{
    use RefreshDatabase;

    private const GENERIC_STATUS = 'If an account with that email exists, you will receive a password reset link shortly.';

    public function test_password_recovery_pages_are_linked_and_render(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('password.request'));

        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Forgot your password?')
            ->assertSee(route('login'));

        $this->get(route('password.reset', ['token' => 'sample-token', 'email' => 'maya@example.test']))
            ->assertOk()
            ->assertSee('Create a new password')
            ->assertSee('maya@example.test');
    }

    public function test_existing_account_gets_reset_mail_and_generic_response(): void
    {
        $user = User::factory()->create(['email' => 'maya@example.test']);
        Mail::fake();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', self::GENERIC_STATUS);

        Mail::assertSent(PasswordResetMail::class, function (PasswordResetMail $mail) use ($user): bool {
            return $mail->hasTo($user->email)
                && str_contains($mail->resetUrl, '/reset-password/')
                && str_contains($mail->resetUrl, 'email=maya%40example.test')
                && str_contains($mail->render(), '60 minutes');
        });
    }

    public function test_unknown_email_receives_the_same_response_without_mail(): void
    {
        Mail::fake();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'missing@example.test'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', self::GENERIC_STATUS);

        Mail::assertNothingOutgoing();
    }

    public function test_valid_token_resets_the_password_and_cannot_be_reused(): void
    {
        $user = User::factory()->create(['password' => 'OldPortfolioPassword123!']);
        $token = Password::createToken($user);
        Mail::fake();

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('Reset password');

        $payload = [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPortfolioPassword456!',
            'password_confirmation' => 'NewPortfolioPassword456!',
        ];

        $this->post(route('password.update'), $payload)
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Your password has been reset. You can now log in.');

        $user->refresh();
        $this->assertTrue(Hash::check('NewPortfolioPassword456!', $user->password));
        Mail::assertNothingOutgoing();

        $this->from(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->post(route('password.update'), $payload)
            ->assertSessionHasErrors(['email' => 'This password reset token is invalid.']);
    }

    public function test_expired_reset_link_is_rejected_after_sixty_minutes(): void
    {
        $user = User::factory()->create(['password' => 'OldPortfolioPassword123!']);
        $token = Password::createToken($user);
        Mail::fake();

        try {
            $this->travel(61)->minutes();

            $this->from(route('password.reset', ['token' => $token, 'email' => $user->email]))
                ->post(route('password.update'), [
                    'token' => $token,
                    'email' => $user->email,
                    'password' => 'NewPortfolioPassword456!',
                    'password_confirmation' => 'NewPortfolioPassword456!',
                ])
                ->assertSessionHasErrors(['email' => 'This password reset token is invalid.']);

            $this->assertTrue(Hash::check('OldPortfolioPassword123!', $user->fresh()->password));
            Mail::assertNothingOutgoing();
        } finally {
            $this->travelBack();
        }
    }

    public function test_reset_link_requests_are_throttled_after_five_attempts_for_the_same_email_and_ip(): void
    {
        $user = User::factory()->create(['email' => 'maya@example.test']);
        Mail::fake();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from(route('password.request'))
                ->post(route('password.email'), ['email' => $user->email])
                ->assertRedirect(route('password.request'))
                ->assertSessionHas('status', self::GENERIC_STATUS);
        }

        $this->post(route('password.email'), ['email' => $user->email])->assertTooManyRequests();

        Mail::assertSentTimes(PasswordResetMail::class, 1);
    }

    public function test_brevo_transport_sends_the_email_using_its_https_api(): void
    {
        Http::preventStrayRequests();
        Http::fake(['https://api.brevo.com/v3/smtp/email' => Http::response()]);
        config([
            'mail.mailers.brevo.key' => 'test-api-key',
            'mail.mailers.brevo.endpoint' => 'https://api.brevo.com/v3/smtp/email',
            'mail.from.address' => 'noreply@example.test',
            'mail.from.name' => 'StuDyIo',
        ]);

        Mail::mailer('brevo')->to('maya@example.test')->send(new PasswordResetMail('https://studyio.example.test/reset-password/test-token'));

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.brevo.com/v3/smtp/email'
                && $request->hasHeader('api-key', 'test-api-key')
                && $request['sender']['email'] === 'noreply@example.test'
                && $request['to'][0]['email'] === 'maya@example.test'
                && isset($request['htmlContent'], $request['textContent']);
        });
    }
}
