<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\PasswordResetMail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use function Illuminate\Support\defer;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function sendPasswordResetNotification($token): void
    {
        defer(function () use ($token): void {
            try {
                $email = $this->getEmailForPasswordReset();
                $resetUrl = route('password.reset', [
                    'token' => $token,
                    'email' => $email,
                ]);

                Mail::mailer('brevo')->to($email)->send(new PasswordResetMail($resetUrl));
            } catch (\Throwable $exception) {
                $context = ['exception' => $exception::class];

                if ($exception instanceof RequestException) {
                    $context['http_status'] = $exception->response->status();
                }

                Log::error('Password reset email delivery failed.', $context);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
