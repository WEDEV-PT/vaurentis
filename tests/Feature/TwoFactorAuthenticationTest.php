<?php

namespace Tests\Feature;

use App\Filament\Auth\MultiFactor\ManagedAppAuthentication;
use App\Filament\Auth\MultiFactor\ManagedEmailAuthentication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_two_factor_authentication_is_enabled_only_when_required(): void
    {
        $user = User::factory()->create([
            'two_factor_required' => true,
            'two_factor_method' => 'email',
        ]);

        $this->assertTrue($user->fresh()->hasEmailAuthentication());
        $this->assertTrue(app(ManagedEmailAuthentication::class)->isEnabled($user->fresh()));

        $user->update(['two_factor_required' => false]);

        $this->assertNull($user->fresh()->two_factor_method);
        $this->assertFalse($user->fresh()->hasEmailAuthentication());
        $this->assertFalse(app(ManagedEmailAuthentication::class)->isEnabled($user->fresh()));
    }

    public function test_app_two_factor_authentication_requires_the_selected_method_and_a_secret(): void
    {
        $user = User::factory()->create([
            'two_factor_required' => true,
            'two_factor_method' => 'app',
        ]);
        $user->saveAppAuthenticationSecret('JBSWY3DPEHPK3PXP');

        $this->assertTrue(app(ManagedAppAuthentication::class)->isEnabled($user->fresh()));

        $user->update(['two_factor_method' => 'email']);

        $this->assertNull($user->fresh()->app_authentication_secret);
        $this->assertFalse(app(ManagedAppAuthentication::class)->isEnabled($user->fresh()));
    }

    public function test_outgoing_emails_are_recorded_in_the_email_log(): void
    {
        $message = (new Email)
            ->from('no-reply@vaurentis.test')
            ->to('user@example.test')
            ->subject('Two-factor authentication code')
            ->text('Your code is 123456.');

        Event::dispatch(new MessageSending($message));
        Event::dispatch(new MessageSending($message));

        $this->assertDatabaseHas('email_log', [
            'to' => 'user@example.test',
            'subject' => 'Two-factor authentication code',
        ]);
        $this->assertDatabaseCount('email_log', 1);
    }
}
